<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tiket Event - {{ $booking->event->title }}</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      color: #333;
      margin: 30px;
    }
    .container {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      max-width: 600px;
      margin: auto;
      text-align: center;
    }
    h1 {
      color: #007bff;
      margin-bottom: 10px;
    }
    p { margin: 8px 0; }
    .label { font-weight: bold; }
    .qr {
      margin: 20px 0;
    }
    .btn {
      display: inline-block;
      text-decoration: none;
      background-color: #007bff;
      color: white;
      padding: 10px 16px;
      border-radius: 6px;
    }
    .btn:hover { background-color: #0056b3; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Tiket Kamu 🎫</h1>

    <p><span class="label">Event:</span> {{ $booking->event->title }}</p>
    <p><span class="label">Tanggal:</span> {{ $booking->event->date }}</p>
    <p><span class="label">Lokasi:</span> {{ $booking->event->location }}</p>
    <p><span class="label">Jumlah Tiket:</span> {{ $booking->quantity }}</p>
    <p><span class="label">Total Bayar:</span> Rp{{ number_format($booking->total_price) }}</p>
    <p><span class="label">Status:</span> 
      <span style="color:{{ $booking->status === 'paid' ? 'green' : 'red' }}">
        {{ ucfirst($booking->status) }}
      </span>
    </p>

    <div class="qr">
      <p><strong>QR Code Tiket:</strong></p>
      @if($booking->qr_code_path)
        <img src="{{ asset('storage/' . $booking->qr_code_path) }}" alt="QR Code Tiket" width="200">
      @else
        <p style="color:gray;">QR Code belum tersedia.</p>
      @endif
    </div>

    <a href="{{ route('dashboard') }}" class="btn">Kembali ke Dashboard</a>
  </div>
</body>
</html>