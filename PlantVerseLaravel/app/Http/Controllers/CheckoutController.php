<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Reward;
use Illuminate\Http\Request;

/**
 * CheckoutController
 * 
 * Manages the checkout flow for redeemed rewards.
 * Allows users to enter delivery information and place orders for physical/digital rewards.
 */
class CheckoutController extends Controller
{
    /**
     * Show checkout form for a specific reward
     * 
     * Displays the delivery address form where users can enter shipping details.
     */
    public function show(Request $request, $rewardId)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $reward = Reward::findOrFail($rewardId);

        // Check if the user owns this reward
        if (!$user->rewards()->where('reward_id', $rewardId)->exists()) {
            return redirect()->route('shop.index')->with('error', 'You do not own this reward.');
        }

        // Check if user has already placed an order for this reward
        $existingOrder = Order::where('user_id', $user->id)
            ->where('reward_id', $rewardId)
            ->whereIn('status', ['pending', 'processing', 'shipped', 'delivered'])
            ->first();

        if ($existingOrder) {
            return redirect()->route('orders.show', $existingOrder->id)
                ->with('info', 'You have already placed an order for this reward.');
        }

        // Pre-fill user information if available
        $defaultData = [
            'full_name' => $user->name,
            'email' => $user->email,
            'phone' => '',
            'street_address' => '',
            'city' => '',
            'state_province' => '',
            'postal_code' => '',
            'country' => '',
        ];

        return view('pages.checkout.show', [
            'reward' => $reward,
            'user' => $user,
            'defaultData' => $defaultData,
        ]);
    }

    /**
     * Store checkout - create order with delivery information
     * 
     * Validates address information and creates an order record.
     */
    public function store(Request $request, $rewardId)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $reward = Reward::findOrFail($rewardId);

        // Verify user owns this reward
        if (!$user->rewards()->where('reward_id', $rewardId)->exists()) {
            return redirect()->route('shop.index')->with('error', 'You do not own this reward.');
        }

        // Validate delivery information
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state_province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        // Create the order
        $order = Order::create([
            'user_id' => $user->id,
            'reward_id' => $rewardId,
            ...$validatedData,
            'status' => 'pending',
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Order placed successfully! We will process and ship your reward soon.');
    }
}
