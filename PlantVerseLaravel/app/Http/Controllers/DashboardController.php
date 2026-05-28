<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\UserDailyActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Record login for attendance streak tracking
        UserDailyActivity::recordLogin($user->id);

        $plants = Plant::with('careTasks')
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get();
        $averageCareConsistency = $plants->isEmpty()
            ? 0
            : (int) round($plants->avg('care_consistency'));

        // Get favorite plants
        $favoritePlants = Plant::where('user_id', $user->id)
            ->where('is_favorite', true)
            ->orderBy('name')
            ->limit(8)
            ->get();

        // Calculate upcoming care tasks and today's counts
        $upcomingTasks = [];
        $todayCounts = ['water' => 0, 'sunlight' => 0, 'fertilize' => 0];
        $nowEnd = now()->endOfDay();

        foreach ($plants as $plant) {
            foreach ($plant->careTasks as $task) {
                $lastCompleted = Carbon::parse($task->last_completed);
                $nextDueDate = (clone $lastCompleted)->addDays($task->frequency_days);
                $isOverdue = $nextDueDate->isPast();

                // Count if due today or already past (needs attention)
                if ($nextDueDate->lte($nowEnd)) {
                    $type = strtolower($task->type);
                    if ($type === 'water' || $type === 'watering') {
                        $todayCounts['water']++;
                    } elseif ($type === 'sunlight') {
                        $todayCounts['sunlight']++;
                    } elseif ($type === 'fertilize' || $type === 'fertilizer') {
                        $todayCounts['fertilize']++;
                    }
                }

                $upcomingTasks[] = [
                    'plant' => $plant,
                    'task' => $task,
                    'nextDueDate' => $nextDueDate,
                    'isOverdue' => $isOverdue,
                    'daysUntilDue' => (int) now()->diffInDays($nextDueDate),
                ];
            }
        }

        // Sort by due date
        usort($upcomingTasks, function ($a, $b) {
            return $a['nextDueDate']->timestamp - $b['nextDueDate']->timestamp;
        });

        return view('pages.dashboard', [
            'user' => $user,
            'plants' => $plants,
            'favoritePlants' => $favoritePlants,
            'upcomingTasks' => array_slice($upcomingTasks, 0, 5), // Top 5 upcoming
            'todayCounts' => $todayCounts,
            'averageCareConsistency' => $averageCareConsistency,
        ]);
    }
}
