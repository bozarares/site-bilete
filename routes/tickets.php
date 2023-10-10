<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Authenticated routes for ticket owners
Route::middleware(['auth', 'ticket.owner'])->group(function () {
    Route::prefix('tickets/{ticket:uuid}')->group(function () {
        Route::get('/', [TicketController::class, 'show'])->name(
            'tickets.show'
        );
        Route::get('/edit', [TicketController::class, 'edit'])->name(
            'tickets.edit'
        );
        Route::patch('/update', [TicketController::class, 'update'])->name(
            'tickets.update'
        );
    });
});

// Authenticated route for ticket index
Route::middleware('auth')->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name(
        'tickets.index'
    );
});
