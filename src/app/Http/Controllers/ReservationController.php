<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\ReservationDoneMail;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Reservation;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\RestaurantRequest;

class ReservationController extends Controller
{
    /**
     * 予約
     *
     * @param RestaurantRequest $request リクエスト
     */
    public function reservation(ReservationRequest $request)
    {
        // セッションキー、画面遷移情報、ユーザー情報の取得
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $user = Auth::user();

        // 予約情報のセット
        $reservation_array = $request->only(['restaurant_id', 'date', 'time', 'number', 'payment_id']);
        $reservation_array['user_id'] = $user->id;

        // 現地決済の場合
        if ($reservation_array['payment_id'] == 1) {
            // 支払済みにする
            $reservation_array['payment_status'] = 2;

            // 予約
            $reservation = Reservation::create($reservation_array);

            // 予約QRコードの作成
            $mail_data['qr_code'] = QrCode::generate($reservation->id);

            // メール送信処理
            $mail = new ReservationDoneMail($mail_data);
            Mail::to($user->email)->send($mail);

            // メッセージの生成
            $message = 'ご予約ありがとうございます';

            // 予約完了ページの表示
            return redirect('/done')->with([
                'reservation_array' => $reservation_array,
                'access_time' => $access_time,
                'page_status' => $page_status,
                'message' => $message,
            ]);
        }

        // Stripe決済の場合
        if ($reservation_array['payment_id'] == 2) {
            // Stripe上に顧客として存在していない場合
            if (!$user->stripe_id) {
                $message = 'カード情報を登録してください';
                return redirect('/done')->with([
                    'reservation_array' => $reservation_array,
                    'access_time' => $access_time,
                    'page_status' => $page_status,
                    'message' => $message,
                ]);
            }

            // Stripe上に顧客として存在している場合
            if ($user->stripe_id) {
                \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

                // カード情報を取得
                $card = \Stripe\Customer::allSources(
                    $user->stripe_id,
                    [
                        'limit'  => 1,
                        'object' => 'card',
                    ]
                )->data;

                // カード情報が存在している場合
                if (count($card) > 0) {
                    // 未支払にする
                    $reservation_array['payment_status'] = 1;

                    // 予約
                    $reservation = Reservation::create($reservation_array);

                    // 予約QRコードの作成
                    $mail_data['qr_code'] = QrCode::generate($reservation->id);

                    // メール送信処理
                    $mail = new ReservationDoneMail($mail_data);
                    Mail::to($user->email)->send($mail);

                    // メッセージの生成
                    $message = 'ご予約ありがとうございます';

                    // 予約完了ページの表示
                    return redirect('/done')->with([
                        'reservation_array' => $reservation_array,
                        'access_time' => $access_time,
                        'page_status' => $page_status,
                        'message' => $message,
                    ]);
                }

                // カード情報が存在しない場合
                $message = 'カード情報を登録してください';
                return redirect('/done')->with([
                    'reservation_array' => $reservation_array,
                    'access_time' => $access_time,
                    'page_status' => $page_status,
                    'message' => $message,
                ]);
            }
        }

        // メッセージの生成
        $message = '予約できませんでした';

        // 予約完了ページの表示
        return redirect('/done')->with([
            'reservation_array' => $reservation_array,
            'access_time' => $access_time,
            'page_status' => $page_status,
            'message' => $message,
        ]);
    }

    /**
     * 予約完了ページの表示
     *
     * @param Request $request リクエスト
     * @return view reservation_done.blade
     */
    public function reservationDone(Request $request)
    {
        // 予約情報、セッションキー、画面遷移情報、メッセージの取得
        $reservation_array = $request->session()->get('reservation_array');
        $access_time = $request->session()->get('access_time');
        $page_status = $request->session()->get('page_status');
        $message = $request->session()->get('message');

        // 予約完了画面の表示
        return view('reservation.done.reservation_done', compact('reservation_array', 'access_time', 'page_status', 'message'));
    }

    /**
     * 予約削除
     *
     * @param Request $request リクエスト
     * @return view reservation_delete_done.blade
     */
    public function reservationDelete(Request $request)
    {
        // 現在日時、予約情報、画面遷移情報の取得
        $dt_now = Carbon::now();
        $reservation = Reservation::find($request->reservation_id);
        $page_status = $request->page_status;

        // 予約情報が存在しない場合
        if (empty($reservation)) {

            // 予約一覧画面から遷移の場合
            if ($page_status == 'reservation_record') {
                $message = '予約情報が存在しません';
                $href = '/reservation/record/?date=' . $request->date;
                return view('reservation.done.reservation_delete_done', compact('message', 'href'));
            }

            // マイページから遷移の場合
            if ($page_status == 'mypage') {
                $message = '予約情報が存在しません';
                $href = '/mypage';
                return view('reservation.done.reservation_delete_done', compact('message', 'href'));
            }
        }

        // 予約日が今日より前の場合
        if ($reservation->date < $dt_now->format("Y-m-d")) {
            $message = '予約日時を過ぎているため、削除できません';
        }

        // 予約日が今日の場合
        if ($reservation->date == $dt_now->format("Y-m-d")) {

            // 予約時間が現在の時刻より後の場合
            if ($reservation->time > $dt_now->format("H:i")) {
                $reservation->delete();
                $message = '予約を削除しました';
            }

            // 予約時間が現在の時刻以前の場合
            if ($reservation->time <= $dt_now->format("H:i")) {
                $message = '予約日時を過ぎているため、削除できません';
            }
        }

        // 予約日が明日以降の場合
        if ($reservation->date > $dt_now->format("Y-m-d")) {
            $reservation->delete();
            $message = '予約を削除しました';
        }

        // 予約一覧画面から遷移の場合
        if ($page_status == 'reservation_record') {
            $href = '/reservation/record/?date=' . $request->date;
            return view('reservation.done.reservation_delete_done', compact('message', 'href'));
        }

        // マイページから遷移の場合
        if ($page_status == 'mypage') {
            $href = '/mypage';
            return view('reservation.done.reservation_delete_done', compact('message', 'href'));
        }
    }

    /**
     * 予約変更ページの表示
     *
     * @param Request $request リクエスト
     * @return view reservation_change.blade
     */
    public function reservationChangeView(Request $request)
    {
        // 現在日時、予約情報、画面遷移情報支払方法の取得
        $dt_now = Carbon::now();
        $reservation = Reservation::find($request->reservation_id);
        $page_status = $request->page_status;
        $payments = Payment::all();

        // 予約情報が存在しない場合
        if (empty($reservation)) {

            // 予約一覧画面から遷移の場合
            if ($page_status == 'reservation_record') {
                $message = '予約情報が存在しません';
                $href = '/reservation/record/?date=' . $request->date;
                return view('reservation.done.reservation_change_done', compact('message', 'href', 'page_status'));
            }

            // マイページから遷移の場合
            if ($page_status == 'mypage') {
                $message = '予約情報が存在しません';
                $href = '/mypage';
                return view('reservation.done.reservation_change_done', compact('message', 'href', 'page_status'));
            }
        }

        // 予約日が今日より前の場合
        if ($reservation->date < $dt_now->format("Y-m-d")) {

            // 予約一覧画面から遷移の場合
            if ($page_status == 'reservation_record') {
                $message = '予約日時を過ぎているため、変更できません';
                $href = '/reservation/record/?date=' . $request->date;
                return view('reservation.done.reservation_change_done', compact('message', 'href', 'page_status'));
            }

            // マイページから遷移の場合
            if ($page_status == 'mypage') {
                $message = '予約日時を過ぎているため、変更できません';
                $href = '/mypage';
                return view('reservation.done.reservation_change_done', compact('message', 'href', 'page_status'));
            }
        }

        // 予約日が今日の場合
        if ($reservation->date == $dt_now->format("Y-m-d")) {

            // 予約時間が現在の時刻より後の場合
            if ($reservation->time > $dt_now->format("H:i")) {

                // 予約一覧画面から遷移の場合
                if ($page_status == 'reservation_record') {
                    $dt = $request->date;
                    $href = '/reservation/record/?date=' . $dt;
                    return view('reservation.reservation_change', compact('reservation', 'page_status', 'dt', 'href', 'payments'));
                }

                // マイページから遷移の場合
                if ($page_status == 'mypage') {
                    $href = '/mypage';
                    return view('reservation.reservation_change', compact('reservation', 'page_status', 'href', 'payments'));
                }
            }

            // 予約時間が現在の時刻以前の場合
            if ($reservation->time <= $dt_now->format("H:i")) {

                // 予約一覧画面から遷移の場合
                if ($page_status == 'reservation_record') {
                    $message = '予約日時を過ぎているため、変更できません';
                    $href = '/reservation/record/?date=' . $request->date;
                    return view('reservation.done.reservation_change_done', compact('message', 'href', 'page_status'));
                }

                // マイページから遷移の場合
                if ($page_status == 'mypage') {
                    $message = '予約日時を過ぎているため、変更できません';
                    $href = '/mypage';
                    return view('reservation.done.reservation_change_done', compact('message', 'href', 'page_status'));
                }
            }
        }

        // 予約日が明日以降の場合
        if ($reservation->date > $dt_now->format("Y-m-d")) {

            // 予約一覧画面から遷移の場合
            if ($page_status == 'reservation_record') {
                $dt = $request->date;
                $href = '/reservation/record/?date=' . $dt;
                return view('reservation.reservation_change', compact('reservation', 'page_status', 'dt', 'href', 'payments'));
            }

            // マイページから遷移の場合
            if ($page_status == 'mypage') {
                $href = '/mypage';
                return view('reservation.reservation_change', compact('reservation', 'page_status', 'href', 'payments'));
            }
        }
    }

    /**
     * 予約変更
     *
     * @param ReservationRequest $request リクエスト
     * @return view reservation_change_done.blade
     */
    public function reservationChange(ReservationRequest $request)
    {
        // 現在日時、予約情報、画面遷移情報、ユーザー情報の取得
        $dt_now = Carbon::now();
        $reservation = Reservation::find($request->reservation_id);
        $page_status = $request->page_status;
        $user = $reservation->user()->first();

        // 予約情報が存在しない場合
        if (empty($reservation)) {

            // 予約一覧画面から遷移の場合
            if ($page_status == 'reservation_record') {
                $message = '予約情報が存在しません';
                $href = '/reservation/record/?date=' . $request->date;
                return view('reservation.done.reservation_change_done', compact('message', 'href', 'page_status'));
            }

            // マイページから遷移の場合
            if ($page_status == 'mypage') {
                $message = '予約情報が存在しません';
                $href = '/mypage';
                return view('reservation.done.reservation_change_done', compact('message', 'href', 'page_status'));
            }
        }

        // 予約日が今日より前の場合
        if ($reservation->date < $dt_now->format("Y-m-d")) {
            $message = '予約日時を過ぎているため、変更できません';
        }

        // 予約日が今日の場合
        if ($reservation->date == $dt_now->format("Y-m-d")) {

            // 予約時間が現在の時刻より後の場合
            if ($reservation->time > $dt_now->format("H:i")) {
                // 予約情報のセット
                $reservation_array = $request->only(['date', 'time', 'number', 'payment_id']);

                // 現地決済の場合
                if ($reservation_array['payment_id'] == 1) {
                    // 支払済みにする
                    $reservation_array['payment_status'] = 2;

                    // 予約変更
                    $reservation->update($reservation_array);
                    $message = '予約の変更が完了しました';
                }

                // Stripe決済の場合
                if ($reservation_array['payment_id'] == 2) {
                    // Stripe上に顧客として存在していない場合
                    if (!$user->stripe_id) {
                        $message = 'カード情報を登録してください';
                        $href = '/card/create';
                        return view('reservation.done.reservation_change_done', compact('message', 'href'));
                    }

                    // Stripe上に顧客として存在している場合
                    if ($user->stripe_id) {
                        \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

                        // カード情報を取得
                        $card = \Stripe\Customer::allSources(
                            $user->stripe_id,
                            [
                                'limit'  => 1,
                                'object' => 'card',
                            ]
                        )->data;

                        // カード情報が存在している場合
                        if (count($card) > 0) {
                            // 未支払にする
                            $reservation_array['payment_status'] = 1;

                            // 予約変更
                            $reservation->update($reservation_array);
                            $message = '予約の変更が完了しました';
                        }

                        // カード情報が存在しない場合
                        if (count($card) == 0) {
                            $message = 'カード情報を登録してください';
                            $href = '/card/create';
                            return view('reservation.done.reservation_change_done', compact('message', 'href'));
                        }
                    }
                }
            }

            // 予約時間が現在の時刻以前の場合
            if ($reservation->time <= $dt_now->format("H:i")) {
                $message = '予約日時を過ぎているため、変更できません';
            }
        }

        // 予約日が明日以降の場合
        if ($reservation->date > $dt_now->format("Y-m-d")) {
            // 予約情報のセット
            $reservation_array = $request->only(['date', 'time', 'number', 'payment_id']);

            // 現地決済の場合
            if ($reservation_array['payment_id'] == 1) {
                // 支払済みにする
                $reservation_array['payment_status'] = 2;

                // 予約変更
                $reservation->update($reservation_array);
                $message = '予約の変更が完了しました';
            }

            // Stripe決済の場合
            if ($reservation_array['payment_id'] == 2) {
                // Stripe上に顧客として存在していない場合
                if (!$user->stripe_id) {
                    $message = 'カード情報を登録してください';
                    $href = '/card/create';
                    return view('reservation.done.reservation_change_done', compact('message', 'href'));
                }

                // Stripe上に顧客として存在している場合
                if ($user->stripe_id) {
                    \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

                    // カード情報を取得
                    $card = \Stripe\Customer::allSources(
                        $user->stripe_id,
                        [
                            'limit'  => 1,
                            'object' => 'card',
                        ]
                    )->data;

                    // カード情報が存在している場合
                    if (count($card) > 0) {
                        // 未支払にする
                        $reservation_array['payment_status'] = 1;

                        // 予約変更
                        $reservation->update($reservation_array);
                        $message = '予約の変更が完了しました';
                    }

                    // カード情報が存在しない場合
                    if (count($card) == 0) {
                        $message = 'カード情報を登録してください';
                        $href = '/card/create';
                        return view('reservation.done.reservation_change_done', compact('message', 'href'));
                    }
                }
            }
        }

        // 予約一覧画面から遷移の場合
        if ($page_status == 'reservation_record') {
            $href = '/reservation/record/?date=' . $request->date;
            return view('reservation.done.reservation_change_done', compact('message', 'href', 'page_status'));
        }

        // マイページから遷移の場合
        if ($page_status == 'mypage') {
            $href = '/mypage';
            return view('reservation.done.reservation_change_done', compact('message', 'href', 'page_status'));
        }
    }

    /**
     * 予約一覧ページの表示
     *
     * @param ReservationRequest $request リクエスト
     * @return view reservation_record.blade
     */
    public function reservationRecord(Request $request)
    {
        // ユーザー、店舗情報を取得
        $user = Auth::user();
        $restaurant = $user->toRestaurants()->first();

        // 店舗情報を未作成の場合
        if ($restaurant == null) {
            $message = '店舗情報を作成してください';
            return view('done', compact('message'));
        }

        // 現在の日時を取得
        $dt_now = Carbon::now();

        // 日付が送信されなかった場合
        if (empty($request->date)) {
            $dt = $dt_now;

            // 現在の時刻以降の予約情報の取得
            $reservations = Reservation::where('restaurant_id', $restaurant->id)
                ->where('date', $dt->format("Y-m-d"))
                ->where('time', '>=', $dt->format("H:i"))
                ->orderBy('time', 'asc')->get();

            // 現在の時刻より前の予約情報の取得
            $reservations_history = Reservation::where('restaurant_id', $restaurant->id)
                ->where('date', $dt->format("Y-m-d"))
                ->where('time', '<', $dt->format("H:i"))
                ->orderBy('time', 'asc')->get();

            $dt = $dt->isoFormat('YYYY年MM月DD日(ddd)');
            $page_status = 'reservation_record';
            return view('reservation.reservation_record', compact('reservations', 'reservations_history', 'dt', 'page_status'));
        }

        // 日付が送信された場合
        if (!empty($request->date)) {
            $year = mb_substr($request->date, 0, 4);
            $month = mb_substr($request->date, 5, 2);
            $day = mb_substr($request->date, 8, 2);
            $dt = Carbon::createFromDate($year, $month, $day);

            // 送信された日付が現在の日時より前の場合
            if ($dt->format("Y-m-d") < $dt_now->format("Y-m-d")) {

                // 現在の時刻以降の予約情報の取得
                $reservations = null;

                // 現在の時刻より前の予約情報の取得
                $reservations_history = Reservation::where('restaurant_id', $restaurant->id)
                    ->where('date', $dt->format("Y-m-d"))
                    ->orderBy('time', 'asc')->get();

                $dt = $dt->isoFormat('YYYY年MM月DD日(ddd)');
                $page_status = 'reservation_record';
                return view('reservation.reservation_record', compact('reservations', 'reservations_history', 'dt', 'page_status'));
            }

            // 送信された日付が現在の日時より後の場合
            if ($dt->format("Y-m-d") > $dt_now->format("Y-m-d")) {
                // 現在の時刻以降の予約情報の取得
                $reservations = Reservation::where('restaurant_id', $restaurant->id)
                    ->where('date', $dt->format("Y-m-d"))
                    ->orderBy('time', 'asc')->get();

                // 現在の時刻より前の予約情報の取得
                $reservations_history = null;

                $dt = $dt->isoFormat('YYYY年MM月DD日(ddd)');
                $page_status = 'reservation_record';
                return view('reservation.reservation_record', compact('reservations', 'reservations_history', 'dt', 'page_status'));
            }

            // 送信された日付が今日の場合
            // 現在の時刻以降の予約情報の取得
            $reservations = Reservation::where('restaurant_id', $restaurant->id)
                ->where('date', $dt->format("Y-m-d"))
                ->where('time', '>=', $dt->format("H:i"))
                ->orderBy('time', 'asc')->get();

            // 現在の時刻より前の予約情報の取得
            $reservations_history = Reservation::where('restaurant_id', $restaurant->id)
                ->where('date', $dt->format("Y-m-d"))
                ->where('time', '<', $dt->format("H:i"))
                ->orderBy('time', 'asc')->get();

            $dt = $dt->isoFormat('YYYY年MM月DD日(ddd)');
            $page_status = 'reservation_record';
            return view('reservation.reservation_record', compact('reservations', 'reservations_history', 'dt', 'page_status'));
        }
    }

    /**
     * 予約QRコード読み取りページの表示
     *
     * @return view reservation_qr.blade
     */
    public function reservationQr()
    {
        return view('reservation.reservation_qr');
    }

    /**
     * 予約情報確認ページの表示
     *
     * @param ReservationRequest $request リクエスト
     * @return view reservation_confirm.blade
     */
    public function reservationConfirm(Request $request)
    {
        // 予約情報の取得
        $reservation = Reservation::find($request->reservation_id);
        return view('reservation.reservation_confirm', compact('reservation'));
    }
}
