<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{
    public function editCartItem(Request $request)
    {
        // Validate the request
        $request->validate([
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        // Get the authenticated user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get the user's cart
        $cart = $user->cart;
        if (!$cart) {
            return response()->json(['message' => 'No cart found for the user'], 404);
        }

        // Get the cart item
        $cartItem = $cart->items()->find($request->cart_item_id);
        if (!$cartItem) {
            return response()->json(['message' => 'Item-ul nu a fost găsit'], 404);
        }

        // Check if the cart item belongs to the user's cart
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['message' => 'Unauthorized access'], 403);
        }

        // Update the cart item
        $cartItem->update(['quantity' => $request->quantity]);
        $user->refresh();

        // Return a success response
        return response()->json(['message' => 'Cart Item updated successfully', 'cart_item_count' => $user->getCartItemCount()]);
    }

    public function deleteCartItem(Request $request)
    {
        // Validate the request
        $request->validate([
            'cart_item_id' => 'required|integer',
        ]);

        // Get the authenticated user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get the user's cart
        $cart = $user->cart;
        if (!$cart) {
            return response()->json(['message' => 'No cart founde for the user'], 404);
        }

        // Get the cart item
        $cartItem = $cart->items()->find($request->cart_item_id);

        // Check if the cart item belongs to the user's cart
        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        // Delete the cart item and refresh the user
        $cartItem->delete();
        $user->refresh();

        // Return a success response
        return response()->json(['message' => 'Cart item deleted successfully', 'cart_item_count' => $user->getCartItemCount()]);
    }
}
