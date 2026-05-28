<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserDailyActivity;
use Illuminate\Http\Request;

/**
 * OrdersController
 * 
 * Manages user orders and their delivery status.
 * Users can view their orders, track delivery status, and view order history.
 */
class OrdersController extends Controller
{
    /**
     * Show all orders for the authenticated user
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        UserDailyActivity::recordLogin($user->id);

        $orders = $user->orders()
            ->with('reward')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Group orders by status for the summary
        $orderStats = [
            'pending' => $user->orders()->where('status', 'pending')->count(),
            'processing' => $user->orders()->where('status', 'processing')->count(),
            'shipped' => $user->orders()->where('status', 'shipped')->count(),
            'delivered' => $user->orders()->where('status', 'delivered')->count(),
            'cancelled' => $user->orders()->where('status', 'cancelled')->count(),
        ];

        return view('pages.orders.index', [
            'orders' => $orders,
            'orderStats' => $orderStats,
        ]);
    }

    /**
     * Show details of a specific order
     */
    public function show(Request $request, $orderId)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $order = Order::findOrFail($orderId);

        // Verify the order belongs to the current user
        if ($order->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        return view('pages.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Cancel an order (only if pending or processing)
     */
    public function cancel(Request $request, $orderId)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $order = Order::findOrFail($orderId);

        // Verify ownership
        if ($order->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Can only cancel pending or processing orders
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->back()
                ->with('error', 'Cannot cancel orders that have already shipped.');
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->back()
            ->with('success', 'Order cancelled successfully.');
    }
}
