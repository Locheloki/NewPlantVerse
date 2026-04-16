$admin = User::where('is_admin', true)->orWhere('email', 'like', '%admin%')->first();

if (!$admin) {
    echo "No admin user found\n";
    exit;
}

echo "Admin User Found:\n";
echo $admin . "\n\n";

// Update admin user streaks
$admin->daily_streak = 5;
$admin->last_care_date = now();
$admin->save();

echo "Admin User Updated:\n";
echo "Daily Streak: " . $admin->daily_streak . "\n";
echo "Last Care Date: " . $admin->last_care_date . "\n\n";

// Get admin's plants
$plants = $admin->plants;

echo "Admin has " . $plants->count() . " plants\n";

// Update each plant's streaks
$threeDaysAgo = now()->subDays(3);
foreach ($plants as $plant) {
    $plant->care_streak = 3;
    $plant->streak_started_at = $threeDaysAgo;
    $plant->save();
}

echo "\nPlant Streaks Updated:\n";
foreach ($plants as $plant) {
    echo "Plant: " . $plant->name . " | Care Streak: " . $plant->care_streak . " | Started: " . $plant->streak_started_at . "\n";
}

echo "\nDone!\n";
