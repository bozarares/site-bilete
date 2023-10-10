<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'full_name' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required',
            'gdpr' => 'required|accepted',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Load the user's cart with items and ticket categories
        $cart = $user->cart->load('items.ticketCategory');

        // If the cart is empty, return an error
        if ($cart->items->isEmpty()) {
            return redirect()
                ->back()
                ->with(
                    'message',
                    json_encode([
                        'title' => 'Cart notification',
                        'content' => 'Cart is empty',
                        'status' => 'danger',
                    ])
                );
        }

        // Create a new Stripe client
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

        $lineItems = [];

        // Create the line items for the Stripe checkout session from the cart items
        foreach ($cart->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'description' =>
                            'Category: ' . $item->ticketCategory->name,
                        'name' =>
                            'Event: ' . $item->ticketCategory->event->title,
                    ],
                    'unit_amount_decimal' => $item->ticketCategory->price * 100,
                ],
                'quantity' => $item->quantity,
            ];
        }

        // Create the Stripe checkout session
        try {
            $checkout_session = $stripe->checkout->sessions->create([
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $request->email,
                'payment_method_types' => ['card'],
                'success_url' =>
                    route('checkout.success', [], true) .
                    '?checkout_session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.failure', [], true),
                'metadata' => [
                    'user_id' => $user->uuid,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'cart_id' => $cart->id,
                ],
            ]);

            // Redirect the user to the Stripe checkout page
            return Inertia::location($checkout_session->url);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withErrors(['stripe_error' => 'Error initializing payment.']);
        }
    }

    public function success(Request $request)
    {
        // Set the Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        // Retrieve the Stripe checkout session
        try {
            $session = Session::retrieve($request->get('checkout_session_id'));

            return Inertia::render('Checkout/Success');
        } catch (\Exception $e) {
            Log::error('Stripe Success Error: ' . $e->getMessage());
            return redirect()->route('checkout.failure');
        }
    }

    public function failure()
    {
        // Render the failure page
        return Inertia::render('Checkout/Failure');
    }

    // Handle the Stripe webhook
    public function webhook()
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        // Constuct the event object from the payload and the signature header
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                env('STRIPE_WEBHOOK_SECRET_KEY')
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('', 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response('', 400);
        }

        // Handle the event based on the type
        switch ($event->type) {
            case 'checkout.session.completed':
                // Payment is successful and the checkout session is complete
                $session = $event->data->object;

                // Retrieve the payment intent
                $paymentIntent = PaymentIntent::retrieve(
                    $session['payment_intent'],
                    ['api_key' => env('STRIPE_SECRET_KEY')]
                );

                // Handle the payment success
                $this->handlePaymentSuccess($session, $paymentIntent->id);
            default:
                // Unexpected event type
                echo 'Received unknown event type ' . $event->type;
        }

        return response('', 200);
    }

    protected function handlePaymentSuccess($session, $id)
    {
        // Log the event
        Log::info('Handling payment success for session ID: ' . $session->id);

        // Begin a database transaction
        DB::beginTransaction();

        // Create the order and tickets
        try {
            $user_uuid = $session->metadata->user_id;
            $cart_id = $session->metadata->cart_id;

            // Get the cart and user
            $cart = Cart::find($cart_id)->load('items.ticketCategory');
            if (!$cart || $cart->items->isEmpty()) {
                throw new \Exception('Cart not found or empty');
            }

            $user = User::find($user_uuid);
            if (!$user) {
                throw new \Exception('User not found');
            }

            $full_name = $session->metadata->full_name;

            // Create the order
            $order = Order::create([
                'checkout_session_id' => $session->id,
                'checkout_id' => $id,
                'user_uuid' => $user_uuid,
                'name' => $full_name,
                'email' => $session->metadata->email,
                'phone' => $session->metadata->phone,
                'address' => $session->metadata->address,
                'price' => $cart->getTotalPriceAttribute(),
            ]);

            // Loop through the cart items and create the tickets and order items
            foreach ($cart->items as $item) {
                for ($i = 0; $i < $item->quantity; $i++) {
                    Ticket::create([
                        'ticket_category_id' => $item->ticketCategory->id,
                        'user_uuid' => $user_uuid,
                        'price' => $item->ticketCategory->price,
                        'name' => $full_name,
                        'validated' => false,
                        'validated_by_uuid' => '',
                    ]);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'price' => $item->ticketCategory->price,
                    'quantity' => $item->quantity,
                    'name' => $item->ticketCategory->name,
                    'event_name' => $item->ticketCategory->event->title,
                ]);
            }

            // Delete the cart
            $cart->delete();

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            try {
                // Refund the payment if the transaction fails
                Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

                $refund = Refund::create([
                    'payment_intent' => $id,
                ]);

                Log::info('Refund issued: ' . $refund->id);
            } catch (ApiErrorException $e) {
                Log::error('Stripe Refund Error: ' . $e->getMessage());
            } catch (\Exception $e) {
                Log::error('Refund Error: ' . $e->getMessage());
            }
            Log::error('Payment Handling Error: ' . $e->getMessage());
        }
        Log::info('Payment success handled without exceptions.');
    }
}
