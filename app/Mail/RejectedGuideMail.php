<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RejectedGuideMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function envelope()
    {
        return new Envelope(
            replyTo: [
                new Address($this->details['to'], $this->details['name']),
            ],
            subject: 'Rejected! Tour by '.$this->details['name'].' for '.$this->details['date'].' ('.$this->details['ref'].' )',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.guide.rejectedguide',
        );
    }
}
