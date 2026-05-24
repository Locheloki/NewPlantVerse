<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * AdminController
 * 
 * Handles administrative functions including:
 * - Admin dashboard with user/plant management
 * - Streak testing and demonstration
 * - Test buttons for various features
 * - Manual streak adjustments
 */
class AdminController extends Controller
{
    /**
     * Show admin dashboard with all users and controls
     */
    public function dashboard(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Verify admin access
        if (!$user->is_admin) {
            abort(403, 'Unauthorized - Admin access required');
        }

        $users = User::with(['plants' => function ($query) {
            $query->orderBy('name')->orderBy('id');
        }])->orderBy('name')->get();

        $users->each(function (User $user) {
            $uniquePlants = $user->plants
                ->unique(fn ($plant) => strtolower(trim($plant->name)) . '|' . strtolower(trim($plant->species)))
                ->values();

            $user->setRelation('plants', $uniquePlants);
        });

        return view('admin.dashboard', [
            'users' => $users,
            'currentUser' => $user,
        ]);
    }

    /**
     * Run the plant neglect check command
     */
    public function runNeglectCommand(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            Artisan::call('check:plant-neglect');
            $output = Artisan::output();

            Log::info('Admin: Plant neglect check command executed', [
                'admin_id' => $user->id,
                'output' => $output,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plant neglect check command executed',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            Log::error('Admin: Plant neglect check command failed', [
                'admin_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Command failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run the care consistency update command
     * 
     * Recalculates care consistency for all plants based on their
     * care task completion patterns. Useful for testing the consistency
     * calculation algorithm.
     */
    public function runCareConsistencyCommand(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            Artisan::call('update:care-consistency');
            $output = Artisan::output();

            Log::info('Admin: Care consistency update command executed', [
                'admin_id' => $user->id,
                'output' => $output,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Care consistency update command executed',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            Log::error('Admin: Care consistency update command failed', [
                'admin_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Command failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle user's vacation mode
     */
    public function toggleVacation(Request $request, $userId)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $targetUser = User::find($userId);
        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $targetUser->is_on_vacation = !$targetUser->is_on_vacation;

        if ($targetUser->is_on_vacation) {
            $targetUser->vacation_ends_at = now()->addDays(7);
        } else {
            $targetUser->vacation_ends_at = null;
        }

        $targetUser->save();

        Log::info('Admin test: Vacation toggled', [
            'admin_id' => $user->id,
            'target_user_id' => $targetUser->id,
            'is_on_vacation' => $targetUser->is_on_vacation,
        ]);

        return response()->json([
            'success' => true,
            'message' => $targetUser->is_on_vacation ? 'Vacation activated (7 days)' : 'Vacation deactivated',
            'is_on_vacation' => $targetUser->is_on_vacation,
        ]);
    }

    /**
     * Add PVT to user's balance
     */
    public function addPVT(Request $request, $userId)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'amount' => 'required|integer|min:1|max:10000',
        ]);

        $targetUser = User::find($userId);
        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $oldBalance = $targetUser->pvt_balance;
        $targetUser->pvt_balance += $validated['amount'];
        $targetUser->save();

        Log::info('Admin test: PVT added', [
            'admin_id' => $user->id,
            'target_user_id' => $targetUser->id,
            'old_balance' => $oldBalance,
            'new_balance' => $targetUser->pvt_balance,
            'amount_added' => $validated['amount'],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Added {$validated['amount']} PVT: {$oldBalance} → {$targetUser->pvt_balance}",
            'new_balance' => $targetUser->pvt_balance,
        ]);
    }

    public function simulateMissedCare(Request $request, $plantId)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $plant = Plant::with('careTasks')->find($plantId);
        if (!$plant) {
            return response()->json(['success' => false, 'message' => 'Plant not found'], 404);
        }

        foreach ($plant->careTasks as $task) {
            $task->update([
                'last_completed' => now()->subDays(30),
            ]);
        }

        $plant->update([
            'care_consistency' => 0,
            'is_neglected' => true,
            'care_streak' => 0,
            'streak_started_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$plant->name} set to 30 days missed care. Consistency is now 0%.",
            'care_consistency' => $plant->care_consistency,
        ]);
    }

    public function simulateCareLog(Request $request, $plantId)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $plant = Plant::with('careTasks')->find($plantId);
        if (!$plant) {
            return response()->json(['success' => false, 'message' => 'Plant not found'], 404);
        }

        $task = $plant->careTasks
            ->sortBy(fn ($task) => $task->last_completed?->copy()->addDays($task->frequency_days)->timestamp ?? 0)
            ->first();

        if (!$task) {
            return response()->json(['success' => false, 'message' => 'This plant has no care tasks to log.'], 422);
        }

        $task->update(['last_completed' => now()]);

        $oldConsistency = $plant->care_consistency;
        $plant->care_consistency = min(100, $plant->care_consistency + 10);
        $plant->is_neglected = false;
        $plant->last_care_completed_at = now();
        $plant->care_streak = max(1, $plant->care_streak + 1);
        $plant->streak_started_at ??= now();
        $plant->save();

        return response()->json([
            'success' => true,
            'message' => "Logged {$task->type} for {$plant->name}: {$oldConsistency}% -> {$plant->care_consistency}%.",
            'care_consistency' => $plant->care_consistency,
        ]);
    }

    public function recalculatePlantConsistency(Request $request, $plantId)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $plant = Plant::with('careTasks')->find($plantId);
        if (!$plant) {
            return response()->json(['success' => false, 'message' => 'Plant not found'], 404);
        }

        $scores = [];
        $isNeglected = false;

        foreach ($plant->careTasks as $task) {
            if (!$task->last_completed) {
                $scores[] = 0;
                $isNeglected = true;
                continue;
            }

            $dueDate = $task->last_completed->copy()->addDays($task->frequency_days);
            $gracePeriodStart = $dueDate->copy()->subHours(12);

            if (now()->lessThan($gracePeriodStart)) {
                $scores[] = 100;
                continue;
            }

            if (now()->lessThanOrEqualTo($dueDate)) {
                $scores[] = 85;
                continue;
            }

            $daysOverdue = (int) floor($dueDate->diffInDays(now()));
            $missedIntervals = max(1, (int) floor($daysOverdue / max(1, $task->frequency_days)) + 1);
            $scores[] = max(0, 85 - (($daysOverdue * 8) + ($missedIntervals * 12)));

            if ($daysOverdue > 3) {
                $isNeglected = true;
            }
        }

        $oldConsistency = $plant->care_consistency;
        $plant->care_consistency = count($scores) > 0 ? (int) round(array_sum($scores) / count($scores)) : 0;
        $plant->is_neglected = $isNeglected;
        $plant->save();

        return response()->json([
            'success' => true,
            'message' => "Recalculated {$plant->name}: {$oldConsistency}% -> {$plant->care_consistency}%.",
            'care_consistency' => $plant->care_consistency,
        ]);
    }
}
