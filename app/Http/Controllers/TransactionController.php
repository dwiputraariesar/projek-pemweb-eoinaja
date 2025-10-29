<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $transactions = Transaction::with('booking')->whereHas('booking', function ($q) {
            $q->where('user_id', auth()->id());
        })->latest()->paginate(12);

        return view('transactions.index', compact('transactions'));
    }
    public function process(Ticket $product)
    {
        // if ($product->stock <= 0) {
        //     return redirect()->route('customer.products.index')
        //         ->with('error', 'Produk tidak tersedia.');
        // }

        $expiredHours = (int) config('services.payment.expired_hours', 24);

        // Create order
        $order = Transaction::create([
            'booking_id' => auth()->id(),
            'amount' => 1,
            'provider' => null,
            'metadata' => null,
            'status' => 'pending',
        ]);        

        // Create Virtual Account via Payment Gateway API
        try {
            $response = Http::withHeaders([
                'X-API-Key' => config('services.payment.api_key'),
                'Accept' => 'application/json',
            ])->withoutVerifying()->post(config('services.payment.base_url') . '/virtual-account/create', [
                'external_id' => $order->booking_id,
                'amount' => $order->amount,
                'customer_name' => auth()->user()->name,
                'customer_email' => auth()->user()->email,
                'customer_phone' => auth()->user()->phone ?? '081234567890',
                'description' => 'Pembayaran ' . $product->name,
                'expired_duration' => $expiredHours,
                // 'callback_url' => route('orders.success', $order),
                'metadata' => [
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $order->update([
                    'booking_id' => $data['data']['va_number'],
                    'metadata' => [ // <-- 'metadata' adalah array
                    'payment_url' => $data['data']['payment_url'],
                    ]
                ]);

                return redirect()->back();
            } else {
            $order->update(['status' => 'failed']);
            
            // Mengembalikan respons error sebagai JSON
            return response()->json([
                'error' => 'Gagal membuat pembayaran. Silakan coba lagi.'
            ], 422); // 422: Unprocessable Entity
        }
            } catch (\Exception $e) {
            $order->update(['status' => 'failed']);

            // Mengembalikan respons error sebagai JSON
            return response()->json([
            'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500); // 500: Internal Server Error
        }
    }
    public function show(Transaction $transaction)
    {
        // Ensure the transaction belongs to the authenticated user
        if ($transaction->booking->user_id !== auth()->id()) {
            abort(403);
        }

        $transaction->load('booking.event');
        return view('transactions.show', compact('transaction'));
    }
}
