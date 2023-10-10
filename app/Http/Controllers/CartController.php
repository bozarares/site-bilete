<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Vinkla\Hashids\Facades\Hashids;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        // Validate the request
        $request->validate([
            'ticket_category_id' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        // Get the authenticated user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Decode the ticket category ID
        $ticketCategoryId =
            Hashids::connection('ticket_category')->decode(
                $request->ticket_category_id
            )[0] ?? null;

        // If the ticket category ID is invalid or could not be decoded, return an error
        if (!$ticketCategoryId) {
            return response()->json(
                ['message' => 'Invalid ticket category ID'],
                400
            );
        }

        // Get the user's cart, or create a new one if it doesn't exist
        $cart = $user->cart ?? $user->cart()->create();

        // Get the ticket category
        $ticketCategory = TicketCategory::find($ticketCategoryId);

        // If the ticket category doesn't exist, return an error
        if (!$ticketCategory) {
            return response()->json(
                ['message' => 'Ticket category not found'],
                404
            );
        }

        $remainingTickets =
            $ticketCategory->total_tickets -
            $ticketCategory->getBoughtTickets();

        // If the ticket category is paused or sold out, return an error
        if ($ticketCategory->paused || $remainingTickets == 0) {
            return response()->json(
                ['message' => 'Ticket category is unavailable'],
                403
            );
        }

        // Get the cart item
        $cartItem = $cart
            ->items()
            ->where('ticket_category_id', $ticketCategoryId)
            ->first();

        // If the cart item doesn't exist, create a new one
        if (!$cartItem) {
            $cartItem = $cart->items()->create([
                'ticket_category_id' => $ticketCategoryId,
                'quantity' => 0,
            ]);
        }

        if ($cartItem->quantity + $request->quantity > $remainingTickets) {
            return response()->json(
                ['message' => 'Not enough tickets available'],
                403
            );
        }

        // Increment the quantity of the cart item
        $cartItem->increment('quantity', $request->quantity);

        // Refresh the user
        $user->refresh();

        // Return a success message
        $cartItemCount = $user->getCartItemCount();
        return response()->json([
            'message' => 'Item added to cart',
            'cart_item_count' => $cartItemCount,
        ]);
    }

    public function index()
    {
        // Get the authenticated user and their cart
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $user->cart;

        // If the user doesn't have a cart, render the cart page with a null cart object
        if (!$cart) {
            return Inertia::render('Cart/Index', ['cart' => null]);
        }

        // Otherwise, create a CartResource object from the cart and render the cart page with it
        $cartResource = new CartResource($cart);
        return Inertia::render('Cart/Index', ['cart' => $cartResource]);
    }
}
