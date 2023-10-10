<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::get('/events/all', [EventController::class, 'all']);
Route::get('/events/types', [EventController::class, 'types']);

// Public routes
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name(
    'events.show'
);

// Authenticated routes
Route::middleware(['auth', 'event.edit'])->group(function () {
    Route::prefix('events')->group(function () {
        Route::get('/{event}/edit', [EventController::class, 'edit'])->name(
            'events.edit'
        );
        Route::patch('/{event}', [EventController::class, 'update'])->name(
            'events.update'
        );
    });
});
