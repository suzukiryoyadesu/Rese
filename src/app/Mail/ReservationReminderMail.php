<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationReminderMail extends Mailable
{
    use Queueable, SerializesModels;
    public $reservation;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data)
    {
        $this->reservation = $mail_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('ご予約のリマインドメールです')
            ->view('mails.reservation_reminder.body')
            ->text('mails.reservation_reminder.body_text')
            ->with(['reservation' => $this->reservation]);
    }
}
