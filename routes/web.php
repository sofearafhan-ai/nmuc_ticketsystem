<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;

// 1. Laluan untuk Pelawat / Belum Log Masuk (Guest Only)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// 2. Laluan yang WAJIB Log Masuk Dahulu (Protected Routes)
Route::middleware(['auth'])->group(function () {

    // Process Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route Pengguna Biasa / Semua Peranan (Authentication User, Admin, Super Admin)
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/reply', [TicketController::class, 'storeReply'])->name('tickets.reply');

    // Route Admin (Hanya dibenarkan untuk Admin & Super Admin)
    Route::middleware(['role:Admin,Super Admin'])->group(function () {
        // TUKAR BARIS INI: panggil AdminController@dashboard
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/admin/tickets', [AdminController::class, 'tickets'])->name('admin.tickets.index');
        Route::get('/admin/inventory', [AdminController::class, 'inventory'])->name('admin.inventory.index');
        Route::post('/admin/inventory', [AdminController::class, 'storeInventory'])->name('admin.inventory.store');
    });

    // Route Super Admin (Hanya dibenarkan untuk Super Admin sahaja)
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('/super-admin/dashboard', function () {
            return view('superadmin.dashboard');
        })->name('superadmin.dashboard');
    });

});