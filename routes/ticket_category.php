<?php

use App\Http\Controllers\TicketCategoryController;
use Illuminate\Support\Facades\Route;

// Authenticated routes
Route::middleware(['auth', 'event.edit'])->group(function () {
    // Event routes
    Route::prefix('events/{event}/ticket_category')->group(function () {
        // Ticket Category routes
        Route::middleware(['ticket_category'])->group(function () {
            Route::get('/{ticket_category}/actions', [
                TicketCategoryController::class,
                'actions',
            ])->name('ticket_category.actions');
            Route::post('/{ticket_category}/toggle', [
                TicketCategoryController::class,
                'toggle',
            ])->name('ticket_category.toggle');
            Route::get('/{ticket_category}/edit', [
                TicketCategoryController::class,
                'edit',
            ])->name('ticket_category.edit');
            Route::patch('/{ticket_category}', [
                TicketCategoryController::class,
                'update',
            ])->name('ticket_category.update');
            Route::delete('/{ticket_category}', [
                TicketCategoryController::class,
                'destroy',
            ])->name('ticket_category.destroy');
        });
        Route::get('/create', [
            TicketCategoryController::class,
            'create',
        ])->name('ticket_categories.create');
        Route::post('/create', [
            TicketCategoryController::class,
            'store',
        ])->name('ticket_categories.store');
    });
});
