<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Transaction;
use App\Jobs\SendTicketPdfJob;

class WebhookController extends Controller
{
    /**
     * Handle incoming Stripe webhook events.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $bookingId = null;
            if (is_object($session->metadata)) {
                $bookingId = $session->metadata->booking_id ?? null;
            } elseif (is_array($session->metadata)) {
                $bookingId = $session->metadata['booking_id'] ?? null;
            }

            if ($bookingId) {
                $booking = Booking::find($bookingId);
                if ($booking && !$booking->transaction_id) {
                    $amount = 0;
                    if (isset($session->amount_total)) {
                        $amount = $session->amount_total / 100;
                    }

                    $transaction = Transaction::create([
                        'booking_id' => $booking->getKey(),
                        'provider' => 'stripe',
                        'amount' => $amount,
                        'status' => 'paid',
                        'metadata' => ['session_id' => $session->id, 'payment_intent' => $session->payment_intent ?? null],
                    ]);

                    $booking->transaction_id = $transaction->getKey();
                    $booking->status = 'paid';
                    $booking->save();

                    SendTicketPdfJob::dispatch($booking);
                }
            }
        }

        return response('OK', 200);
    }
}
