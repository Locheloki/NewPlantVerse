$admin = User::find(2);
echo "Admin User Final State:\n";
echo "ID: " . $admin->id . "\n";
echo "Email: " . $admin->email . "\n";
echo "Daily Streak: " . $admin->daily_streak . "\n";
echo "Last Care Date: " . $admin->last_care_date . "\n";
echo "Number of Plants: " . $admin->plants->count() . "\n";
echo "\nFirst 5 Plants Check:\n";
foreach ($admin->plants->take(5) as $plant) {
    echo "- " . $plant->name . ": Streak=" . $plant->care_streak . ", Started=" . $plant->streak_started_at . "\n";
}
