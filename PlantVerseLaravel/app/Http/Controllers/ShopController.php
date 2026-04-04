<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;

/**
 * ShopController
 * 
 * Manages reward shop operations including viewing rewards, purchasing them, and redeeming them.
 * Admin routes (edit, update) are protected by the admin middleware.
 * 
 * REFACTORED: Added reward ownership tracking via belongsToMany relationship.
 */
class ShopController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $rewards = Reward::all();

        /**
         * REFACTORED: Dynamic PVT eligibility check
         * 
         * Instead of hardcoding a 100 PVT threshold, we now calculate whether the user
         * has enough balance to afford any reward in the shop.
         */
        $minimumRewardCost = $rewards->isNotEmpty() ? $rewards->min('pvt_cost') : PHP_INT_MAX;
        $isEligible = $user->pvt_balance >= $minimumRewardCost;

        /**
         * REFACTORED: Load user's owned rewards
         * 
         * Fetch all reward IDs that this user already owns.
         * This is passed to the view so the frontend can disable/show "Owned" for purchased rewards.
         * 
         * Uses pluck() for efficiency - returns only the IDs we need instead of full reward objects.
         */
        $ownedRewardIds = $user->rewards()->pluck('reward_id')->toArray();

        return view('pages.shop.index', [
            'rewards' => $rewards,
            'user' => $user,
            'isEligible' => $isEligible,
            'ownedRewardIds' => $ownedRewardIds,
        ]);
    }

    public function redeem(Request $request, $rewardId)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $reward = Reward::findOrFail($rewardId);

        /**
         * REFACTORED: Prevent duplicate ownership
         * 
         * Check if the user already owns this reward.
         * If they do, return an error message instead of allowing duplicate purchase.
         * This prevents accidental double-buying and maintains data integrity.
         */
        if ($user->rewards()->where('reward_id', $rewardId)->exists()) {
            return redirect()->back()->with('error', "You already own {$reward->title}!");
        }

        if ($user->pvt_balance < $reward->pvt_cost) {
            return redirect()->back()->with('error', 'Insufficient PVT balance');
        }

        /**
         * REFACTORED: Track reward ownership
         * 
         * After successfully deducting the PVT cost, attach the reward to the user.
         * This establishes the many-to-many relationship in the reward_user pivot table.
         * The created_at timestamp on the pivot table tracks when they purchased it.
         * 
         * Using attach() ensures the relationship is created even if it already exists,
         * but we've already checked for that above, so this is safe.
         */
        $user->decrement('pvt_balance', $reward->pvt_cost);
        $user->rewards()->attach($rewardId);

        return redirect()->back()->with('success', "You redeemed {$reward->title}!");
    }

    /**
     * Edit reward page
     * 
     * Authorization is handled by the admin middleware in routes/web.php
     */
    public function edit(Request $request, $rewardId)
    {
        $reward = Reward::findOrFail($rewardId);
        return view('pages.shop.edit', compact('reward'));
    }

    /**
     * Update reward
     * 
     * Authorization is handled by the admin middleware in routes/web.php
     */
    public function update(Request $request, $rewardId)
    {
        $reward = Reward::findOrFail($rewardId);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'pvt_cost' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:10',
        ]);

        $reward->update($validatedData);

        return redirect()->route('shop.index')->with('success', 'Reward updated successfully!');
    }
}
