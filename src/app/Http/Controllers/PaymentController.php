<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Models\Reservation;
use App\Http\Requests\ReservationRequest;

class PaymentController extends Controller
{
    /**
     * 決済ページの表示
     *
     * @param ReservationRequest $request リクエスト
     * @return view payment.blade
     */
    public function paymentView(Request $request)
    {
        // 予約情報、日付の取得
        $reservation = Reservation::find($request->reservation_id);
        $dt = $request->date;

        // 未支払の場合
        if ($reservation->payment_status == 1) {

            // 決済画面の表示
            return view('payment.payment', compact('reservation', 'dt'));
        }

        // 支払済みの場合
        if ($reservation->payment_status == 2) {
            $message = '既に決済が完了しています';
            return view('payment.done.payment_done', compact('message', 'dt'));
        }
    }

    /**
     * 決済
     *
     * @param ReservationRequest $request リクエスト
     * @return view payment_done.blade
     */
    public function payment(Request $request)
    {
        // 予約情報、日付、ユーザー情報の取得
        $reservation = Reservation::find($request->reservation_id);
        $dt = $request->date;
        $user = $reservation->user->first();

        // バリデーションルール、メッセージの作成
        $rules = [
            'amount' => 'required|integer',
        ];
        $messages = [
            'amount.required' => '金額を必ず入力してください',
            'amount.integer' => '金額を半角数字のみで入力してください',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        // バリデーションエラーの場合
        if ($validator->fails()) {
            $url = '/reservation/payment?reservation_id=' . $reservation->id . '&date=' . $dt;
            return redirect($url)->withErrors($validator)->withInput();
        }

        // バリデーション済みデータの取得
        $amount = $validator->validated()['amount'];

        // 未支払の場合
        if ($reservation->payment_status == 1) {
            \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

            // 決済情報のセット
            $chargeOject = [
                'amount'      => $amount,
                'currency'    => 'jpy',
                'description' => 'ユーザーID：' . $user->id . '/予約ID：' . $reservation->id,
                'customer'      => $user->stripe_id,
            ];

            // 決済
            try {
                $charge = \Stripe\Charge::create($chargeOject);
            } catch (\Stripe\Exception\CardException $e) {
                $message = '決済できませんでした';
                return view('payment.done.payment_done', compact('message', 'reservation', 'dt'));
            }

            // 支払済みにする
            $reservation->payment_status = 2;
            $reservation->update();

            // 決済完了画面の表示
            $message = '決済が完了しました';
            return view('payment.done.payment_done', compact('message', 'dt'));
        }

        // 支払済みの場合
        if ($reservation->payment_status == 2) {
            $message = '既に決済が完了しています';
            return view('payment.done.payment_done', compact('message', 'dt'));
        }
    }
}
