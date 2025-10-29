<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

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
