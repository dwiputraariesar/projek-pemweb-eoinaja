<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Event') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                    background: white;
                }

                table, th, td {
                    border: 1px solid #ddd;
                }

                th {
                    background-color: #007bff;
                    color: white;
                    text-align: left;
                    padding: 10px;
                }

                td {
                    padding: 10px;
                    vertical-align: middle;
                }

                .btn-create {
                    display: inline-block;
                    margin-bottom: 10px;
                    background-color: #28a745;
                    color: white;
                    padding: 8px 12px;
                    border-radius: 5px;
                    text-decoration: none;
                }

                .btn-create:hover {
                    background-color: #218838;
                }

                .btn-action {
                    margin-right: 5px;
                    color: #007bff;
                    text-decoration: none;
                }

                .btn-action:hover {
                    text-decoration: underline;
                }

                button {
                    background: none;
                    border: none;
                    color: red;
                    cursor: pointer;
                    font-size: 14px;
                }

                button:hover {
                    text-decoration: underline;
                }
            </style>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">Daftar Event</h1>

            <a href="{{ route('events.create') }}" class="btn-create">+ Tambah Event</a>

            @if(session('success'))
                <p class="text-green-600 mt-2">{{ session('success') }}</p>
            @endif

            @if($events->isEmpty())
                <p class="mt-4 text-gray-600">Tidak ada event tersedia.</p>
            @else
                <table class="mt-4">
                    <tr>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>

                    @foreach($events as $event)
                    <tr>
                        <td>{{ $event->title }}</td>
                        <td>{{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</td>
                        <td>{{ $event->location }}</td>
                        <td>Rp{{ number_format($event->price, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('events.show', $event->id) }}" class="btn-action">Lihat</a> |
                            <a href="{{ route('events.edit', $event->id) }}" class="btn-action">Edit</a> |
                            <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus event ini?')"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
