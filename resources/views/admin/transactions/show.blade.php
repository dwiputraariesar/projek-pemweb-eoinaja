@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Transaction #{{ $transaction->id }}</h1>

    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Booking</p>
                <div class="font-medium">#{{ $transaction->booking_id }}</div>

                <p class="mt-3 text-sm text-gray-500">Provider</p>
                <div class="font-medium">{{ ucfirst($transaction->provider) }}</div>
            </div>

            <div>
                <p class="text-sm text-gray-500">Amount</p>
                <div class="text-lg font-semibold">Rp {{ number_format($transaction->amount,2) }}</div>

                <p class="mt-3 text-sm text-gray-500">Status</p>
                <div class="mt-1"><x-badge color="green">{{ ucfirst($transaction->status) }}</x-badge></div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.transactions.index') }}"><x-button variant="secondary">Back</x-button></a>
        </div>
    </x-card>
</div>
@endsection
