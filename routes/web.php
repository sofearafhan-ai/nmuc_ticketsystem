<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;

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

        Route::get('/admin/tickets/export', [AdminController::class, 'exportTickets'])->name('admin.tickets.export');

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

        Route::get('/admin/inventory/export', [AdminController::class, 'exportInventory'])->name('admin.inventory.export');

        Route::get('/admin/inventory/create', [AdminController::class, 'createInventory'])->name('admin.inventory.create');
        Route::post('/admin/inventory', [AdminController::class, 'storeInventory'])->name('admin.inventory.store');
        Route::get('/admin/inventory/{id}/edit', [AdminController::class, 'editInventory'])->name('admin.inventory.edit');
        Route::put('/admin/inventory/{id}', [AdminController::class, 'updateInventory'])->name('admin.inventory.update');
        Route::delete('/admin/inventory/{id}', [AdminController::class, 'destroyInventory'])->name('admin.inventory.destroy');
        Route::delete('/admin/inventory/{id}/attachment', [AdminController::class, 'deleteAttachment'])->name('admin.inventory.delete-attachment');
    });

    // Route Super Admin 
    Route::middleware(['role:Super Admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        // Dashboard, Users & Departments
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [SuperAdminController::class, 'usersIndex'])->name('users.index');
        Route::get('/users/create', [SuperAdminController::class, 'usersCreate'])->name('users.create');
        Route::post('/users', [SuperAdminController::class, 'usersStore'])->name('users.store');
        Route::delete('/users/{id}', [SuperAdminController::class, 'usersDestroy'])->name('users.destroy');
        
        Route::get('/departments', [SuperAdminController::class, 'departmentsIndex'])->name('departments.index');
        Route::post('/departments', [SuperAdminController::class, 'departmentsStore'])->name('departments.store');
        Route::delete('/departments/{id}', [SuperAdminController::class, 'departmentsDestroy'])->name('departments.destroy');

        // TICKETS FOR SUPER ADMIN 
        Route::get('/tickets', [SuperAdminController::class, 'ticketsIndex'])->name('tickets.index');
        Route::get('/tickets/create', [SuperAdminController::class, 'ticketsCreate'])->name('tickets.create');
        Route::post('/tickets', [SuperAdminController::class, 'ticketsStore'])->name('tickets.store');
        Route::get('/tickets/{id}/edit', [SuperAdminController::class, 'ticketsEdit'])->name('tickets.edit');
        Route::put('/tickets/{id}', [SuperAdminController::class, 'ticketsUpdate'])->name('tickets.update');
        Route::get('/messages', [SuperAdminController::class, 'messagesIndex'])->name('messages.index');
        Route::post('/messages/{ticket_id}/reply', [SuperAdminController::class, 'messagesReply'])->name('messages.reply');

        Route::delete('/tickets/{id}', [SuperAdminController::class, 'ticketsDestroy'])->name('tickets.destroy');
        
        // INVENTORY FOR SUPER ADMIN 
        Route::get('/inventory', [SuperAdminController::class, 'inventoryIndex'])->name('inventory.index');
        Route::get('/inventory/create', [SuperAdminController::class, 'inventoryCreate'])->name('inventory.create');
        Route::post('/inventory', [SuperAdminController::class, 'inventoryStore'])->name('inventory.store');
        Route::get('/inventory/{id}/edit', [SuperAdminController::class, 'inventoryEdit'])->name('inventory.edit');
        Route::put('/inventory/{id}', [SuperAdminController::class, 'inventoryUpdate'])->name('inventory.update');
        Route::delete('/inventory/{id}', [SuperAdminController::class, 'inventoryDestroy'])->name('inventory.destroy');
    });

});