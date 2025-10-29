@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">My Transactions</h1>
    </div>

    @if($transactions->isEmpty())
        <x-card>Belum ada transaksi.</x-card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($transactions as $t)
                <x-card>
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-sm text-gray-500">Event</div>
                            <div class="font-medium">{{ $t->booking->event->title }}</div>
                            <div class="text-sm text-gray-500 mt-2">Booking #{{ $t->booking_id }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold">Rp {{ number_format($t->amount,2) }}</div>
                            <div class="mt-2"><x-badge color="green">{{ ucfirst($t->status) }}</x-badge></div>
                        </div>
                    </div>

                    <div class="mt-4 text-right">
                        <a href="{{ route('transactions.show', $t) }}"><x-button variant="secondary">View</x-button></a>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-4">{{ $transactions->links() }}</div>
    @endif
</div>
@endsection
