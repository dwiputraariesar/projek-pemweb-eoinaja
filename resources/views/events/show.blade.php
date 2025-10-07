<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Event - {{ $event->title }}</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 30px;
      background-color: #f8f9fa;
      color: #333;
    }

    .container {
      background: white;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      max-width: 600px;
      margin: auto;
    }

    h1 {
      color: #007bff;
      margin-bottom: 10px;
    }

    p {
      margin: 8px 0;
    }

    .label {
      font-weight: bold;
    }

    .actions {
      margin-top: 20px;
    }

    a, button {
      text-decoration: none;
      display: inline-block;
      background-color: #007bff;
      color: white;
      padding: 8px 14px;
      border-radius: 5px;
      margin-right: 8px;
      border: none;
      cursor: pointer;
    }

    a:hover, button:hover {
      background-color: #0056b3;
    }

    .delete-btn {
      background-color: #dc3545;
    }

    .delete-btn:hover {
      background-color: #b02a37;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>{{ $event->title }}</h1>

    <p><span class="label">Tanggal:</span> {{ $event->date }}</p>
    <p><span class="label">Lokasi:</span> {{ $event->location }}</p>
    <p><span class="label">Harga Tiket:</span> Rp{{ number_format($event->price) }}</p>
    <p><span class="label">Deskripsi:</span> {{ $event->description }}</p>

    <div class="actions">
      <a href="{{ route('events.edit', $event->id) }}">Edit Event</a>

      <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="delete-btn" onclick="return confirm('Yakin hapus event ini?')">Hapus</button>
      </form>
        <form action="{{ route('bookings.store', $event->id) }}" method="POST">
            @csrf
             <label>Jumlah Tiket:</label>
                <input type="number" name="quantity" value="1" min="1" class="border rounded p-1">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Beli Tiket</button>
        </form>
      <a href="{{ route('events.index') }}">← Kembali</a>
    </div>
  </div>
</body>
</html>