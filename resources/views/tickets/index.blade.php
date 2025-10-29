<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tiket Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                         <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($bookings->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400 text-lg">Anda belum memiliki tiket event apapun.</p>
                            <a href="{{ route('events.index') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Jelajahi Event
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($bookings as $booking)
                                <div class="border rounded-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center dark:border-gray-700">
                                    <div>
                                        <h3 class="text-lg font-bold">{{ $booking->event->title }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($booking->event->date)->format('l, d F Y') }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Jumlah: {{ $booking->quantity }} tiket
                                        </p>
                                    </div>
                                    <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($booking->status == 'paid' || $booking->status == 'success') bg-green-200 text-green-800
                                            @elseif($booking->status == 'pending') bg-yellow-200 text-yellow-800
                                            @else bg-red-200 text-red-800 @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                        <a href="{{ route('tickets.show', $booking) }}" class="inline-block bg-gray-600 text-white px-3 py-1 rounded-md text-sm hover:bg-gray-700">
                                            Lihat Tiket
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
