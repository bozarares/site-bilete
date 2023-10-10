<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Get the authenticated user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get user's orders with items
        $orders = $user
            ->orders()
            ->with('items')
            ->get();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }
}
