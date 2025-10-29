<div style="font-family: Arial, Helvetica, sans-serif;">
    <h2>Your ticket for {{ $booking->event->title }}</h2>

    <p>Hi {{ $booking->user->name }},</p>

    <p>Thanks for your purchase. Attached is your ticket (PDF) containing the QR code to check-in at the event.</p>

    <p>Event: {{ $booking->event->title }}<br>
    When: {{ $booking->event->date }} {{ $booking->event->time }}<br>
    Where: {{ $booking->event->location }}</p>

    <p>If you have questions, reply to this email.</p>

    <p>— Event Team</p>
</div>
