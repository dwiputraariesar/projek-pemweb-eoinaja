<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">
                        Halo, {{ Auth::user()->name }} 👋
                    </h3>
                    <p class="mb-6">Selamat datang di dashboard kamu!</p>

                    <div class="border-t border-gray-300 my-4"></div>

                    <h4 class="text-lg font-semibold mb-3">Daftar Event Tersedia</h4>

                    @if(isset($events) && count($events) > 0)
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 px-3">Judul</th>
                                    <th class="py-2 px-3">Tanggal</th>
                                    <th class="py-2 px-3">Lokasi</th>
                                    <th class="py-2 px-3">Harga</th>
                                    <th class="py-2 px-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $event)
                                    <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-2 px-3">{{ $event->title }}</td>
                                        <td class="py-2 px-3">{{ $event->date }}</td>
                                        <td class="py-2 px-3">{{ $event->location }}</td>
                                        <td class="py-2 px-3">Rp{{ number_format($event->price) }}</td>
                                        <td class="py-2 px-3">
                                            <a href="{{ route('events.show', $event->id) }}" class="text-blue-600 hover:underline">Lihat</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500">Belum ada event yang tersedia.</p>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('events.index') }}"
                           class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                           Lihat Semua Event
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>