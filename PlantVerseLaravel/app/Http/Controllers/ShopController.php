<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reward;

class ShopController extends Controller
{
    public function index()
    {
        $user = User::firstOrFail();
        $rewards = Reward::all();

        return view('pages.shop.index', [
            'rewards' => $rewards,
            'user' => $user,
            'isEligible' => $user->pvt_balance >= 100,
        ]);
    }

    public function redeem($rewardId)
    {
        $user = User::firstOrFail();
        $reward = Reward::findOrFail($rewardId);

        if ($user->pvt_balance < $reward->pvt_cost) {
            return redirect()->back()->with('error', 'Insufficient PVT balance');
        }

        $user->decrement('pvt_balance', $reward->pvt_cost);

        return redirect()->back()->with('success', "You redeemed {$reward->title}!");
    }
}
