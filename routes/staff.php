<?php

use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

// Authenticated routes
Route::middleware(['auth', 'event.edit'])->group(function () {
    // Staff routes
    Route::prefix('{event}/staff')->group(function () {
        Route::get('/create', [StaffController::class, 'create'])->name(
            'staff.create'
        );
        Route::post('/', [StaffController::class, 'store'])->name(
            'staff.store'
        );
        Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name(
            'staff.edit'
        );
        Route::patch('/{staff}', [StaffController::class, 'update'])->name(
            'staff.update'
        );
        Route::delete('/{staff}', [StaffController::class, 'destroy'])->name(
            'staff.destroy'
        );
    });
});
