<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $plants = Plant::where('user_id', $user->id)->get();

        return view('pages.plants.index', [
            'plants' => $plants,
            'user' => $user,
        ]);
    }

    public function show(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = $request->user();

        return view('pages.plants.show', [
            'plant' => $plant,
            'user' => $user,
        ]);
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
            'photo' => 'nullable|image|max:5120',
            'care_recommendations' => 'nullable|string',
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photoUrl = $request->file('photo')->store('plants', 'public');
        }

        $plant = Plant::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'species' => $validated['species'],
            'photo_url' => $photoUrl,
            'care_consistency' => 0,
            'is_neglected' => false,
        ]);

        // Create default care tasks using dedicated private method
        $this->createDefaultCareTasks($plant);

        return redirect()->route('plants.show', $plant)->with('success', 'Plant added successfully!');
    }

    public function logCare(Request $request, $plantId, $taskType)
    {
        $plant = Plant::findOrFail($plantId);
        $task = $plant->careTasks()->where('type', $taskType)->firstOrFail();

        /**
         * IMPROVED TIME LOGIC
         * 
         * Previous approach using diffInDays() could lead to rounding errors.
         * New approach: Calculate the exact next available time and check if it's in the past.
         * This ensures accurate cooldown duration without day-boundary issues.
         * 
         * Example:
         * - Task last completed: 2026-04-01 10:00 AM
         * - Frequency: 7 days
         * - Next available: 2026-04-08 10:00 AM
         * - Current time: 2026-04-07 11:00 PM
         * - Result: NOT YET AVAILABLE (even though diffInDays would say it's available)
         */
        $nextAvailableTime = $task->last_completed->addDays($task->frequency_days);

        if ($nextAvailableTime->isFuture()) {
            $hoursRemaining = $nextAvailableTime->diffInHours(now());
            $daysRemaining = (int) ceil($hoursRemaining / 24);

            return redirect()->back()->with('error', "You can do this in $daysRemaining day(s). Come back later!");
        }

        $task->update(['last_completed' => now()]);

        // Increase PVT balance using class constant
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->increment('pvt_balance', self::PVT_CARE_REWARD);

        return redirect()->back()->with('success', "{$taskType} logged successfully! +" . self::PVT_CARE_REWARD . " PVT");
    }

    public function identifyPlant(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        try {
            $file = $request->file('photo');
            $imageData = base64_encode(file_get_contents($file));
            $dataUri = 'data:image/jpeg;base64,' . $imageData;

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
    private function createDefaultCareTasks(Plant $plant): void
    {
        $defaultTasks = [
            ['type' => 'Water', 'frequency_days' => 7],
            ['type' => 'Sunlight', 'frequency_days' => 1],
            ['type' => 'Fertilize', 'frequency_days' => 30],
        ];

        foreach ($defaultTasks as $task) {
            $plant->careTasks()->create([
                'type' => $task['type'],
                'frequency_days' => $task['frequency_days'],
                'last_completed' => now(),
            ]);
        }
    }
}
