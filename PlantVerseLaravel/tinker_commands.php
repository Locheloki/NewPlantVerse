$admin = User::where('is_admin', true)->orWhere('email', 'like', '%admin%')->first();

if (!$admin) {
echo "No admin user found\n";
exit;
}

echo "Admin User Found:\n";
echo $admin . "\n\n";

// Get admin's plants
$plants = $admin->plants;

echo "Admin has " . $plants->count() . " plants\n";

echo "\nDone!\n";