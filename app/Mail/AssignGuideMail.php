<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssignGuideMail extends Mailable
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
                new Address('tour@hirebalidriver.com', 'Hire Bali Driver'),
            ],
            subject: 'New Booking for '.$this->details['date'].' ('.$this->details['ref'].' )',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.guide.assignguide',
        );
    }
}
