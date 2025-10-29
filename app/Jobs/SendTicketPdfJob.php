<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\TicketMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTicketPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Booking $booking;

    /**
     * Create a new job instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $booking = $this->booking->fresh();

            $qrSvg = null;
            if ($booking->qr_code && Storage::disk('public')->exists($booking->qr_code)) {
                $qrSvg = Storage::disk('public')->get($booking->qr_code);
            }

            $pdf = Pdf::loadView('tickets.pdf', ['booking' => $booking, 'qr_svg' => $qrSvg]);

            $path = 'public/tickets/booking-' . $booking->getKey() . '.pdf';
            Storage::put($path, $pdf->output());

            // Queue the email with the generated PDF attached
            Mail::to($booking->user->email)->queue(new TicketMailable($booking, storage_path('app/' . $path)));
        } catch (\Throwable $e) {
            Log::error('SendTicketPdfJob failed for booking '.$this->booking->getKey().': '.$e->getMessage(), ['exception' => $e]);
            throw $e; // rethrow to allow worker retry behavior
        }
    }
}
