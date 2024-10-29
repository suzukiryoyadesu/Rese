<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationReminderMail;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Reservation;

class ReservationReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ReservationReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '予約リマインドメールの送信';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 今日、明日の日時、予約情報を取得
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $reservations = Reservation::where([['date', '=', $today->format("Y-m-d")], ['time', '>=', '09:00']])
            ->orwhere([['date', '=', $tomorrow->format("Y-m-d")], ['time', '<', '09:00']])
            ->get();

        // メール送信
        if (count($reservations) > 0) {
            foreach ($reservations as $reservation) {
                $user = $reservation->user()->first();
                $restaurant = $reservation->restaurant()->first();
                $payment = $reservation->payment()->first();
                $mail_data = [
                    'user_name' => $user->name,
                    'restaurant_name' => $restaurant->name,
                    'date' => $reservation->date,
                    'time' => $reservation->time,
                    'number' => $reservation->number,
                    'payment_name' => $payment->name,
                ];
                $mail = new ReservationReminderMail($mail_data);
                Mail::to($user->email)->send($mail);
            }
        }
    }
}
