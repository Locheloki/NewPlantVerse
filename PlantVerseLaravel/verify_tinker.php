$admin = User::find(2);
echo "Admin User Final State:\n";
echo "ID: " . $admin->id . "\n";
echo "Email: " . $admin->email . "\n";
echo "Number of Plants: " . $admin->plants->count() . "\n";
echo "\nFirst 5 Plants Check:\n";
foreach ($admin->plants->take(5) as $plant) {
echo "- " . $plant->name . ": Neglected=" . ($plant->is_neglected ? 'Yes' : 'No') . "\n";
}