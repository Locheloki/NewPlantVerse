<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plant;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlantsController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function index()
    {
        $user = User::firstOrFail();
        $plants = Plant::where('user_id', $user->id)->get();

        return view('pages.plants.index', [
            'plants' => $plants,
            'user' => $user,
        ]);
    }

    public function show($id)
    {
        $plant = Plant::findOrFail($id);
        $user = User::firstOrFail();

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
        $user = User::firstOrFail();

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

        // Create default care tasks
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

        return redirect()->route('plants.show', $plant)->with('success', 'Plant added successfully!');
    }

    public function logCare($plantId, $taskType)
    {
        $plant = Plant::findOrFail($plantId);
        $task = $plant->careTasks()->where('type', $taskType)->firstOrFail();

        // Check if enough days have passed
        $daysSinceCompleted = $task->last_completed->diffInDays(now());
        if ($daysSinceCompleted < $task->frequency_days) {
            $daysRemaining = $task->frequency_days - $daysSinceCompleted;
            return redirect()->back()->with('error', "You can do this in $daysRemaining day(s). Come back later!");
        }

        $task->update(['last_completed' => now()]);

        // Increase PVT balance
        $user = User::firstOrFail();
        $user->increment('pvt_balance', 10);

        return redirect()->back()->with('success', "{$taskType} logged successfully! +10 PVT");
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
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
