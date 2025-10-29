<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Tiket: {{ $booking->event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Kolom Kiri: QR Code & Detail Booking -->
                        <div class="text-center">
                            <h3 class="text-lg font-bold mb-2">Pindai QR Code Ini di Pintu Masuk</h3>
                            @if($booking->status == 'paid' || $booking->status == 'success')
                                <div class="flex justify-center p-4 bg-white rounded-lg">
                                    <img src="{{ asset('storage/' . $booking->qr_code_path) }}" alt="QR Code Tiket">
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Kode Booking: BOOKING-{{ $booking->id }}</p>
                            @else
                                <div class="flex justify-center items-center p-4 bg-gray-100 dark:bg-gray-700 rounded-lg h-48">
                                    <p class="text-gray-500">Selesaikan pembayaran untuk melihat QR Code.</p>
                                </div>
                            @endif
                            <div class="mt-6 text-left border-t pt-4 dark:border-gray-700">
                                <p><span class="font-semibold">Status:</span>
                                     <span class="font-bold
                                            @if($booking->status == 'paid' || $booking->status == 'success') text-green-600
                                            @elseif($booking->status == 'pending') text-yellow-500
                                            @else text-red-500 @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                </p>
                                <p><span class="font-semibold">Jumlah Tiket:</span> {{ $booking->quantity }}</p>
                                <p><span class="font-semibold">Total Harga:</span> Rp{{ number_format($booking->total_price, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Detail Event -->
                        <div class="border-t md:border-t-0 md:border-l pt-6 md:pt-0 md:pl-8 dark:border-gray-700">
                            <h3 class="text-xl font-bold mb-4">{{ $booking->event->title }}</h3>
                            <div class="space-y-3 text-gray-700 dark:text-gray-300">
                                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->event->date)->format('l, d F Y') }}</p>
                                <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->event->time)->format('H:i') }} WIB</p>
                                <p><strong>Lokasi:</strong> {{ $booking->event->location }}</p>
                                <div class="prose dark:prose-invert max-w-none">
                                    <p><strong>Deskripsi:</strong><br>{{ $booking->event->description }}</p>
                                </div>
                            </div>

                             <!-- Tombol Aksi -->
                            <div class="mt-8 border-t pt-4 dark:border-gray-700">
                                @if($booking->status != 'cancelled' && $booking->event->date >= now())
                                <form action="{{ route('tickets.cancel', $booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                        Batalkan Booking
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('tickets.index') }}" class="mt-4 block text-center w-full bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                    Kembali ke Daftar Tiket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
