# 🌱 PlantVerse - Fresh Windows Installation Guide

## Complete Step-by-Step Setup (Fresh Windows PC)

This guide will walk you through installing PlantVerse on a completely fresh Windows machine with nothing pre-installed.

**Estimated Time**: 30-45 minutes (depending on download speeds)

---

## 📋 PART 1: INSTALL REQUIRED SOFTWARE (15 minutes)

### Step 1.1: Install PHP 8.2+

1. Go to **https://windows.php.net/downloads/releases/**
2. Download the **Non Thread Safe (NTS) x64 version** (e.g., `php-8.2.x-nts-Win32-x64-vs16.zip`)
3. Extract the zip file to: `C:\PHP`
4. Open **Control Panel → Environment Variables**
5. Click **"Environment Variables"** button
6. Under **"System variables"**, click **"New"**
   - Variable name: `PATH`
   - Variable value: `C:\PHP`
7. Click **OK** three times to save

**Verify installation**:
- Open **PowerShell** as Administrator
- Type: `php -v`
- Should show: `PHP 8.2.x ...`

---

### Step 1.2: Install Composer (PHP Dependency Manager)

1. Go to **https://getcomposer.org/download/**
2. Download **Composer-Setup.exe** (Windows Installer)
3. Run the installer
4. When prompted for PHP location, select: `C:\PHP\php.exe`
5. Complete the installation

**Verify installation**:
- Open **PowerShell** as Administrator
- Type: `composer -v`
- Should show: `Composer version 2.x.x ...`

---

### Step 1.3: Install Node.js & npm

1. Go to **https://nodejs.org/**
2. Download the **LTS (Long Term Support) version**
3. Run the installer and follow the default installation steps
4. Choose to install npm when prompted

**Verify installation**:
- Open **PowerShell** as Administrator
- Type: `node -v` and `npm -v`
- Should show version numbers like `v20.x.x` and `10.x.x`

---

### Step 1.4: Install MySQL 8.0+

1. Go to **https://dev.mysql.com/downloads/mysql/**
2. Download **MySQL Community Server** (Windows x86 64-bit MSI Installer)
3. Run the installer:
   - Choose **Development Default** setup type
   - Accept defaults for most options
   - When prompted for MySQL port, use default **3306**
   - When prompted for password, set it or leave blank for `root` user
4. Complete installation

**Verify installation**:
- Open **PowerShell** as Administrator
- Type: `mysql -u root -p` (leave password blank or enter your password)
- If it connects, type: `EXIT`

---

### Step 1.5: Install Git (for version control & easy cloning)

1. Go to **https://git-scm.com/download/win**
2. Download the Windows installer
3. Run installer and use default settings

**Verify installation**:
- Open **PowerShell** as Administrator
- Type: `git --version`
- Should show: `git version 2.x.x ...`

---

## 📋 PART 2: INSTALL PLANTVERSE (15 minutes)

### Step 2.1: Clone or Copy the Repository

**Option A: Clone via Git** (Recommended)
```powershell
cd C:\
git clone https://github.com/YOUR_USERNAME/plantverse.git
cd plantverse\PlantVerseLaravel
```

**Option B: Copy Files Manually**
```powershell
# Copy the entire project folder to C:\plantverse\
# Then open PowerShell and navigate to:
cd C:\plantverse\PlantVerseLaravel
```

---

### Step 2.2: Install PHP Dependencies with Composer

```powershell
composer install
```

⏳ **This takes 3-5 minutes**. You'll see it downloading and installing all required packages.

**Expected output**: 
```
Loading composer repositories with package information
...
Generating autoload files
✓ Successfully installed
```

---

### Step 2.3: Set Up Environment Configuration

1. Navigate to your project folder in PowerShell
2. Copy the example .env file:
```powershell
copy .env.example .env
```

3. Open `.env` in a text editor (Notepad or VS Code):
   - Find the line: `APP_KEY=`
   - Leave it empty (we'll generate it in the next step)

4. Find the database configuration section and set it up:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plantverse
DB_USERNAME=root
DB_PASSWORD=
```

**Note**: If you set a MySQL password during installation, add it after `DB_PASSWORD=`

---

### Step 2.4: Generate Application Key

```powershell
php artisan key:generate
```

**Expected output**:
```
Application key set successfully.
```

This generates a unique encryption key and updates `.env` automatically.

---

### Step 2.5: Create Database

**Option A: MySQL Command Line**
```powershell
mysql -u root -p
```
(Leave password blank or enter your MySQL password)

Then type:
```sql
CREATE DATABASE plantverse;
EXIT
```

**Option B: Use Laravel (Automatic)**
The next step will create the database automatically.

---

### Step 2.6: Run Database Migrations

```powershell
php artisan migrate
```

⏳ **Takes 30 seconds to 1 minute**

**Expected output**:
```
  Migrating: 2014_10_12_000000_create_users_table
  Migrating: 2014_10_12_100000_create_password_resets_table
  ...
  ✓ All migrations completed successfully
```

This creates all necessary database tables.

---

### Step 2.7: Install Frontend Dependencies

```powershell
npm install
```

⏳ **Takes 2-3 minutes**

**Expected output**:
```
added XXX packages
```

---

### Step 2.8: Build Frontend Assets

```powershell
npm run build
```

⏳ **Takes 30 seconds to 1 minute**

**Expected output**:
```
  ✓ built in 15.23s
```

---

## 📋 PART 3: START THE APPLICATION (2 minutes)

### Step 3.1: Start the Laravel Development Server

Open **PowerShell** and run:

```powershell
php artisan serve
```

**Expected output**:
```
  INFO  Server running on [http://127.0.0.1:8000].

  Press Ctrl+C to quit
```

✅ **Keep this terminal open!**

---

### Step 3.2: Access the Application

1. Open your web browser (Chrome, Firefox, Edge, etc.)
2. Go to: **http://127.0.0.1:8000**
3. You should see the PlantVerse login page

---

## 📋 PART 4: CREATE TEST USER & LOGIN (2 minutes)

### Step 4.1: Create a Test User (via Tinker)

Open a **NEW PowerShell** window (keep the server running in the other one):

```powershell
cd C:\plantverse\PlantVerseLaravel
php artisan tinker
```

Now type these commands in tinker:

```php
$user = App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
]);

echo "User created!";
exit
```

**Expected output**:
```
User created!
```

---

### Step 4.2: Login to the Application

1. Refresh your browser (http://127.0.0.1:8000)
2. Click **"Login"** 
3. Enter:
   - Email: `test@example.com`
   - Password: `password`
4. Click **"Login"**

✅ **You should now see the PlantVerse dashboard!**

---

## 🧪 PART 5: TEST THE APPLICATION (5 minutes)

### Test Core Features

**Test 1: Add a Plant**
- Click "Add Plant"
- Fill in name: "Monstera"
- Fill in species: "Monstera Deliciosa"
- Click "Add Plant"
- ✅ Should appear on your dashboard

**Test 2: View Plant Details**
- Click on the plant
- See care tasks and schedule
- ✅ Should show watering frequency, etc.

**Test 3: Log a Care Task**
- Click green "Log Care" button
- ✅ Should show "+10 PVT" reward message

**Test 4: Edit & Delete Plant**
- Click "Edit Plant" → Make changes → Click "Update"
- Click "Delete Plant" → Confirm
- ✅ Changes should be saved/deleted

**Test 5: Add Plant Journal Entry**
- From plant details, scroll to "Plant Journal"
- Click "Add Entry"
- Add a note and optional photo
- ✅ Entry should appear in timeline

---

## 🚀 OPTIONAL: RUN SCHEDULER COMMANDS

Open a **NEW PowerShell** window and run:

```powershell
cd C:\plantverse\PlantVerseLaravel

# Test the neglect checker
php artisan check:plant-neglect

# Test care reminders
php artisan send:care-reminders
```

✅ Both should complete without errors

---

## 🔧 TROUBLESHOOTING

### Issue: "PHP is not recognized"
- **Solution**: Restart PowerShell or check that `C:\PHP` is in your PATH environment variable

### Issue: "Composer command not found"
- **Solution**: Reinstall Composer or add it to PATH

### Issue: "MySQL connection refused"
- **Solution**: 
  - Check MySQL is running (Services in Windows)
  - Verify `DB_PASSWORD` in `.env` is correct
  - Run: `mysql -u root -p` to test connection

### Issue: "Port 8000 already in use"
- **Solution**: 
  ```powershell
  php artisan serve --port=8001
  ```
  Then visit `http://127.0.0.1:8001`

### Issue: "npm install fails"
- **Solution**: 
  ```powershell
  npm cache clean --force
  npm install
  ```

### Issue: "Database doesn't exist"
- **Solution**: 
  ```powershell
  php artisan migrate --force
  ```

---

## ✅ VERIFICATION CHECKLIST

- [ ] PHP 8.2+ installed (`php -v`)
- [ ] Composer installed (`composer -v`)
- [ ] Node.js installed (`node -v`)
- [ ] MySQL installed and running
- [ ] Git installed (`git --version`)
- [ ] PlantVerse folder exists
- [ ] `composer install` completed
- [ ] `.env` file configured
- [ ] `php artisan key:generate` completed
- [ ] Database created
- [ ] `php artisan migrate` completed
- [ ] `npm install` completed
- [ ] `npm run build` completed
- [ ] Server running at `http://127.0.0.1:8000`
- [ ] Test user created
- [ ] Successfully logged in
- [ ] Can add/view/edit/delete plants
- [ ] Can add plant journal entries

---

## 📞 NEED HELP?

If you encounter any issues:
1. Check the [TROUBLESHOOTING](#-troubleshooting) section above
2. Review the error message carefully
3. Verify all software versions are correct
4. Try restarting the services

---

## 🎉 SETUP COMPLETE!

You now have a fully functional PlantVerse application running on your PC!

**Next Steps**:
- Explore all 7 features in the [QUICK_TEST.md](QUICK_TEST.md) file
- Read [TESTING_GUIDE.md](TESTING_GUIDE.md) for detailed feature testing
- Customize the application as needed

Happy gardening! 🌿
