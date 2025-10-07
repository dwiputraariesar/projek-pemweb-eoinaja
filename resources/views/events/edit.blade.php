<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Event</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 30px;
      background-color: #f8f9fa;
    }

    h1 { color: #333; }

    form {
      background: white;
      padding: 20px;
      border-radius: 8px;
      width: 400px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    label { display: block; margin-top: 10px; }

    input, textarea {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      margin-top: 15px;
      background-color: #28a745;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover { background-color: #218838; }

    a {
      display: inline-block;
      margin-top: 15px;
      color: #555;
    }
  </style>
</head>
<body>
  <h1>Edit Event</h1>

  <form action="{{ route('events.update', $event->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Judul Event</label>
    <input type="text" name="title" value="{{ $event->title }}" required>

    <label>Tanggal</label>
    <input type="date" name="date" value="{{ $event->date }}" required>

    <label>Lokasi</label>
    <input type="text" name="location" value="{{ $event->location }}" required>

    <label>Harga Tiket (Rp)</label>
    <input type="number" name="price" min="0" value="{{ $event->price }}" required>

    <label>Deskripsi</label>
    <textarea name="description" rows="4">{{ $event->description }}</textarea>

    <button type="submit">Update</button>
  </form>

  <a href="{{ route('events.index') }}">← Kembali ke daftar event</a>
</body>
</html>
