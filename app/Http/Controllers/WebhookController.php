<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handlePayment(Request $request)
    {
        // Log webhook received
        Log::info('Webhook received', $request->all());

        // Verify webhook signature
        $signature = $request->header('X-Webhook-Signature');
        $webhookSecret = config('services.payment.webhook_secret');

        // Get payload and calculate expected signature
        $payload = $request->all();
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $webhookSecret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Invalid webhook signature', [
                'expected' => $expectedSignature,
                'received' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Process webhook
        $event = $request->input('event');
        $data = $request->input('data');

        if ($event === 'payment.success') {
            $externalId = $data['external_id'];

            $order = Transaction::where('order_number', $externalId)->first();

            if (!$order) {
                Log::warning('Order not found', ['external_id' => $externalId]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Check if already processed (idempotency)
            if ($order->isPaid()) {
                Log::info('Payment already processed', ['order_id' => $order->id]);
                return response()->json(['message' => 'Already processed'], 200);
            }

            // Update order status
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Reduce product stock
            $product = $order->product;
            $product->decrement('stock', $order->quantity);

            Log::info('Payment success processed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $order->total_amount,
            ]);

            return response()->json(['message' => 'Webhook processed successfully'], 200);
        }

        if ($event === 'payment.failed') {
            $externalId = $data['external_id'];

            $order = Transaction::where('order_number', $externalId)->first();

            if ($order) {
                $order->update(['payment_status' => 'failed']);
                Log::info('Payment failed processed', ['order_id' => $order->id]);
            }

            return response()->json(['message' => 'Webhook processed'], 200);
        }

        if ($event === 'payment.expired') {
            $externalId = $data['external_id'];

            $order = Transaction::where('order_number', $externalId)->first();

            if ($order) {
                $order->update(['payment_status' => 'expired']);
                Log::info('Payment expired processed', ['order_id' => $order->id]);
            }

            return response()->json(['message' => 'Webhook processed'], 200);
        }

        return response()->json(['message' => 'Event not handled'], 200);
    }
}