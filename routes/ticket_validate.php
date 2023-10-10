<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Authenticated routes
Route::middleware(['auth', 'can.validate'])->group(function () {
    // Ticket validation routes
    Route::get('events/{event}/ticket/{ticket:uuid}/validate', [
        TicketController::class,
        'showValidateTicket',
    ])->name('ticket_validate.show');
    Route::patch('events/{event}/ticket/{ticket:uuid}/validate', [
        TicketController::class,
        'validateTicket',
    ])->name('ticket.validate');

    // Event validation route
    Route::get('/events/{event}/validate', [
        EventController::class,
        'validateTicket',
    ])->name('event.validate');
});
