<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Milestone;

class MilestonesController extends Controller
{
    public function index()
    {
        $user = User::firstOrFail();
        $milestones = Milestone::where('user_id', $user->id)->get();

        return view('pages.milestones.index', [
            'milestones' => $milestones,
            'user' => $user,
        ]);
    }
}
