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
                    <x-card>
                        <div>
                            <h3 class="text-3xl font-bold border-b pb-3 mb-4 text-gray-800 dark:text-gray-100">{{ $event->title }}</h3>
                            <p class="text-lg text-gray-600 dark:text-gray-300">{{ $event->description }}</p>
                        </div>
                    </x-card>

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
                        <h3 class="text-lg font-semibold mb-2">Pilih Jenis Tiket</h3>
                        @if($event->tickets->isEmpty())
                            <p class="text-gray-600">Belum ada tipe tiket dibuat untuk event ini.</p>
                        @else
                            <form action="{{ route('events.book', $event->id) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label for="ticket_id" class="block text-sm font-medium text-gray-700">Tipe Tiket</label>
                                        <select name="ticket_id" id="ticket_id" class="mt-1 block w-full rounded-md border-gray-300">
                                            @foreach($event->tickets as $ticket)
                                                <option value="{{ $ticket->id }}">{{ $ticket->name }} — Rp{{ number_format($ticket->price,0,',','.') }} (sisa {{ $ticket->stock }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="quantity" class="block text-sm font-medium">Jumlah</label>
                                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $event->remainingQuota() }}" class="mt-1 block w-full rounded-md border-gray-300">
                                    </div>

                                    <div class="flex items-end">
                                        <x-button variant="success" type="submit">Beli Tiket</x-button>
                                    </div>
                                </div>
                            </form>
                        @endif
                        
                        {{-- Ticket management for organizer/admin --}}
                        @can('update', $event)
                            <div class="mt-6 border-t pt-4">
                                <h4 class="font-semibold">Tambah Tipe Tiket</h4>
                                <form action="{{ route('events.tickets.store', $event->id) }}" method="POST" class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-3">
                                    @csrf
                                    <input type="text" name="name" placeholder="Nama tiket" class="rounded border-gray-300 p-2" required>
                                    <input type="number" name="price" placeholder="Harga (Rp)" class="rounded border-gray-300 p-2" min="0" required>
                                    <input type="number" name="stock" placeholder="Stok" class="rounded border-gray-300 p-2" min="0" required>
                                    <div class="md:col-span-3">
                                        <x-button class="mt-2" variant="primary">Simpan Tipe Tiket</x-button>
                                    </div>
                                </form>
                            </div>
                        @endcan
                    </div>

                    {{-- Reviews & Ratings --}}
                    <div class="border-t pt-6">
                        <h3 class="text-xl font-semibold">Ulasan & Penilaian</h3>

                        <div class="mt-3">
                            <strong>Rata-rata rating:</strong>
                            <span>{{ $averageRating ? number_format($averageRating,1) : 'Belum ada' }}</span>
                        </div>

                        <div class="mt-4">
                            @if($event->reviews->isEmpty())
                                <p class="text-gray-600">Belum ada ulasan untuk event ini.</p>
                            @else
                                <ul class="space-y-4">
                                    @foreach($event->reviews as $review)
                                        <li class="p-3 border rounded">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <strong>{{ $review->user->name ?? 'Pengguna' }}</strong>
                                                    <span class="text-sm text-gray-500">— {{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                                <div class="text-yellow-500">
                                                    {{ $review->rating }} / 5
                                                </div>
                                            </div>
                                            @if($review->comment)
                                                <p class="mt-2 text-gray-700">{{ $review->comment }}</p>
                                            @endif

                                            @auth
                                                @if(auth()->id() === $review->user_id || auth()->user()->hasRole('Administrator'))
                                                    <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini?')" class="mt-2">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-sm text-red-600">Hapus</button>
                                                    </form>
                                                @endif
                                            @endauth
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        {{-- Form input review jika boleh --}}
                        @auth
                            @if($canReview)
                                @php
                                    $hasReviewed = $event->reviews->where('user_id', auth()->id())->isNotEmpty();
                                @endphp
                                @if(!$hasReviewed)
                                    <form action="{{ route('events.reviews.store', $event->id) }}" method="POST" class="mt-4">
                                        @csrf
                                        <div class="flex items-center gap-3">
                                            <label for="rating" class="font-medium">Rating</label>
                                            <select name="rating" id="rating" class="rounded border-gray-300">
                                                @for($i=5;$i>=1;$i--)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="mt-2">
                                            <textarea name="comment" rows="3" class="w-full rounded border-gray-300" placeholder="Tulis ulasan Anda (opsional)"></textarea>
                                        </div>
                                        <div class="mt-2">
                                            <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded">Kirim Ulasan</button>
                                        </div>
                                    </form>
                                @else
                                    <p class="mt-3 text-gray-600">Anda sudah memberikan ulasan untuk event ini.</p>
                                @endif
                            @else
                                <p class="mt-3 text-gray-600">Hanya peserta yang membeli tiket untuk event ini yang boleh memberikan ulasan.</p>
                            @endif
                        @endauth
                    </div>

                    {{-- Ticket management (organizer/admin) --}}
                    @if(auth()->check() && (auth()->id() === $event->organizer_id || auth()->user()->hasRole('Administrator')))
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold">Kelola Tipe Tiket</h3>

                            <div class="mt-3">
                                @if($event->tickets->isEmpty())
                                    <p class="text-gray-600">Belum ada tipe tiket untuk event ini.</p>
                                @else
                                    <ul class="space-y-2">
                                        @foreach($event->tickets as $ticket)
                                            <li class="p-2 border rounded flex items-center justify-between">
                                                <div>
                                                    <strong>{{ $ticket->name }}</strong>
                                                    <div class="text-sm text-gray-600">Rp{{ number_format($ticket->price,0,',','.') }} — sisa {{ $ticket->stock }}</div>
                                                </div>
                                                {{-- future: edit/delete buttons --}}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            <form action="{{ route('events.tickets.store', $event->id) }}" method="POST" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                                @csrf
                                <input type="text" name="name" placeholder="Nama tipe (e.g. General)" class="border rounded p-2" required>
                                <input type="number" name="price" placeholder="Harga" class="border rounded p-2" min="0" required>
                                <input type="number" name="stock" placeholder="Stock" class="border rounded p-2" min="0" required>
                                <div class="md:col-span-3">
                                    <button class="px-3 py-2 bg-indigo-600 text-white rounded">Tambah Tipe Tiket</button>
                                </div>
                            </form>
                        </div>
                    @endif

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