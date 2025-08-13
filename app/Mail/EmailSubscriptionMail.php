<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailSubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriberEmail;

    public function __construct($subscriberEmail)
    {
        $this->subscriberEmail = $subscriberEmail;
    }

    public function build()
    {
        return $this->subject('New Newsletter Subscription')
                    ->view('emails.subscription')
                    ->with(['email' => $this->subscriberEmail]);
    }
}
