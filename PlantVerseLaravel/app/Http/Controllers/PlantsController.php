<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\CareTask;
use App\Models\UserDailyActivity;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * PlantsController
 * 
 * Handles all plant-related operations including creation, care logging, and plant identification.
 * Refactored for better maintainability, security, and accurate time-based logic.
 */
class PlantsController extends Controller
{
    /**
     * PVT reward amount for completing a care task.
     * Extracted as a class constant to enable easy adjustments and avoid magic numbers.
     */
    private const PVT_CARE_REWARD = 10;
    private const CARE_CONSISTENCY_LOG_REWARD = 10;

    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Record plant visit for attendance streak tracking
        UserDailyActivity::recordPlantVisit($user->id);

        $careFilter = $request->query('care_filter', $request->query('filter')); // expected: 'water', 'sunlight', 'fertilize'
        $categoryFilter = $request->query('category'); // expected: 'normal', 'vegetable', 'fruit'
        $careFilter = in_array($careFilter, ['water', 'sunlight', 'fertilize']) ? $careFilter : null;
        $categoryFilter = in_array($categoryFilter, ['normal', 'vegetable', 'fruit']) ? $categoryFilter : null;

        $duePlantIds = null;
        if ($careFilter) {
            $taskTypes = match ($careFilter) {
                'water' => ['Water', 'water', 'watering'],
                'sunlight' => ['Sunlight', 'sunlight'],
                'fertilize' => ['Fertilize', 'fertilize', 'fertilizer'],
            };
            $nowEnd = now()->endOfDay();

            $duePlantIds = CareTask::query()
                ->select(['plant_id', 'last_completed', 'frequency_days'])
                ->whereIn('type', $taskTypes)
                ->whereHas('plant', function ($query) use ($user, $categoryFilter) {
                    $query->where('user_id', $user->id)
                        ->when($categoryFilter, function ($categoryQuery) use ($categoryFilter) {
                            $categoryQuery->where('category', $categoryFilter);
                        });
                })
                ->get()
                ->filter(function ($task) use ($nowEnd) {
                    return $task->last_completed->copy()->addDays($task->frequency_days)->lte($nowEnd);
                })
                ->pluck('plant_id')
                ->unique()
                ->values();
        }

        $plantsQuery = Plant::query()
            ->where('user_id', $user->id)
            ->when($categoryFilter, function ($query) use ($categoryFilter) {
                $query->where('category', $categoryFilter);
            })
            ->when($duePlantIds !== null, function ($query) use ($duePlantIds) {
                $query->whereIn('id', $duePlantIds);
            })
            ->orderByDesc('is_favorite')
            ->orderBy('name');

        $plants = $plantsQuery->get();

        return view('pages.plants.index', [
            'plants' => $plants,
            'user' => $user,
            'activeFilter' => $careFilter ?? null,
            'activeCategory' => $categoryFilter ?? null,
        ]);
    }

    public function show(Request $request, $id)
    {
        $plant = Plant::with(['careTasks', 'journals' => function ($query) {
            $query->latest();
        }])->findOrFail($id);

        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($plant->user_id !== $user->id) {
            return abort(403, 'Unauthorized');
        }

        UserDailyActivity::recordPlantVisit($user->id);

        return view('pages.plants.show', [
            'plant' => $plant,
            'user' => $user,
        ]);
    }

    /**
     * Show edit form for a plant
     * 
     * Authorization: User must own the plant ($plant->user_id === auth()->id())
     */
    public function edit(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Verify ownership
        if ($plant->user_id !== $user->id) {
            return abort(403, 'Unauthorized');
        }

        return view('pages.plants.edit', [
            'plant' => $plant,
        ]);
    }

    /**
     * Update plant information
     * 
     * Allows users to update plant name, species, care recommendations, and photo.
     * Photo is optional - if not provided, existing photo is kept.
     * Authorization: User must own the plant
     */
    public function update(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Verify ownership
        if ($plant->user_id !== $user->id) {
            return abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'category' => 'nullable|in:normal,vegetable,fruit',
            'care_recommendations' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:5120',
        ]);

        $photoUrl = $plant->photo_url;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoUrl = $request->file('photo')->store('plants', 'public');
        }

        $plant->update([
            'name' => $validated['name'],
            'species' => $validated['species'],
            'category' => $validated['category'] ?? $plant->category,
            'care_recommendations' => $validated['care_recommendations'] ?? $plant->care_recommendations,
            'photo_url' => $photoUrl,
        ]);

        return redirect()->route('plants.show', $plant)->with('success', 'Plant updated successfully!');
    }

    /**
     * Delete/destroy a plant and its associated care tasks
     * 
     * When a plant is deleted:
     * - All associated CareTask records are deleted (cascade delete via relationship)
     * - Plant record is removed from database
     * - User is redirected to plants index
     * 
     * Authorization: User must own the plant
     */
    public function destroy(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Verify ownership
        if ($plant->user_id !== $user->id) {
            return abort(403, 'Unauthorized');
        }

        // Delete associated care tasks (cascade delete)
        $plant->careTasks()->delete();

        // Delete the plant
        $plant->delete();

        return redirect()->route('plants.index')->with('success', 'Plant deleted successfully!');
    }

    public function bulkDestroy(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $validated = $request->validate([
            'plant_ids' => 'required|array|min:1',
            'plant_ids.*' => 'integer',
        ]);

        $plants = Plant::where('user_id', $user->id)
            ->whereIn('id', $validated['plant_ids'])
            ->get();

        if ($plants->isEmpty()) {
            return redirect()->route('plants.index')->with('error', 'No plants were selected for deletion.');
        }

        foreach ($plants as $plant) {
            $plant->careTasks()->delete();
            $plant->delete();
        }

        $count = $plants->count();
        $message = $count === 1
            ? '1 plant deleted successfully.'
            : "{$count} plants deleted successfully.";

        return redirect()->route('plants.index')->with('success', $message);
    }

    public function create()
    {
        return view('pages.plants.create');
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string',
            'species' => 'required|string',
            'category' => 'nullable|in:normal,vegetable,fruit',
            'photo' => 'nullable|image|max:5120',
            'care_recommendations' => 'nullable|string',
            'water_frequency' => 'nullable|integer|min:1|max:365',
            'sunlight_frequency' => 'nullable|integer|min:1|max:365',
            'fertilize_frequency' => 'nullable|integer|min:1|max:365',
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photoUrl = $request->file('photo')->store('plants', 'public');
        }

        $plant = Plant::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'species' => $validated['species'],
            'category' => $validated['category'] ?? 'normal',
            'photo_url' => $photoUrl,
            'care_consistency' => 0,
            'is_neglected' => false,
        ]);

        // Create care tasks with custom frequencies if provided
        $customFrequencies = [
            'water_frequency' => $validated['water_frequency'] ?? null,
            'sunlight_frequency' => $validated['sunlight_frequency'] ?? null,
            'fertilize_frequency' => $validated['fertilize_frequency'] ?? null,
        ];
        $this->createDefaultCareTasks($plant, $customFrequencies);

        return redirect()->route('plants.show', $plant)->with('success', 'Plant added successfully!');
    }

    public function logCare(Request $request, $plantId, $taskType)
    {
        $plant = Plant::findOrFail($plantId);
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($plant->user_id !== $user->id) {
            return abort(403, 'Unauthorized');
        }

        $task = $plant->careTasks()->where('type', $taskType)->firstOrFail();

        /**
         * FLEXIBLE CARE WINDOWS (REFACTORED)
         * 
         * Instead of a strict cooldown, we now implement a grace period.
         * Users can log care tasks up to 12 hours BEFORE the exact due time.
         * 
         * Logic:
         * - Calculate grace period start: last_completed + frequency_days - 12 hours
         * - Allow logging if current time >= grace period start
         * - This provides flexibility while maintaining task integrity
         * 
         * Example:
         * - Task last completed: 2026-04-01 10:00 AM
         * - Frequency: 7 days
         * - Due time (exact): 2026-04-08 10:00 AM
         * - Grace period starts: 2026-04-08 10:00 AM - 12 hours = 2026-04-07 10:00 PM
         * - Current time: 2026-04-07 11:00 PM
         * - Result: CAN LOG NOW (within grace period)
         */
        // Calculate when task is due
        $dueDate = $task->last_completed->copy()->addDays($task->frequency_days);
        $gracePeriodStart = $dueDate->copy()->subHours(12);

        // Allow if we're in the grace period or past due
        if (now()->lessThan($gracePeriodStart)) {
            $daysRemaining = (int) ceil(now()->diffInDays($gracePeriodStart));
            return redirect()->back()->with('error', "You can do this in $daysRemaining day(s). Come back later!");
        }

        $task->update(['last_completed' => now()]);
        $user->increment('pvt_balance', self::PVT_CARE_REWARD);

        // Record care log for attendance tracking
        UserDailyActivity::recordCareLog($user->id);

        // === CARE CONSISTENCY TRACKING ===
        // Every valid care log should visibly recover consistency. The daily
        // recalculation command still handles decay from missed care.
        $consistencyReward = self::CARE_CONSISTENCY_LOG_REWARD;
        $plant->care_consistency = min(100, $plant->care_consistency + $consistencyReward);

        // === STREAK TRACKING ===
        // Update plant's care streak.
        $plant->last_care_completed_at = now();
        if ($plant->is_neglected) {
            $plant->is_neglected = false;
            $plant->care_streak = 1;
            $plant->streak_started_at = now();
        } else {
            if (is_null($plant->streak_started_at)) {
                $plant->streak_started_at = now();
                $plant->care_streak = 1;
            } else {
                $plant->care_streak += 1;
            }
        }
        $plant->save();

        // Build success message with current streak info
        $streakMessage = $user->daily_streak > 0
            ? " | {$user->daily_streak}-day streak!"
            : " | Start your attendance streak by visiting daily!";

        return redirect()->back()->with('success', "{$taskType} logged successfully! +" . self::PVT_CARE_REWARD . " PVT, +{$consistencyReward}% care consistency{$streakMessage}");
    }

    public function identifyPlant(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($plant->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        $file = $request->file('photo');

        try {
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            $dataUri = 'data:' . $file->getMimeType() . ';base64,' . $imageData;

            $result = $this->geminiService->identifyPlant($dataUri);

            if ($result['success'] ?? false) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'] ?? [],
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Failed to identify plant',
            ], 400);
        } catch (\Exception $e) {
            /**
             * IMPROVED ERROR HANDLING
             * 
             * Log the full exception details including message, code, and stack trace
             * for easier debugging and monitoring in production environments.
             * This helps with troubleshooting without exposing sensitive info to the client.
             */
            Log::error('Plant identification failed', [
                'exception_message' => $e->getMessage(),
                'exception_code' => $e->getCode(),
                'exception_trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'timestamp' => now()->toIso8601String(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while identifying the plant. Please try again.',
            ], 500);
        }
    }

    /**
     * Store a new journal entry for a plant
     * 
     * Allows users to document their plant's progress with optional photos and notes.
     * Creates a timestamped record of plant development journey.
     * 
     * Authorization: User must own the plant
     * 
     * @param Request $request The HTTP request containing photo and note
     * @param int $plantId The ID of the plant to add journal entry for
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeJournal(Request $request, $plantId)
    {
        $plant = Plant::findOrFail($plantId);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Verify ownership
        if ($plant->user_id !== $user->id) {
            return abort(403, 'Unauthorized');
        }

        // Validate request
        $validated = $request->validate([
            'photo' => 'nullable|image|max:5120',
            'note' => 'nullable|string|max:2000',
        ]);

        // Handle photo upload if provided
        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photoUrl = $request->file('photo')->store('journals', 'public');
        }

        // Create journal entry
        $plant->journals()->create([
            'photo_url' => $photoUrl,
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('plants.show', $plant)->with('success', 'Journal entry added successfully!');
    }

    /**
     * Shows the care schedule customization form for a plant
     * 
     * Allows users to customize the frequency of each care task (Water, Sunlight, Fertilize).
     * Displays a reminder to research the plant's specific care requirements before customizing.
     * 
     * Authorization: User must own the plant
     * 
     * @param Request $request The HTTP request
     * @param int $id The plant ID
     * @return \Illuminate\View\View
     */
    public function editCareSchedule(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Verify ownership
        if ($plant->user_id !== $user->id) {
            return abort(403, 'Unauthorized');
        }

        // Get all care tasks for this plant
        $careTasks = $plant->careTasks()->get();

        return view('pages.plants.edit-care-schedule', [
            'plant' => $plant,
            'careTasks' => $careTasks,
            'user' => $user,
        ]);
    }

    /**
     * Updates the care schedule frequencies for a plant's tasks
     * 
     * Allows users to customize how often each care task needs to be performed.
     * Validates that frequencies are positive integers.
     * Updates only the frequency_days column, preserving last_completed timestamps.
     * 
     * Authorization: User must own the plant
     * 
     * @param Request $request The HTTP request containing updated frequencies
     * @param int $id The plant ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCareSchedule(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Verify ownership
        if ($plant->user_id !== $user->id) {
            return abort(403, 'Unauthorized');
        }

        /**
         * CUSTOM CARE SCHEDULE VALIDATION
         * 
         * Validate that each care task type has a valid frequency (1-365 days).
         * Uses dynamic validation to accept: water_frequency, sunlight_frequency, fertilize_frequency
         * 
         * Example request body:
         * - water_frequency: 7 days
         * - sunlight_frequency: 1 day
         * - fertilize_frequency: 30 days
         */
        $validated = $request->validate([
            'water_frequency' => 'required|integer|min:1|max:365',
            'sunlight_frequency' => 'required|integer|min:1|max:365',
            'fertilize_frequency' => 'required|integer|min:1|max:365',
        ]);

        /**
         * UPDATE EACH CARE TASK
         * 
         * Map the validated frequencies to their corresponding care task types.
         * Preserve last_completed timestamp to avoid resetting task schedules.
         * Only update frequency_days to reflect new custom schedule.
         */
        $frequencyMap = [
            'Water' => $validated['water_frequency'],
            'Sunlight' => $validated['sunlight_frequency'],
            'Fertilize' => $validated['fertilize_frequency'],
        ];

        foreach ($frequencyMap as $taskType => $frequency) {
            $plant->careTasks()
                ->where('type', $taskType)
                ->update(['frequency_days' => $frequency]);
        }

        Log::info('Plant care schedule updated', [
            'plant_id' => $plant->id,
            'plant_name' => $plant->name,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'frequencies' => $validated,
            'timestamp' => now()->toIso8601String(),
        ]);

        return redirect()->route('plants.show', $plant)
            ->with('success', 'Care schedule updated successfully! Your custom frequencies are now active.');
    }

    /**
     * Creates default care tasks for a newly added plant.
     * 
     * Extracted into a private method to:
     * - Reduce controller bloat and improve readability
     * - Enable easy reuse and testing
     * - Keep related logic together
     * - Make the store() method more focused
     * 
     * @param Plant $plant The plant instance to create tasks for
     */
    private function createDefaultCareTasks(Plant $plant, array $customFrequencies = []): void
    {
        $defaultTasks = [
            ['type' => 'Water', 'frequency_days' => 7, 'customKey' => 'water_frequency'],
            ['type' => 'Sunlight', 'frequency_days' => 1, 'customKey' => 'sunlight_frequency'],
            ['type' => 'Fertilize', 'frequency_days' => 30, 'customKey' => 'fertilize_frequency'],
        ];

        foreach ($defaultTasks as $task) {
            // Use custom frequency if provided, otherwise use default
            $frequency = $customFrequencies[$task['customKey']] ?? $task['frequency_days'];

            $plant->careTasks()->create([
                'type' => $task['type'],
                'frequency_days' => $frequency,
                'last_completed' => now()->subDays($frequency),
            ]);
        }
    }

    /**
     * Toggle favorite status for a plant
     * 
     * Allows users to mark a plant as favorite (pinned to top).
     * Returns JSON response for AJAX requests.
     * 
     * Authorization: User must own the plant
     * 
     * @param Request $request The HTTP request
     * @param int $id The plant ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFavorite(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Verify ownership
        if ($plant->user_id !== $user->id) {
            return abort(403, 'Unauthorized');
        }

        $plant->is_favorite = !$plant->is_favorite;
        $plant->save();

        return response()->json([
            'success' => true,
            'is_favorite' => $plant->is_favorite,
            'message' => $plant->is_favorite ? 'Added to favorites!' : 'Removed from favorites!',
        ]);
    }
}
