<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Public routes
Route::get('/', WelcomeController::class)->name('welcome');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'events' => Auth::user()->events,
            'staffedEvents' => Auth::user()->staffs,
            'tickets' => Auth::user()->tickets,
        ]);
    })->name('dashboard');

    // Event routes
    require __DIR__ . '/events.php';

    // Ticket category routes
    require __DIR__ . '/ticket_category.php';

    // Ticket routes
    require __DIR__ . '/tickets.php';

    // Cart routes
    require __DIR__ . '/cart.php';

    // Checkout routes
    require __DIR__ . '/checkout.php';

    // Profile routes
    require __DIR__ . '/profile.php';

    // Ticket validation routes
    require __DIR__ . '/ticket_validate.php';

    // Staff routes
    require __DIR__ . '/staff.php';
});

// Authentication routes
require __DIR__ . '/auth.php';

Route::post('/webhook', [PaymentController::class, 'webhook'])->name(
    'checkout.webhook'
);

// No access fallback route
Route::get('/no_access', function () {
    return Inertia::render('Fallbacks/NoAccess');
})->name('no_access');

// Fallback route
Route::fallback(function () {
    return Inertia::render('Fallbacks/Fallback');
});
