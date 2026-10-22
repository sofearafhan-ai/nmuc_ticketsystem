<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware(['auth'])->group(function () {

    // Logout Process
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route Authentication User, Admin, Super Admin
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/reply', [TicketController::class, 'storeReply'])->name('tickets.reply');

    // Route Admin 
   Route::middleware(['role:Admin,Super Admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/admin/tickets', [AdminController::class, 'tickets'])->name('admin.tickets.index');
    Route::get('/admin/tickets/create', [AdminController::class, 'createTicket'])->name('admin.tickets.create');
    Route::get('/admin/tickets/{id}/edit', [AdminController::class, 'editTicket'])->name('admin.tickets.edit');
    Route::put('/admin/tickets/{id}', [AdminController::class, 'updateTicket'])->name('admin.tickets.update');
    Route::post('/admin/tickets', [AdminController::class, 'storeTicket'])->name('admin.tickets.store');
    Route::delete('/admin/tickets/{id}', [AdminController::class, 'destroyTicket'])->name('admin.tickets.destroy');

    Route::get('/admin/messages', [AdminController::class, 'messages'])->name('admin.messages.index');
    Route::get('/admin/messages/{ticketId}', [AdminController::class, 'showConversation'])->name('admin.messages.show');
    Route::post('/admin/messages/{ticketId}', [AdminController::class, 'replyMessage'])->name('admin.messages.reply');
    
    Route::get('/admin/inventory', [AdminController::class, 'inventory'])->name('admin.inventory.index');
    Route::get('/admin/inventory/create', [AdminController::class, 'createInventory'])->name('admin.inventory.create');
    Route::post('/admin/inventory', [AdminController::class, 'storeInventory'])->name('admin.inventory.store');
    Route::get('/admin/inventory/{id}/edit', [AdminController::class, 'editInventory'])->name('admin.inventory.edit');
    Route::put('/admin/inventory/{id}', [AdminController::class, 'updateInventory'])->name('admin.inventory.update');
    Route::delete('/admin/inventory/{id}', [AdminController::class, 'destroyInventory'])->name('admin.inventory.destroy');

});

    // Route Super Admin 
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('/super-admin/dashboard', function () {
            return view('superadmin.dashboard');
        })->name('superadmin.dashboard');
    });

});