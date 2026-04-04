<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlantsController;
use App\Http\Controllers\MilestonesController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\PlantCareController;

/**
 * Root Route
 * Conditionally redirects authenticated users to dashboard and unauthenticated users to login.
 * This provides better UX by automatically routing users to their intended destination.
 */
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

// Guest routes (Authentication)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Protected routes (Require Authentication)
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Plants routes
    Route::prefix('/my-plants')->group(function () {
        Route::get('/', [PlantsController::class, 'index'])->name('plants.index');
        Route::get('/add', [PlantsController::class, 'create'])->name('plants.create');
        Route::post('/', [PlantsController::class, 'store'])->name('plants.store');
        Route::get('/{id}', [PlantsController::class, 'show'])->name('plants.show');

        /**
         * PLANT CRUD OPERATIONS
         * 
         * Protected with ownership verification in controller methods.
         * Users can only edit/update/delete their own plants.
         */
        Route::get('/{id}/edit', [PlantsController::class, 'edit'])->name('plants.edit');
        Route::put('/{id}', [PlantsController::class, 'update'])->name('plants.update');
        Route::delete('/{id}', [PlantsController::class, 'destroy'])->name('plants.destroy');

        Route::post('/{plantId}/log-care/{taskType}', [PlantsController::class, 'logCare'])->name('plants.log-care');
        Route::post('/{id}/identify', [PlantsController::class, 'identifyPlant'])->name('plants.identify');

        /**
         * PLANT JOURNAL OPERATIONS
         * 
         * Add growth photos and notes to document a plant's progress over time.
         * Protected with ownership verification in controller methods.
         */
        Route::post('/{plantId}/journal', [PlantsController::class, 'storeJournal'])->name('plants.journal.store');

        /**
         * PLANT CARE SCHEDULE CUSTOMIZATION
         * 
         * Customize care frequency for each plant based on species-specific requirements.
         * Users can research and adjust Water, Sunlight, and Fertilize schedules.
         * Protected with ownership verification in controller methods.
         */
        Route::get('/{id}/care-schedule', [PlantsController::class, 'editCareSchedule'])->name('plants.care-schedule.edit');
        Route::put('/{id}/care-schedule', [PlantsController::class, 'updateCareSchedule'])->name('plants.care-schedule.update');
    });

    // Plant care advice
    Route::post('/plants/{plantId}/care-advice', [PlantCareController::class, 'getAdvice'])->name('plants.care-advice');

    // Milestones routes
    Route::get('/milestones', [MilestonesController::class, 'index'])->name('milestones.index');

    // Shop routes
    Route::prefix('/shop')->group(function () {
        Route::get('/', [ShopController::class, 'index'])->name('shop.index');
        Route::post('/redeem/{rewardId}', [ShopController::class, 'redeem'])->name('shop.redeem');

        /**
         * ADMIN ROUTES
         * 
         * Protected by admin middleware to ensure only administrators can access shop management.
         * The middleware checks the user's is_admin database column.
         */
        Route::middleware('admin')->group(function () {
            Route::get('/{rewardId}/edit', [ShopController::class, 'edit'])->name('shop.edit');
            Route::put('/{rewardId}', [ShopController::class, 'update'])->name('shop.update');
        });
    });
});
