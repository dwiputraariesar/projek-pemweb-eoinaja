<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || ! method_exists($user, 'hasRole') || ! $user->hasRole('Administrator')) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $transactions = Transaction::with('booking')->latest()->paginate(20);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('booking');
        return view('admin.transactions.show', compact('transaction'));
    }
}
