# NewPlantVerse - Complete Feature Testing Guide
# Date: April 4, 2026

## ✅ TESTING CHECKLIST

All new features are now live and ready to test. Follow the scenarios below:

---

## 1️⃣ FLEXIBLE CARE WINDOWS (12-Hour Grace Period)

### Scenario: Log care task before exact due time
**Test**: Can a user log a care task 6 hours BEFORE it's officially due?

**Setup**:
1. Login at: http://127.0.0.1:8000/login
   - Email: test@example.com
   - Password: password

2. Navigate to: `/my-plants` → Click any plant

3. In database, set a care task to be due tomorrow 10:00 AM:
   ```bash
   # Manual: UPDATE care_tasks SET last_completed = NOW() - INTERVAL 5 DAYS WHERE type='Water';
   ```

4. Try to log the "Water" task at 9:00 PM today
   - **BEFORE FIX**: Would fail with "Come back later!"
   - **AFTER FIX**: Should succeed because you're within 12-hour grace period
   - **EXPECTED**: ✅ "+10 PVT" success message

**Expected Behavior:**
- Users can log tasks 12 hours early
- Earlybird users get rewards for planning ahead!
- Message shows accurate days remaining

---

## 2️⃣ FULL PLANT CRUD (Edit, Update, Destroy)

### Test 1: Add a test plant
1. Go to `/my-plants/add`
2. Fill form:
   - Name: "Test Rose"
   - Species: "Rosa damascena"
   - Upload a photo (optional)
3. Click "Add Plant"
4. **EXPECTED**: ✅ Redirects to plant detail page with success message

### Test 2: Edit plant information
1. From plant details page, click "Edit Plant"
2. Change name to "Beautiful Rose"
3. Update species to "Rosa hybrid"
4. Click "Update"
5. **EXPECTED**: ✅ Plant updated, redirects back to show page

### Test 3: Delete plant
1. Go to `/my-plants/{id}`
2. Scroll to bottom and click "Delete Plant"
3. Confirm deletion
4. **EXPECTED**: ✅ Plant deleted, redirected to `/my-plants` index
   - Plant no longer appears in list
   - All care tasks automatically deleted (cascade delete)

### Test 4: Authorization check (Security)
1. Login as User A (test@example.com)
2. Get plant ID of User B's plant (e.g., ID=5)
3. Try to directly visit: `/my-plants/5/edit` (where ID 5 belongs to User B)
4. **EXPECTED**: ✅ 403 Forbidden error (cannot edit someone else's plant)

---

## 3️⃣ THE CONSEQUENCE ENGINE (CheckPlantNeglect Command)

### Test 1: Mark plant as neglected (no vacation)
**Setup**: Create an overdue care task

```bash
# Option A: Use Artisan directly
cd PlantVerseLaravel
php artisan tinker
> $plant = App\Models\Plant::first();
> $plant->careTasks()->first()->update(['last_completed' => now()->subDays(5)]);
> exit

# Option B: Direct database
# UPDATE care_tasks SET last_completed = DATE_SUB(NOW(), INTERVAL 5 DAY) WHERE id = 1;
```

**Run the command**:
```bash
php artisan check:plant-neglect
```

**Expected Output**:
```
✓ Plant neglect check completed!
  Neglected Plants Found: 1
  Users Penalized: 1
  Total PVT Deducted: 5
  Plants Skipped (Vacation): 0
```

**Verify**:
- Plant is_neglected column = true
- User pvt_balance reduced by 5 PVT

### Test 2: Skip plants (user on vacation)

```bash
cd PlantVerseLaravel
php artisan tinker

# Set user to vacation mode
> $user = App\Models\User::first();
> $user->update(['is_on_vacation' => true, 'vacation_ends_at' => now()->addDays(5)]);

# Create overdue task
> $user->plants()->first()->careTasks()->first()->update(['last_completed' => now()->subDays(5)]);
> exit
```

**Run the command**:
```bash
php artisan check:plant-neglect
```

**Expected Output**:
```
✓ Plant neglect check completed!
  Neglected Plants Found: 0
  Users Penalized: 0
  Total PVT Deducted: 0
  Plants Skipped (Vacation): 1
```

**Verify**: User's PVT balance unchanged (vacation protection worked!)

### Test 3: Auto-reset vacation when expired

```bash
cd PlantVerseLaravel
php artisan tinker

# Set vacation to expired
> $user = App\Models\User::first();
> $user->update(['is_on_vacation' => true, 'vacation_ends_at' => now()->subHours(1)]);
> exit
```

**Run the command**:
```bash
php artisan check:plant-neglect
```

**Expected Output**:
```
ℹ️  Vacation Mode Reset: 1 user(s)
✓ Plant neglect check completed!
```

**Verify**: User is_on_vacation = false, vacation_ends_at = NULL

---

## 4️⃣ PLANT GROWTH JOURNAL (Photo + Notes)

### Test 1: Add journal entry with photo
1. Go to plant detail page: `/my-plants/{id}`
2. Scroll to "Growth Journal" section
3. Upload a photo of your plant
4. Add note: "My plant is looking healthy!"
5. Click "Add Journal Entry"
6. **EXPECTED**: ✅ Entry appears in timeline with timestamp

### Test 2: Add journal entry with text only
1. Same as above but skip photo upload
2. Just add note: "Day 15: New growth spotted!"
3. Click "Add Journal Entry"
4. **EXPECTED**: ✅ Entry created (photo_url = NULL, note stored)

### Test 3: Verify photo storage
1. Open browser DevTools (F12)
2. Go to plant detail page
3. Right-click on journal photo → "Copy image link"
4. Should be: `http://127.0.0.1:8000/storage/journals/filename.jpg`
5. **EXPECTED**: ✅ Photos accessible via public storage link

### Test 4: Check cascade delete
1. Go to `/my-plants`
2. Delete a plant that has 2+ journal entries
3. Go to database and verify:
   ```sql
   SELECT COUNT(*) FROM plant_journals WHERE plant_id = {deleted_id};
   -- Should return 0 (journal entries deleted)
   ```
4. **EXPECTED**: ✅ All journal entries deleted with plant

---

## 5️⃣ DAILY CARE DIGEST NOTIFICATIONS

### Test 1: Manual trigger (development)
```bash
cd PlantVerseLaravel
php artisan send:care-reminders
```

**Expected Output**:
```
✓ Care reminder sending completed!
  Reminders Sent: 1
  Users Skipped: 0
```

### Test 2: Check email queue (if using queue)
If notifications are queued:
```bash
# Optional: Process queued notifications
php artisan queue:work
```

### Test 3: Verify notification content
1. Check your app's configured email driver
2. If using 'log' driver, check: `storage/logs/laravel.log`
3. Look for email subjects containing "Daily Care Reminder 🌱"
4. **EXPECTED**: ✅ Email contains:
   - User greeting
   - Count: "You have X plant(s) that need care"
   - Each plant: "🌿 Rose needs: Water, Fertilize"
   - Link to `/my-plants`

### Test 4: Skip vacation users
```bash
cd PlantVerseLaravel
php artisan tinker

# Create overdue task for vacation user
> $user = App\Models\User::where('is_on_vacation', true)->first();
> $user->plants()->first()->careTasks()->first()->update(['last_completed' => now()->subDays(5)]);
> exit
```

**Run**:
```bash
php artisan send:care-reminders
```

**Expected**: Vacation user NOT in reminders sent count

---

## 6️⃣ SCHEDULER INTEGRATION (Production Setup)

### Verify scheduled commands are registered:
```bash
cd PlantVerseLaravel
php artisan schedule:list
```

**Expected Output** (shows next run times):
```
+----------------------------------+----------+
| Command                          | Interval |
+----------------------------------+----------+
| check:plant-neglect              | Daily at 02:00 |
| send:care-reminders              | Daily at 09:00 |
+----------------------------------+----------+
```

### Development Testing (every minute)
Edit `app/Console/Kernel.php` and uncomment (line ~46):
```php
$schedule->command(CheckPlantNeglect::class)->everyMinute();
$schedule->command(SendCareReminders::class)->everyMinute();
```

Then run scheduler worker:
```bash
php artisan schedule:work
```

**Expected**: Commands run every minute for 60 seconds while you watch.

---

## 7️⃣ VACATION MODE (User Model)

### Test 1: Set vacation via database
```bash
cd PlantVerseLaravel
php artisan tinker

> $user = App\Models\User::find(1);
> $user->update([
    'is_on_vacation' => true,
    'vacation_ends_at' => now()->addDays(7)
  ]);
> dd($user);
> exit
```

**Verify Output**:
```
is_on_vacation: true
vacation_ends_at: 2026-04-11 XX:XX:XX
```

### Test 2: Create UI to toggle vacation (optional)
You can add a vacation toggle in the dashboard or settings. For now:
1. Verify via database that columns exist
2. Confirm next `check:plant-neglect` skips their plants
3. Confirm `send:care-reminders` skips them

---

## 📊 COMPLETE TEST FLOW (Start to Finish)

**Option A: UI Testing** (15 minutes)
```
1. Login → Add Plant → Edit Plant → View Details
2. Add Journal (Photo + Note) → Verify photos display
3. Delete Plant → Verify cascade delete
4. Check authorization (try to edit another user's plant)
```

**Option B: Command Testing** (10 minutes)
```
1. php artisan check:plant-neglect (basic)
2. php artisan check:plant-neglect (with vacation)
3. php artisan send:care-reminders
4. php artisan schedule:list
```

**Option C: Full Integration Test** (30 minutes)
```
1. Do Option A (UI)
2. Do Option B (Commands)
3. Set up test data with various plant statuses
4. Run all commands multiple times
5. Verify logs in storage/logs/laravel.log
```

---

## 🐛 DEBUGGING TIPS

### Enable debug logging:
Edit `.env`:
```
LOG_LEVEL=debug
```

### Check command execution logs:
```bash
tail -f storage/logs/laravel.log | grep "Plant marked as neglected\|Care reminder"
```

### Verify database changes:
```bash
cd PlantVerseLaravel
php artisan tinker

> App\Models\Plant::where('is_neglected', true)->get();
> App\Models\User::where('is_on_vacation', true)->get();
> App\Models\PlantJournal::latest()->limit(5)->get();
```

### Check migration status:
```bash
php artisan migrate:status
```

### Common issues & fixes:
- **"Cache store [DATABASE] not defined"**: Use `php artisan schedule:list` instead of tinker
- **Photos not loading**: Run `php artisan storage:link` again
- **Notifications not sending**: Check config/mail.php and MAIL_DRIVER in .env
- **Commands not running**: Verify scheduler cron is active or use `schedule:work`

---

## ✨ FEATURE SUMMARY TABLE

| Feature | Route/Command | Test Time | Status |
|---------|---------------|-----------|--------|
| Flexible Care Windows | Log care task | 2 min | ✅ Ready |
| Plant Edit | `/my-plants/{id}/edit` | 3 min | ✅ Ready |
| Plant Update | `PUT /my-plants/{id}` | 3 min | ✅ Ready |
| Plant Delete | `DELETE /my-plants/{id}` | 3 min | ✅ Ready |
| Check Neglect | `php artisan check:plant-neglect` | 2 min | ✅ Ready |
| Vacation Skip | Database toggle + command | 3 min | ✅ Ready |
| Journal Add | `POST /my-plants/{id}/journal` | 3 min | ✅ Ready |
| Journal Gallery | Plant detail page | 2 min | ✅ Ready |
| Care Reminders | `php artisan send:care-reminders` | 2 min | ✅ Ready |
| Scheduler | `php artisan schedule:list` | 1 min | ✅ Ready |

**Total Estimated Testing Time: 30-45 minutes for full coverage**

---

## 🎯 QUICK START

```bash
# Terminal 1: Start server
cd PlantVerseLaravel && php artisan serve

# Terminal 2: Run commands
cd PlantVerseLaravel

# Test 1: Check neglect
php artisan check:plant-neglect

# Test 2: Send reminders
php artisan send:care-reminders

# Test 3: View schedule
php artisan schedule:list

# Browser: Visit http://127.0.0.1:8000
# Login and test UI features (CRUD, Journal)
```

---

Happy testing! 🌱🎮
