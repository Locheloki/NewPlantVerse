# 🎯 NewPlantVerse - Command Reference Sheet

## Copy-Paste Commands for Testing

All commands ready to copy and paste directly into your terminal!

---

## ✅ PREREQUISITES
```bash
cd c:\vs_PlantVerse\NewPlantVerse\PlantVerseLaravel
```
(Make sure you're in this directory for all commands)

---

## 1️⃣ VIEW ALL AVAILABLE COMMANDS
```bash
php artisan list
```
**Expected**: You'll see `check:plant-neglect` and `send:care-reminders` listed

---

## 2️⃣ TEST: PLANT NEGLECT CHECK
```bash
php artisan check:plant-neglect
```

**Expected Output**:
```
Starting plant neglect check...
✓ Plant neglect check completed!
  Neglected Plants Found: X
  Users Penalized: X
  Total PVT Deducted: X
  Plants Skipped (Vacation): 0
```

---

## 3️⃣ TEST: SEND CARE REMINDERS
```bash
php artisan send:care-reminders
```

**Expected Output**:
```
Starting care reminder notifications...
✓ Care reminder sending completed!
  Reminders Sent: X
  Users Skipped: 0
```

---

## 4️⃣ TEST: DATABASE INSPECTION (Advanced)

### Get database shell (sqlite)
```bash
php artisan tinker
```

Inside the tinker prompt (`>>>`), paste these one at a time:

### Check how many plants exist
```php
>>> App\Models\Plant::count()
```

### View recent plants
```php
>>> App\Models\Plant::latest()->limit(3)->get()
```

### View plant journals
```php
>>> App\Models\PlantJournal::with('plant')->latest()->limit(5)->get()
```

### Check vacation users
```php
>>> App\Models\User::where('is_on_vacation', true)->get()
```

### View care tasks
```php
>>> App\Models\CareTask::latest()->limit(5)->get()
```

### Exit tinker
```php
>>> exit
```

---

## 5️⃣ TEST: CREATE TEST DATA (If needed)

### Create a test plant with future tasks
```php
>>> $plant = factory(App\Models\Plant::class)->create(['user_id' => 1]);
>>> $plant->careTasks()->create(['type' => 'Water', 'frequency_days' => 7, 'last_completed' => now()]);
>>> $plant
```

### Set user to vacation mode
```php
>>> $user = App\Models\User::find(1);
>>> $user->update(['is_on_vacation' => true, 'vacation_ends_at' => now()->addDays(7)]);
>>> $user
```

### Create overdue care task (for neglect testing)
```php
>>> $task = App\Models\CareTask::first();
>>> $task->update(['last_completed' => now()->subDays(5)]);
>>> $task
```

### View all users
```php
>>> App\Models\User::all()
```

---

## 6️⃣ TEST: VIEW APPLICATION LOGS
```bash
type storage\logs\laravel.log
```
**Look for**: "Plant marked as neglected" or "Care reminder notification sent"

### View only recent logs (last 50 lines)
```bash
powershell -Command "(Get-Content storage\logs\laravel.log -Tail 50)"
```

### Search for specific entries
```bash
findstr /I "neglected" storage\logs\laravel.log
findstr /I "care reminder" storage\logs\laravel.log
findstr /I "vacation" storage\logs\laravel.log
```

---

## 7️⃣ UI TESTING (Browser)

**App URL**: http://127.0.0.1:8000

### Login
- Email: `test@example.com`
- Password: `password`

### Test Locations
- **Dashboard**: http://127.0.0.1:8000/dashboard
- **My Plants**: http://127.0.0.1:8000/my-plants
- **Add Plant**: http://127.0.0.1:8000/my-plants/add
- **Edit Plant** (ID 1): http://127.0.0.1:8000/my-plants/1/edit
- **View Plant** (ID 1): http://127.0.0.1:8000/my-plants/1

---

## 8️⃣ DATABASE SCHEMA VERIFICATION

### Check migrations were applied
```bash
php artisan migrate:status
```

**Expected**: 
```
2026_04_04_add_vacation_mode_to_users_table ......................... [2] Ran
2026_04_04_create_plant_journals_table .............................. [2] Ran
```

---

## 9️⃣ DEVELOPMENT MODE: RUN COMMANDS EVERY MINUTE

### Edit Kernel.php (optional for testing)
Open: `app/Console/Kernel.php`

Find the commented section around line 70:
```php
// Optional: Debug/Development Only
// Uncomment below to test scheduling every minute (development only!)
// $schedule->command(CheckPlantNeglect::class)->everyMinute();
// $schedule->command(SendCareReminders::class)->everyMinute();
```

Change to:
```php
// Optional: Debug/Development Only
// Uncomment below to test scheduling every minute (development only!)
$schedule->command(CheckPlantNeglect::class)->everyMinute();
$schedule->command(SendCareReminders::class)->everyMinute();
```

Then run:
```bash
php artisan schedule:work
```

This will execute both commands **every minute** for development testing!

---

## 🔟 CLEAR CACHE & LOGS

### Clear all cache
```bash
php artisan cache:clear
php artisan config:cache
```

### Clear logs
```bash
cd PlantVerseLaravel
rm storage\logs\laravel.log
```

### Restart in fresh state
```bash
php artisan migrate:refresh --seed
```
⚠️ **Warning**: This deletes all test data!

---

## 1️⃣1️⃣ QUICK COMMAND SEQUENCE (Copy + Paste)

Run these commands in order, one terminal line at a time:

```bash
# Navigate to project
cd c:\vs_PlantVerse\NewPlantVerse\PlantVerseLaravel

# Test 1: List all commands
php artisan list

# Test 2: Check neglect
php artisan check:plant-neglect

# Test 3: Send reminders
php artisan send:care-reminders

# Test 4: View migrations
php artisan migrate:status

# Test 5: View logs
type storage\logs\laravel.log
```

---

## 🔐 COMPLETE TESTING WORKFLOW

**Terminal Tab 1** (Keep this running):
```bash
cd c:\vs_PlantVerse\NewPlantVerse\PlantVerseLaravel
php artisan serve
# Browse: http://127.0.0.1:8000
```

**Terminal Tab 2** (Run commands):
```bash
cd c:\vs_PlantVerse\NewPlantVerse\PlantVerseLaravel

# Test command 1
php artisan check:plant-neglect

# Test command 2
php artisan send:care-reminders

# Test inspector
php artisan tinker
# Run the database queries from section 5️⃣
```

**Browser**:
- Login to http://127.0.0.1:8000
- Test CRUD operations in `/my-plants`
- Add journal entries
- Verify UI changes

---

## ✨ EXPECTED SUCCESS OUTPUTS

### ✅ check:plant-neglect Success
```
✓ Plant neglect check completed!
  Neglected Plants Found: 1
  Users Penalized: 1
  Total PVT Deducted: 5
  Plants Skipped (Vacation): 0
```

### ✅ send:care-reminders Success
```
✓ Care reminder sending completed!
  Reminders Sent: 1
  Users Skipped: 0
```

### ✅ Plant CRUD Success
- Create: Redirects to plant show page
- Edit: Form loads, changes save
- Delete: Plant removed from list
- Journal: Entry appears in timeline

---

## 🆘 ERROR SOLUTIONS

### "Cache store [DATABASE] not defined"
**Fix**: Don't use `artisan tinker`, use `php artisan list` instead

### "Database connection error"
**Fix**: Check `.env` file for correct DB credentials

### Photos not showing
**Fix**: Run `php artisan storage:link`

### Commands not executing
**Fix**: Clear config cache: `php artisan config:cache`

---

## 📞 HELP REFERENCE

| Issue | Command |
|-------|---------|
| Check all migrations | `php artisan migrate:status` |
| List all commands | `php artisan list` |
| View Laravel version | `php artisan --version` |
| Show app config | `php artisan config:show` |
| Reset everything | `php artisan migrate:refresh --seed` |
| Clear cache | `php artisan cache:clear` |
| Inspect database | `php artisan tinker` |

---

## 🎯 Test Complete When:

- ✅ Both commands run without errors
- ✅ Browser shows plant CRUD working
- ✅ Journal entries appear with photos
- ✅ Logs show command execution
- ✅ Database shows new records created
- ✅ Vacation mode skips neglect checks

**You're done testing!** 🎉
