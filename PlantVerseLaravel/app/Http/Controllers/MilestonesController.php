<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestonesController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $milestones = Milestone::where('user_id', $user->id)->get();

        return view('pages.milestones.index', [
            'milestones' => $milestones,
            'user' => $user,
        ]);
    }
}
