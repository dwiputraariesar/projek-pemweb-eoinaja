@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Booking #{{ $booking->id }}</h1>

    <x-card class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Event</p>
                <h2 class="text-lg font-semibold">{{ $booking->event->title }}</h2>

                <p class="mt-2 text-sm text-gray-500">Tipe Tiket</p>
                <div class="text-base">{{ $booking->ticket->name }}</div>

                <div class="mt-3">
                    <p class="text-sm text-gray-500">Jumlah</p>
                    <div class="font-medium">{{ $booking->quantity }}</div>
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status</p>
                <div class="mt-1">
                    <x-badge color="blue">{{ ucfirst($booking->status) }}</x-badge>
                </div>

                @if($booking->status === 'paid' && $booking->qr_code)
                    <div class="mt-6">
                        <h3 class="font-semibold">Your QR Ticket</h3>
                        <div class="mt-3">
                            <object type="image/svg+xml" data="{{ asset('storage/'.$booking->qr_code) }}" class="w-48 h-48 md:w-64 md:h-64">Your browser does not support SVG</object>
                        </div>
                        <div class="mt-2">
                            <a href="{{ asset('storage/'.$booking->qr_code) }}" class="inline-block mr-3"><x-button variant="secondary">Download SVG</x-button></a>
                            <a href="{{ route('tickets.download', $booking) ?? asset('storage/'.$booking->qr_code) }}"><x-button variant="primary">Download PDF</x-button></a>
                        </div>
                    </div>
                @endif

                <div class="mt-6 space-y-2">
                    @if(auth()->id() === $booking->user_id && $booking->status === 'pending')
                        <form method="POST" action="{{ route('bookings.pay', $booking) }}">
                            @csrf
                            <x-button variant="success">Pay (Mock)</x-button>
                        </form>
                    @endif

                    @if((auth()->user()?->hasRole('Administrator') ?? false) || (auth()->id() === $booking->event->organizer_id))
                        <form method="POST" action="{{ route('admin.bookings.checkin', $booking) }}">
                            @csrf
                            <x-button variant="primary">Check-in Attendee</x-button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </x-card>
</div>
@endsection
