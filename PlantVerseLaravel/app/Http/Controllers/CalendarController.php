<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $plants = Plant::where('user_id', $user->id)->get();

        $calendar = [];
        $start = Carbon::today();
        $end = Carbon::today()->addDays(13);

        foreach ($plants as $plant) {
            foreach ($plant->careTasks as $task) {
                $last = Carbon::parse($task->last_completed);
                $freq = max(1, (int) $task->frequency_days);

                $next = (clone $last)->addDays($freq);
                $safety = 0;
                while ($next->lte($end) && $safety < 100) {
                    if ($next->gte($start)) {
                        $key = $next->toDateString();
                        $calendar[$key][] = [
                            'plant' => $plant,
                            'task' => $task,
                            'due' => $next->toDateTimeString(),
                        ];
                    }
                    $next->addDays($freq);
                    $safety++;
                }
            }
        }

        return view('pages.calendar', [
            'plants' => $plants,
            'calendar' => $calendar,
            'calendarStart' => $start->toDateString(),
            'calendarEnd' => $end->toDateString(),
        ]);
    }
}
