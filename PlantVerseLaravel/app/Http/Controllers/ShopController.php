<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $rewards = Reward::all();

        return view('pages.shop.index', [
            'rewards' => $rewards,
            'user' => $user,
            'isEligible' => $user->pvt_balance >= 100,
        ]);
    }

    public function redeem(Request $request, $rewardId)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $reward = Reward::findOrFail($rewardId);

        if ($user->pvt_balance < $reward->pvt_cost) {
            return redirect()->back()->with('error', 'Insufficient PVT balance');
        }

        $user->decrement('pvt_balance', $reward->pvt_cost);

        return redirect()->back()->with('success', "You redeemed {$reward->title}!");
    }
}
