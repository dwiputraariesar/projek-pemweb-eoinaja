@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">My Bookings</h1>
    </div>

    @if($bookings->isEmpty())
        <div class="bg-white p-4 rounded shadow">Belum ada booking.</div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($bookings as $b)
                <x-card>
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Event</div>
                            <div class="font-semibold">{{ $b->event->title }}</div>
                            <div class="text-sm text-gray-500 mt-2">Tiket: {{ $b->ticket->name }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold">x{{ $b->quantity }}</div>
                            <div class="mt-2"><x-badge color="blue">{{ ucfirst($b->status) }}</x-badge></div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <a href="{{ route('bookings.show', $b) }}"><x-button variant="secondary">View</x-button></a>
                        <div class="space-x-2">
                            @if($b->status === 'pending')
                                <form action="{{ route('bookings.pay', $b) }}" method="POST" class="inline">
                                    @csrf
                                    <x-button variant="success">Pay</x-button>
                                </form>
                            @endif

                            @if($b->status === 'paid' && $b->qr_code)
                                <a href="{{ asset('storage/'.$b->qr_code) }}"><x-button variant="primary">Download QR</x-button></a>
                            @endif
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</div>
@endsection
