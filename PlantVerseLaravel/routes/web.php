<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlantsController;
use App\Http\Controllers\MilestonesController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\PlantCareController;

Route::get('/', function () {
    return redirect()->route('login');
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
        Route::post('/{plantId}/log-care/{taskType}', [PlantsController::class, 'logCare'])->name('plants.log-care');
        Route::post('/{id}/identify', [PlantsController::class, 'identifyPlant'])->name('plants.identify');
    });

    // Plant care advice
    Route::post('/plants/{plantId}/care-advice', [PlantCareController::class, 'getAdvice'])->name('plants.care-advice');

    // Milestones routes
    Route::get('/milestones', [MilestonesController::class, 'index'])->name('milestones.index');

    // Shop routes
    Route::prefix('/shop')->group(function () {
        Route::get('/', [ShopController::class, 'index'])->name('shop.index');
        Route::post('/redeem/{rewardId}', [ShopController::class, 'redeem'])->name('shop.redeem');
    });
});
