<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // User routes
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::post('/reports/{report}/cancel', [ReportController::class, 'cancel'])->name('reports.cancel');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Admin routes
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/reports/{report}', [AdminController::class, 'showReport'])->name('reports.show');
        Route::put('/reports/{report}', [AdminController::class, 'updateReport'])->name('reports.update');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/technicians', [AdminController::class, 'technicians'])->name('technicians');
        Route::post('/technicians', [AdminController::class, 'storeTechnician'])->name('technicians.store');
        Route::put('/technicians/{technician}', [AdminController::class, 'updateTechnician'])->name('technicians.update');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    });

    // Technician routes
    Route::middleware(\App\Http\Middleware\TechnicianMiddleware::class)->prefix('technician')->name('technician.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TechnicianController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports/{report}', [\App\Http\Controllers\TechnicianController::class, 'showReport'])->name('reports.show');
        Route::put('/reports/{report}', [\App\Http\Controllers\TechnicianController::class, 'updateReport'])->name('reports.update');
    });
});

// API for getting rooms by building
Route::get('/api/rooms/{building}', function (\App\Models\Building $building) {
    return $building->rooms;
});
