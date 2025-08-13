<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $subjectLine;
    public $messageContent;

    public function __construct($name, $email, $subjectLine, $messageContent)
    {
        $this->name          = $name;
        $this->email         = $email;
        $this->subjectLine   = $subjectLine;
        $this->messageContent = $messageContent;
    }

    public function build()
    {
        return $this->subject('Contact Us: ' . $this->subjectLine)
                    ->view('emails.contact')
                    ->with([
                        'name'    => $this->name,
                        'email'   => $this->email,
                        'subject' => $this->subjectLine,
                        'message' => $this->messageContent,
                    ]);
    }
}
