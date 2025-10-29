<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class PaymentController extends Controller
{
    /**
     * Create a Stripe Checkout session for a booking and redirect the user.
     */
    public function checkout(Request $request, Booking $booking)
    {
        $user = $request->user();
        if (!$user || $user->getKey() !== $booking->user_id) {
            abort(403);
        }
        $session = $this->createStripeCheckoutSession($booking);
        return redirect($session->url);
    }

    /**
     * Build and create a Stripe Checkout Session for the given booking.
     */
    private function createStripeCheckoutSession(Booking $booking)
    {
        // set API key once inside helper
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $sessionData = $this->buildStripeSessionData($booking);

        return \Stripe\Checkout\Session::create($sessionData);
    }

    /**
     * Build the array payload for Stripe Checkout Session creation.
     */
    private function buildStripeSessionData(Booking $booking): array
    {
        $unitPrice = $booking->ticket ? $booking->ticket->price : $booking->event->price;
        $unitAmount = (int) round($unitPrice * 100);
        $currency = env('CURRENCY', 'usd');

        return [
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => ['name' => $booking->event->title],
                        'unit_amount' => $unitAmount,
                    ],
                    'quantity' => $booking->quantity,
                ]
            ],
            'success_url' => route('bookings.show', $booking) . '?paid=1',
            'cancel_url' => route('bookings.show', $booking) . '?canceled=1',
            'metadata' => ['booking_id' => $booking->getKey()],
        ];
    }
}
