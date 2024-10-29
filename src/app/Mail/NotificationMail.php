<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data)
    {
        $this->subject = $mail_data['subject'];
        $this->body = $mail_data['message'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('mails.notification.body')
                    ->text('mails.notification.body_text')
                    ->with(['body' => $this->body]);
    }
}
