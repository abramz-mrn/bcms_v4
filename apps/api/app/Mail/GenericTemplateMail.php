<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericTemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectText,
        public string $htmlBody
    ) {}

    public function build()
    {
        return $this->subject($this->subjectText)
            ->html($this->htmlBody);
    }
}