<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        // return $this->from($this->details['email'], $this->details['name'])
        //             ->subject('New Booking for '.$this->details['date'].' ('.$this->details['ref'].' )')
        //             ->view('emails.front.bookingadmin');
        return $this->subject('New Booking for '.$this->details['date'].' ('.$this->details['ref'].' )')
                    ->view('emails.front.bookingadmin');
    }

    // public function envelope()
    // {
    //     return new Envelope(
    //         from: new Address($this->details['email'], $this->details['name']),
    //         replyTo: [
    //             new Address($this->details['email'], $this->details['name']),
    //         ],
    //         subject: 'New Booking for '.$this->details['date'].' ('.$this->details['ref'].' )',
    //     );
    // }

    // public function content()
    // {
    //     return new Content(
    //         view: 'emails.front.bookingadmin',
    //     );
    // }

}
