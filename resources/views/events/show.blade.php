<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Event: {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100 space-y-6">
                    
                    {{-- Judul dan Deskripsi --}}
                    <div>
                        <h3 class="text-3xl font-bold border-b pb-3 mb-4 text-gray-800 dark:text-gray-100">{{ $event->title }}</h3>
                        <p class="text-lg text-gray-600 dark:text-gray-300">{{ $event->description }}</p>
                    </div>

                    {{-- Detail Event dalam Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
                        <div>
                            <strong class="block text-sm font-medium text-gray-500">📍 Lokasi:</strong>
                            <p class="text-lg">{{ $event->location }}</p>
                        </div>
                        <div>
                            <strong class="block text-sm font-medium text-gray-500">🗓️ Tanggal & Waktu:</strong>
                            <p class="text-lg">
                                {{ \Carbon\Carbon::parse($event->date)->isoFormat('dddd, D MMMM Y') }}
                                pukul {{ \Carbon\Carbon::parse($event->time)->format('H:i') }} WIB
                            </p>
                        </div>
                        <div>
                            <strong class="block text-sm font-medium text-gray-500">💰 Harga Tiket:</strong>
                            <p class="text-lg">Rp{{ number_format($event->price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <strong class="block text-sm font-medium text-gray-500">🎟️ Sisa Kuota:</strong>
                            <p class="text-lg">{{ $event->remainingQuota() }} / {{ $event->quota }}</p>
                        </div>
                    </div>

                    {{-- Form Booking --}}
                    <div class="border-t pt-6">
                        <form action="{{ route('bookings.store', $event->id) }}" method="POST">
                            @csrf
                            <div class="flex items-center gap-4">
                                <label for="quantity" class="font-semibold">Jumlah Tiket:</label>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $event->remainingQuota() }}" class="w-20 rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900">
                                    Beli Tiket
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Tombol Navigasi --}}
                    <div class="border-t pt-6 flex items-center justify-between">
                        <a href="{{ route('events.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                            &larr; Kembali ke Semua Event
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>