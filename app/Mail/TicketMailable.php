<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class TicketMailable extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public string $pdfPath;

    public function __construct(Booking $booking, string $pdfPath)
    {
        $this->booking = $booking;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject('Your ticket for ' . $this->booking->event->title)
            ->view('emails.ticket')
            ->attach($this->pdfPath, ['as' => 'ticket.pdf', 'mime' => 'application/pdf'])
            ->with(['booking' => $this->booking]);
    }
}
