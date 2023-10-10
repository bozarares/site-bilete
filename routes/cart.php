<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use Illuminate\Support\Facades\Route;

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'addToCart'])->name(
        'cart.add'
    );
    Route::put('/cart', [CartItemController::class, 'editCartItem'])->name(
        'cart.edit'
    );
    Route::delete('/cart', [CartItemController::class, 'deleteCartItem'])->name(
        'cart.delete'
    );
});
