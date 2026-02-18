<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plant;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // For now, using the first user (in production, use Auth::user())
        $user = User::firstOrFail();

        $plants = Plant::where('user_id', $user->id)->get();

        // Calculate upcoming care tasks
        $upcomingTasks = [];
        foreach ($plants as $plant) {
            foreach ($plant->careTasks as $task) {
                $lastCompleted = new Carbon($task->last_completed);
                $nextDueDate = $lastCompleted->addDays($task->frequency_days);
                $isOverdue = $nextDueDate->isPast();

                $upcomingTasks[] = [
                    'plant' => $plant,
                    'task' => $task,
                    'nextDueDate' => $nextDueDate,
                    'isOverdue' => $isOverdue,
                    'daysUntilDue' => now()->diffInDays($nextDueDate, false),
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
            'upcomingTasks' => array_slice($upcomingTasks, 0, 5), // Top 5 upcoming
        ]);
    }
}
