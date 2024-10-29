<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\NotificationMail;
use App\Mail\ReservationDoneMail;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\Role;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\RestaurantRequest;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $access_time = Carbon::now()->format('Y/m/d  H:i:s.v');
        $search_condition = [
            'access_time' => $access_time,
            'search_area_id' => '',
            'search_genre_id' => '',
            'search_keyword' => '',
        ];
        $request->session()->put($access_time, $search_condition);
        $position = 0;
        $restaurants = Restaurant::all();
        foreach ($restaurants as $restaurant) {
            $restaurant['review_total'] = Review::where('restaurant_id', $restaurant->id)->count();
            if ($restaurant['review_total'] > 0) {
                $reviews = Review::where('restaurant_id', $restaurant->id)->get();
                $i = 0;
                foreach ($reviews as $review) {
                    $i += $review->evaluation;
                }
                $restaurant['review_average'] = number_format($i / $restaurant['review_total'], 1);
            }
        }
        $areas = Area::all();
        $genres = Genre::all();
        return view('index', compact('restaurants', 'areas', 'genres', 'search_condition', 'position'));
    }
    //飲食店一覧ページの表示

    public function addFavorite(Request $request)
    {
        $user = Auth::user();
        $restaurant_id = $request->restaurant_id;
        if (!$user->isFavorite($restaurant_id)) {
            $user->favoriteToRestaurants()->attach($restaurant_id);
        }
        $request->session()->put('access_time', $request->access_time);
        $request->session()->put('position', $request->position);
        return redirect('/search');
    }
    //お気に入り追加

    public function deleteFavorite(Request $request)
    {
        $user = Auth::user();
        $restaurant_id = $request->restaurant_id;
        if ($user->isFavorite($restaurant_id)) {
            $user->favoriteToRestaurants()->detach($restaurant_id);
        }
        if ($request->page_status == 'mypage') {
            return redirect('/mypage');
        } else {

            $request->session()->put('access_time', $request->access_time);
            $request->session()->put('position', $request->position);
            return redirect('/search');
        }
    }
    //お気に入り削除

    /**
     * 飲食店詳細ページの表示
     *
     * @param RestaurantRequest $request リクエスト
     * @return view reservation_done.blade
     */
    public function detail(Request $request)
    {
        // セッションキー、画面遷移情報、飲食店情報、レビュー、支払方法の取得
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $restaurant = Restaurant::find($request->restaurant_id);
        $reviews = Review::where('restaurant_id', $request->restaurant_id)->get();
        $payments = Payment::all();

        // 飲食店詳細画面の表示
        return view('detail', compact('restaurant', 'access_time', 'page_status', 'reviews', 'payments'));
    }

    /**
     * 予約
     *
     * @param RestaurantRequest $request リクエスト
     * @return view reservation_done.blade
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
            return view('done.reservation_done', compact('reservation_array', 'access_time', 'page_status', 'message'));
        }

        // Stripe決済の場合
        if ($reservation_array['payment_id'] == 2) {
            // Stripe上に顧客として存在していない場合
            if (!$user->stripe_id) {
                $message = 'カード情報を登録してください';
                return view('done.reservation_done', compact('message'));
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
                    return view('done.reservation_done', compact('reservation_array', 'access_time', 'page_status', 'message'));
                }

                // カード情報が存在しない場合
                $message = 'カード情報を登録してください';
                return view('done.reservation_done', compact('message'));
            }
        }

        // メッセージの生成
        $message = '予約できませんでした';

        // 予約完了ページの表示
        return view('done.reservation_done', compact('reservation_array', 'access_time', 'page_status', 'message'));
    }

    /**
     * 予約削除
     *
     * @param Request $request リクエスト
     * @return view reservation_delete_done.blade
     */
    public function deleteReservation(Request $request)
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
                return view('done.reservation_delete_done', compact('message', 'href'));
            }

            // マイページから遷移の場合
            if ($page_status == 'mypage') {
                $message = '予約情報が存在しません';
                $href = '/mypage';
                return view('done.reservation_delete_done', compact('message', 'href'));
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
            return view('done.reservation_delete_done', compact('message', 'href'));
        }

        // マイページから遷移の場合
        if ($page_status == 'mypage') {
            $href = '/mypage';
            return view('done.reservation_delete_done', compact('message', 'href'));
        }
    }

    /**
     * 予約変更ページの表示
     *
     * @param Request $request リクエスト
     * @return view reservation_change.blade
     */
    public function changeReservationView(Request $request)
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
                return view('done.reservation_change_done', compact('message', 'href', 'page_status'));
            }

            // マイページから遷移の場合
            if ($page_status == 'mypage') {
                $message = '予約情報が存在しません';
                $href = '/mypage';
                return view('done.reservation_change_done', compact('message', 'href', 'page_status'));
            }
        }

        // 予約日が今日より前の場合
        if ($reservation->date < $dt_now->format("Y-m-d")) {

            // 予約一覧画面から遷移の場合
            if ($page_status == 'reservation_record') {
                $message = '予約日時を過ぎているため、変更できません';
                $href = '/reservation/record/?date=' . $request->date;
                return view('done.reservation_change_done', compact('message', 'href', 'page_status'));
            }

            // マイページから遷移の場合
            if ($page_status == 'mypage') {
                $message = '予約日時を過ぎているため、変更できません';
                $href = '/mypage';
                return view('done.reservation_change_done', compact('message', 'href', 'page_status'));
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
                    return view('reservation_change', compact('reservation', 'page_status', 'dt', 'href', 'payments'));
                }

                // マイページから遷移の場合
                if ($page_status == 'mypage') {
                    $href = '/mypage';
                    return view('reservation_change', compact('reservation', 'page_status', 'href', 'payments'));
                }
            }

            // 予約時間が現在の時刻以前の場合
            if ($reservation->time <= $dt_now->format("H:i")) {

                // 予約一覧画面から遷移の場合
                if ($page_status == 'reservation_record') {
                    $message = '予約日時を過ぎているため、変更できません';
                    $href = '/reservation/record/?date=' . $request->date;
                    return view('done.reservation_change_done', compact('message', 'href', 'page_status'));
                }

                // マイページから遷移の場合
                if ($page_status == 'mypage') {
                    $message = '予約日時を過ぎているため、変更できません';
                    $href = '/mypage';
                    return view('done.reservation_change_done', compact('message', 'href', 'page_status'));
                }
            }
        }

        // 予約日が明日以降の場合
        if ($reservation->date > $dt_now->format("Y-m-d")) {

            // 予約一覧画面から遷移の場合
            if ($page_status == 'reservation_record') {
                $dt = $request->date;
                $href = '/reservation/record/?date=' . $dt;
                return view('reservation_change', compact('reservation', 'page_status', 'dt', 'href', 'payments'));
            }

            // マイページから遷移の場合
            if ($page_status == 'mypage') {
                $href = '/mypage';
                return view('reservation_change', compact('reservation', 'page_status', 'href', 'payments'));
            }
        }
    }

    /**
     * 予約変更
     *
     * @param ReservationRequest $request リクエスト
     * @return view reservation_change_done.blade
     */
    public function changeReservation(ReservationRequest $request)
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
                return view('done.reservation_change_done', compact('message', 'href', 'page_status'));
            }

            // マイページから遷移の場合
            if ($page_status == 'mypage') {
                $message = '予約情報が存在しません';
                $href = '/mypage';
                return view('done.reservation_change_done', compact('message', 'href', 'page_status'));
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
                        return view('done.reservation_change_done', compact('message', 'href'));
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
                            return view('done.reservation_change_done', compact('message', 'href'));
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
                    return view('done.reservation_change_done', compact('message', 'href'));
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
                        return view('done.reservation_change_done', compact('message', 'href'));
                    }
                }
            }
        }

        // 予約一覧画面から遷移の場合
        if ($page_status == 'reservation_record') {
            $href = '/reservation/record/?date=' . $request->date;
            return view('done.reservation_change_done', compact('message', 'href', 'page_status'));
        }

        // マイページから遷移の場合
        if ($page_status == 'mypage') {
            $href = '/mypage';
            return view('done.reservation_change_done', compact('message', 'href', 'page_status'));
        }
    }

    public function search(Request $request)
    {
        $access_time = $request->access_time;
        if (empty($access_time)) {
            $access_time = $request->session()->pull('access_time', Carbon::now()->format('Y/m/d  H:i:s.v'));
        }
        if ($request->status === "search") {
            $search_condition = [
                'access_time' => $access_time,
                'search_area_id' => $request->area_id,
                'search_genre_id' => $request->genre_id,
                'search_keyword' => $request->keyword,
            ];
            $request->session()->put($access_time, $search_condition);
        } else {
            $search_condition_default = [
                'access_time' => $access_time,
                'search_area_id' => '',
                'search_genre_id' => '',
                'search_keyword' => '',
            ];
            $search_condition = $request->session()->get($access_time, $search_condition_default);
        }
        $position = $request->session()->pull('position', 0);
        $restaurants = Restaurant::AreaSearch($search_condition['search_area_id'])->GenreSearch($search_condition['search_genre_id'])->KeywordSearch($search_condition['search_keyword'])->get();
        foreach ($restaurants as $restaurant) {
            $restaurant['review_total'] = Review::where('restaurant_id', $restaurant->id)->count();
            if ($restaurant['review_total'] > 0) {
                $reviews = Review::where('restaurant_id', $restaurant->id)->get();
                $i = 0;
                foreach ($reviews as $review) {
                    $i += $review->evaluation;
                }
                $restaurant['review_average'] = number_format($i / $restaurant['review_total'], 1);
            }
        }
        $areas = Area::all();
        $genres = Genre::all();
        return view('index', compact('restaurants', 'areas', 'genres', 'search_condition', 'position'));
    }
    //検索

    public function mypage(Request $request)
    {
        $dt_now = Carbon::now();
        $reservations = Reservation::where('user_id', Auth::id())->where([['date', '=', $dt_now->format("Y-m-d")], ['time', '>=', $dt_now->format("H:i")]])->orwhere('user_id', Auth::id())->where('date', '>', $dt_now->format("Y-m-d"))->orderBy('date', 'asc')->orderBy('time', 'asc')->get();
        $reservations_history = Reservation::where('user_id', Auth::id())->where([['date', '=', $dt_now->format("Y-m-d")], ['time', '<', $dt_now->format("H:i")]])->orwhere('user_id', Auth::id())->where('date', '<', $dt_now->format("Y-m-d"))->orderBy('date', 'asc')->orderBy('time', 'asc')->get();
        $favorites = Favorite::where('user_id', Auth::id())->get();
        foreach ($favorites as $favorite) {
            $favorite['review_total'] = Review::where('restaurant_id', $favorite->restaurant_id)->count();
            if ($favorite['review_total'] > 0) {
                $reviews = Review::where('restaurant_id', $favorite->restaurant_id)->get();
                $i = 0;
                foreach ($reviews as $review) {
                    $i += $review->evaluation;
                }
                $favorite['review_average'] = number_format($i / $favorite['review_total'], 1);
            }
        }
        $page_status = 'mypage';
        return view('mypage', compact('reservations', 'reservations_history', 'favorites', 'page_status'));
    }
    //マイページの表示

    public function review(Request $request)
    {
        $restaurant = Restaurant::find($request->restaurant_id);
        $review = Review::where([['user_id', '=', Auth::id()], ['restaurant_id', '=', $request->restaurant_id]])->first();
        return view('review', compact('restaurant', 'review'));
    }
    //レビューページの表示

    public function reviewPost(ReviewRequest $request)
    {
        $review_array = $request->only(['user_id', 'restaurant_id', 'evaluation', 'comment']);
        Review::create($review_array);
        return view('review_done', compact('review_array'));
    }
    //レビュー投稿

    /**
     * 店舗代表者登録ページの表示
     *
     * @return view representative_register.blade
     */
    public function representativeRegisterView()
    {
        return view('representative_register');
    }

    public function representativeRegister(Request $request)
    {
        $request->validate([
            'email' => [
                // ユーザーの存在確認と権限の確認
                function ($attribute, $value, $fail) {
                    $user = User::where('email', '=', $value)->first();

                    if ($user === null) {
                        $fail('ユーザーが存在しません');
                    } else {
                        $role = Role::where('id', '=', $user->role_id)->first();

                        if ($role->name == '店舗代表者') {
                            $fail('このユーザーは店舗代表者です');
                        } elseif ($role->name != '利用者') {
                            $fail($role->name . 'は店舗代表者にはなれません');
                        }
                    }
                },
            ],
        ]);

        User::where('email', '=', $request->email)->first()->update(['role_id' => 2]);

        $message = '店舗代表者に登録しました';

        return view('done', compact('message'));
    }
    // 店舗代表者登録

    public function restaurantCreateView(Request $request)
    {
        $user = Auth::user();
        if ($user->toRestaurants()->first() == null) {
            $areas = Area::all();
            $genres = Genre::all();
            return view('restaurant_create', compact('areas', 'genres'));
        } else {
            $message = '店舗情報は作成済です';
        }
        return view('done', compact('message'));
    }
    //飲食店作成ページの表示

    public function restaurantCreate(RestaurantRequest $request)
    {
        $user = Auth::user();
        if ($user->toRestaurants()->first() == null) {
            $image = $request->file('image')->store('public/images/');
            $restaurant_array = $request->only(['name', 'area_id', 'genre_id', 'detail']);
            $restaurant_array['image'] = 'storage/images/' . basename($image);
            $restaurant = Restaurant::create($restaurant_array);
            $user->toRestaurants()->attach($restaurant->id);
            $message = '店舗情報を作成しました';
        } else {
            $message = '店舗情報は作成済です';
        }
        return view('done', compact('message'));
    }
    //飲食店作成

    public function restaurantEditView(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->toRestaurants()->first();
        if (!$restaurant == null) {
            $areas = Area::all();
            $genres = Genre::all();
            return view('restaurant_edit', compact('areas', 'genres', 'restaurant'));
        } else {
            $message = '店舗情報を作成してください';
        }
        return view('done', compact('message'));
    }
    //飲食店更新ページの表示

    public function restaurantEdit(RestaurantRequest $request)
    {
        $restaurant = Restaurant::find($request->restaurant_id);
        $image = $request->file('image');
        if (!empty($image)) {
            Storage::disk('public')->delete('images/' . basename($restaurant->image));
            $image_path = $image->store('public/images/');
            $restaurant_array = $request->only(['name', 'area_id', 'genre_id', 'detail']);
            $restaurant_array['image'] = 'storage/images/' . basename($image_path);
            $restaurant->update($restaurant_array);
        } else {
            $restaurant_array = $request->only(['name', 'area_id', 'genre_id', 'detail']);
            $restaurant->update($restaurant_array);
        }
        $message = '店舗情報を更新しました';
        return view('done', compact('message'));
    }
    // 飲食店更新

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
            return view('reservation_record', compact('reservations', 'reservations_history', 'dt', 'page_status'));
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
                return view('reservation_record', compact('reservations', 'reservations_history', 'dt', 'page_status'));
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
                return view('reservation_record', compact('reservations', 'reservations_history', 'dt', 'page_status'));
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
            return view('reservation_record', compact('reservations', 'reservations_history', 'dt', 'page_status'));
        }
    }

    /**
     * メール送信ページの表示
     *
     * @return view notification.blade
     */
    public function notificationView()
    {
        return view('notification');
    }

    /**
     * メール送信
     *
     * @param ReservationRequest $request リクエスト
     * @return view notification_done.blade
     */
    public function notification(Request $request)
    {
        // バリデーションルール、メッセージの作成
        $rules = [
            'subject' => 'required|string',
            'message' => 'required|string'
        ];
        $messages = [
            'subject.required' => '件名を必ず入力してください',
            'subject.string' => '件名を文字列で入力してください',
            'message.required' => 'メッセージを入力してください',
            'message.string' => 'メッセージを文字列で入力してください',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        // バリデーションエラーの場合
        if ($validator->fails()) {
            return redirect('/notification')->withErrors($validator)->withInput();
        }

        // バリデーション済みデータの取得
        $mail_data = $validator->validated();

        // ユーザー情報の取得
        $users = User::all();

        // 宛先が存在しない場合
        if (count($users) == 0) {
            $message = '宛先が存在しません';
            return view('done.notification_done', compact('message'));
        }

        // 送信に失敗したアドレスを入れる変数
        $error_emails = [];

        // メール送信処理
        foreach ($users as $user) {
            $mail = new NotificationMail($mail_data);
            Mail::to($user->email)->send($mail);

            // メール送信失敗の場合
            if (count(Mail::failures()) > 0) {
                $error_emails[] = $user->email;
            }
        }

        // メール送信が一つでも失敗の場合
        if (count($error_emails) > 0) {
            $message = '下記アドレスに送信できませんでした';
            return view('done.notification_done', compact('message', 'error_emails'));
        }

        // メール送信がすべて成功の場合
        if (count($error_emails) == 0) {
            $message = 'メールを送信しました';
            return view('done.notification_done', compact('message', 'error_emails'));
        }
    }

    /**
     * 予約QRコード読み取りページの表示
     *
     * @return view reservation_qr.blade
     */
    public function reservationQr()
    {
        return view('reservation_qr');
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
        return view('reservation_confirm', compact('reservation'));
    }

    /**
     * カード情報確認ページの表示
     *
     * @return view card.blade
     */
    public function card()
    {
        \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

        // カード情報を入れる変数
        $default_card = null;

        // ユーザー情報の取得
        $user = Auth::user();

        // Stripe上に顧客として存在する場合
        if (!empty($user->stripe_id)) {
            $card = \Stripe\Customer::allSources(
                $user->stripe_id,
                [
                    'limit'  => 1,
                    'object' => 'card',
                ]
            )->data;

            // カード情報が存在する場合
            if (count($card) > 0) {
                $card = $card[0];
                $default_card = [
                    'number' => str_repeat('*', 8) . $card->last4,
                    'brand' => $card->brand,
                    'exp_month' => $card->exp_month,
                    'exp_year' => $card->exp_year,
                ];
            }
        }

        // カード情報確認画面の表示
        return view('card', compact('default_card'));
    }

    /**
     * カード情報登録ページの表示
     *
     * @return view card_create.blade
     */
    public function cardCreateView()
    {
        return view('card_create');
    }

    /**
     * カード情報登録
     *
     * @param ReservationRequest $request リクエスト
     * @return view payment_create_done.blade
     */
    public function cardCreate(Request $request)
    {
        // Stripeトークン、ユーザー情報の取得
        $token = $request->stripeToken;
        $user = Auth::user();

        // Stripeトークンが存在する場合
        if ($token) {

            // Stripe上に顧客として存在していない場合
            if (!$user->stripe_id) {
                \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));
                $result = false;

                // Stripe上に顧客情報を保存
                try {
                    $customer = \Stripe\Customer::create([
                        'card' => $token,
                        'name' => $user->name,
                        'description' => 'ユーザーID：' . $user->id
                    ]);
                } catch (\Stripe\Exception\CardException $e) {
                    $message = 'カード情報を登録できませんでした';
                    return view('done.card_create_done', compact('message'));
                }

                // Stripe上に顧客情報を保存できた場合
                if (isset($customer->id)) {
                    $user->stripe_id = $customer->id;
                    $user->update();
                    $message = 'カード情報を登録しました';
                    return view('done.card_create_done', compact('message'));
                }

                $message = 'カード情報を登録できませんでした';
                return view('done.card_create_done', compact('message'));
            }

            // Stripe上に顧客として存在している場合
            if ($user->stripe_id) {
                \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

                // 顧客情報、カード情報を取得
                $customer = \Stripe\Customer::retrieve($user->stripe_id);
                $card = \Stripe\Customer::allSources(
                    $user->stripe_id,
                    [
                        'limit'  => 1,
                        'object' => 'card',
                    ]
                )->data;

                // カード情報が存在している場合
                if (count($card) > 0) {
                    $message = 'カード情報を登録できませんでした';
                    return view('done.card_create_done', compact('message'));
                }

                try {
                    $stripe = new \Stripe\StripeClient(Config::get('stripe.stripe_secret_key'));
                    $card = $stripe->customers->createSource($user->stripe_id, ['source' => $token]);

                    if (isset($customer)) {
                        $customer->default_source = $card["id"];
                        $customer->save();
                    }
                } catch (\Stripe\Exception\CardException $e) {
                    $message = 'カード情報を登録できませんでした';
                    return view('done.card_create_done', compact('message'));
                }
                $message = 'カード情報を登録しました';
                return view('done.card_create_done', compact('message'));
            }
        }

        $message = 'カード情報を登録できませんでした';
        return view('done.card_create_done', compact('message'));
    }

    /**
     * カード情報削除
     *
     * @return view payment_delete_done.blade
     */
    public function cardDelete()
    {
        // ユーザー情報を取得
        $user = Auth::user();

        // 未支払の予約情報の取得
        $no_payment_reservation = $user->reservations()->where('payment_status', 1)->first();

        // 未支払の予約情報が存在しない場合
        if ($no_payment_reservation == null) {

            // Stripe上に顧客として存在しない場合
            if (empty($user->stripe_id)) {
                $message = 'カード情報が存在しません';
                return view('done.card_delete_done', compact('message'));
            }

            // Stripe上に顧客として存在する場合
            if (!empty($user->stripe_id)) {
                \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

                // 顧客情報、カード情報を取得
                $customer = \Stripe\Customer::retrieve($user->stripe_id);
                $card = \Stripe\Customer::allSources(
                    $user->stripe_id,
                    [
                        'limit'  => 1,
                        'object' => 'card',
                    ]
                )->data;

                // カード情報が存在している場合
                if (count($card) > 0) {
                    $card = $card[0];
                    \Stripe\Customer::deleteSource(
                        $user->stripe_id,
                        $card->id
                    );
                    $message = 'カード情報を削除しました';
                    return view('done.card_delete_done', compact('message'));
                }

                // カード情報が存在しない場合
                if (count($card) == 0) {
                    $message = 'カード情報が存在しません';
                    return view('done.card_delete_done', compact('message'));
                }
            }
        }

        // 未支払の予約情報が存在する場合
        if ($no_payment_reservation != null) {
            $message = '未決済の予約情報が存在するため、カード情報を削除できませんでした';
            return view('done.card_delete_done', compact('message'));
        }
    }

    /**
     * カード情報更新ページの表示
     *
     * @return view payment_create.blade
     */
    public function cardUpdateView()
    {
        return view('card_update');
    }

    /**
     * カード情報更新
     *
     * @param ReservationRequest $request リクエスト
     * @return view payment_update_done.blade
     */
    public function cardUpdate(Request $request)
    {
        // Stripeトークン、ユーザー情報の取得
        $token = $request->stripeToken;
        $user = Auth::user();

        // Stripeトークンが存在する場合
        if ($token) {

            // Stripe上に顧客として存在していない場合
            if (!$user->stripe_id) {
                $message = 'カード情報を更新できませんでした';
                return view('done.card_create_done', compact('message'));
            }

            // Stripe上に顧客として存在している場合
            if ($user->stripe_id) {
                \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

                // 顧客情報、カード情報を取得
                $customer = \Stripe\Customer::retrieve($user->stripe_id);
                $card = \Stripe\Customer::allSources(
                    $user->stripe_id,
                    [
                        'limit'  => 1,
                        'object' => 'card',
                    ]
                )->data;

                // カード情報が存在している場合
                if (count($card) > 0) {
                    $card = $card[0];
                    \Stripe\Customer::deleteSource(
                        $user->stripe_id,
                        $card->id
                    );
                }

                try {
                    $stripe = new \Stripe\StripeClient(Config::get('stripe.stripe_secret_key'));
                    $card = $stripe->customers->createSource($user->stripe_id, ['source' => $token]);

                    if (isset($customer)) {
                        $customer->default_source = $card["id"];
                        $customer->save();
                    }
                } catch (\Stripe\Exception\CardException $e) {
                    $message = 'カード情報を更新できませんでした';
                    return view('done.card_update_done', compact('message'));
                }

                $message = 'カード情報を更新しました';
                return view('done.card_update_done', compact('message'));
            }
        }

        $message = 'カード情報を更新できませんでした';
        return view('done.card_update_done', compact('message'));
    }

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
            return view('payment', compact('reservation', 'dt'));
        }

        // 支払済みの場合
        if ($reservation->payment_status == 2) {
            $message = '既に決済が完了しています';
            return view('done.payment_done', compact('message', 'dt'));
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
                return view('done.payment_done', compact('message', 'reservation', 'dt'));
            }

            // 支払済みにする
            $reservation->payment_status = 2;
            $reservation->update();

            // 決済完了画面の表示
            $message = '決済が完了しました';
            return view('done.payment_done', compact('message', 'dt'));
        }

        // 支払済みの場合
        if ($reservation->payment_status == 2) {
            $message = '既に決済が完了しています';
            return view('done.payment_done', compact('message', 'dt'));
        }
    }
}
