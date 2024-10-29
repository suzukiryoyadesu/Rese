<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationDoneMail extends Mailable
{
    use Queueable, SerializesModels;
    public $qr_code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data)
    {
        $this->qr_code = $mail_data['qr_code'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('ご予約が完了致しました')
            ->view('mails.reservation_done.body')
            ->with(['qr_code' => $this->qr_code]);
    }
}
