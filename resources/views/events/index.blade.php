<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Event') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Daftar Event</h1>
            <a href="{{ route('events.create') }}">
                <x-button variant="success">+ Tambah Event</x-button>
            </a>
        </div>

        @if(session('success'))
            <p class="text-green-600 mt-2">{{ session('success') }}</p>
        @endif

        @if($events->isEmpty())
            <p class="mt-4 text-gray-600">Tidak ada event tersedia.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <x-card>
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $event->title }}</h3>
                                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($event->date)->format('d M Y') }} • {{ $event->location }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">Rp{{ number_format($event->price, 0, ',', '.') }}</div>
                                @php $avg = $event->reviews()->avg('rating'); @endphp
                                <x-badge color="yellow">{{ $avg ? number_format($avg,1) . ' / 5' : '—' }}</x-badge>
                            </div>
                        </div>

                        <p class="mt-4 text-gray-700 dark:text-gray-300">{{ Str::limit($event->description, 120) }}</p>

                        <div class="mt-4 flex items-center justify-between">
                            <div class="space-x-2">
                                <a href="{{ route('events.show', $event->id) }}"><x-button variant="secondary">Lihat</x-button></a>
                                <a href="{{ route('events.edit', $event->id) }}"><x-button variant="primary">Edit</x-button></a>
                            </div>
                            <div>
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus event ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-button variant="danger">Hapus</x-button>
                                </form>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
