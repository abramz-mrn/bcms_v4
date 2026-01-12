<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public int $dayOffset
    ) {}

    public function build()
    {
        $no = $this->invoice->invoice_number ?? ('#'.$this->invoice->id);

        $when = match ($this->dayOffset) {
            -3 => 'Reminder H-3 sebelum jatuh tempo',
            -1 => 'Reminder H-1 sebelum jatuh tempo',
            1 => 'Reminder H+1 setelah jatuh tempo',
            3 => 'Reminder H+3 setelah jatuh tempo',
            default => 'Reminder invoice',
        };

        return $this->subject("{$when} - Invoice {$no}")
            ->view('emails.invoice_reminder')
            ->with([
                'invoice' => $this->invoice,
                'dayOffset' => $this->dayOffset,
                'when' => $when,
            ]);
    }
}