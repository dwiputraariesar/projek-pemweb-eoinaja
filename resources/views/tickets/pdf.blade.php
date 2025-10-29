<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket - {{ $booking->event->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .container { padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .qr { margin-top: 20px; text-align: center; }
    </style>
    </head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $booking->event->title }}</h1>
        <p>{{ $booking->event->date }} {{ $booking->event->time }} — {{ $booking->event->location }}</p>
        <p>Attendee: {{ $booking->user->name }} ({{ $booking->user->email }})</p>
        <p>Quantity: {{ $booking->quantity }}</p>
    </div>

    <div class="qr">
        @if(!empty($qr_svg))
            {!! $qr_svg !!}
        @else
            <p>No QR available</p>
        @endif
    </div>
</div>
</body>
</html>
