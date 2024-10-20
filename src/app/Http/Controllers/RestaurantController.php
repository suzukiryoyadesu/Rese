<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\User;
use App\Models\Area;
use App\Models\Genre;
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

    public function detail(Request $request)
    {
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $restaurant = Restaurant::find($request->restaurant_id);
        $reviews = Review::where('restaurant_id', $request->restaurant_id)->get();
        return view('detail', compact('restaurant', 'access_time', 'page_status', 'reviews'));
    }
    //飲食店詳細ページの表示

    public function reservation(ReservationRequest $request)
    {
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $reservation_array = $request->only(['restaurant_id', 'date', 'time', 'number']);
        $reservation_array['user_id'] = Auth::id();
        Reservation::create($reservation_array);
        $message = 'ご予約ありがとうございます';
        return view('done', compact('reservation_array', 'access_time', 'page_status', 'message'));
    }
    //予約

    // 予約削除
    public function deleteReservation(Request $request)
    {
        // 現在日時、予約情報、画面遷移情報の取得
        $dt_now = Carbon::now();
        $reservation = Reservation::find($request->reservation_id);
        $page_status = $request->page_status;
        // 予約情報が存在しない場合
        if(empty($reservation)){
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
        if($reservation->date > $dt_now->format("Y-m-d")) {
            $reservation->delete();
            $message = '予約を削除しました';
        }
        // 予約一覧画面から遷移の場合
        if($page_status == 'reservation_record'){
            $href = '/reservation/record/?date=' . $request->date;
            return view('done.reservation_delete_done', compact('message', 'href'));
        }
        // マイページから遷移の場合
        if ($page_status == 'mypage') {
            $href = '/mypage';
            return view('done.reservation_delete_done', compact('message', 'href'));
        }
    }

    // 予約変更ページの表示
    public function changeReservationView(Request $request)
    {
        // 予約情報、画面遷移情報の取得
        $reservation = Reservation::find($request->reservation_id);
        $page_status = $request->page_status;
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
        // 予約一覧画面から遷移の場合
        if($page_status == 'reservation_record'){
            $dt = $request->date;
            $href = '/reservation/record/?date=' . $dt;
            return view('reservation_change', compact('reservation', 'page_status', 'dt', 'href'));
        }
        // マイページから遷移の場合
        if ($page_status == 'mypage') {
            $href = '/mypage';
            return view('reservation_change', compact('reservation', 'page_status', 'href'));
        }
    }

     // 予約変更
    public function changeReservation(ReservationRequest $request)
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
                $reservation_array = $request->only(['date', 'time', 'number']);
                $reservation->update($reservation_array);
                $message = '予約の変更が完了しました';
            }
            // 予約時間が現在の時刻以前の場合
            if ($reservation->time <= $dt_now->format("H:i")) {
                $message = '予約日時を過ぎているため、変更できません';
            }
        }
        // 予約日が明日以降の場合
        if ($reservation->date > $dt_now->format("Y-m-d")) {
            $reservation_array = $request->only(['date', 'time', 'number']);
            $reservation->update($reservation_array);
            $message = '予約の変更が完了しました';
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

    public function representativeRegisterView(Request $request)
    {
        return view('representative_register');
    }
    // 店舗代表者登録ページの表示

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
        if(!empty($image)){
            Storage::disk('public')->delete('images/' . basename($restaurant->image));
            $image_path = $image->store('public/images/');
            $restaurant_array = $request->only(['name', 'area_id', 'genre_id', 'detail']);
            $restaurant_array['image'] = 'storage/images/' . basename($image_path);
            $restaurant->update($restaurant_array);
        }else{
            $restaurant_array = $request->only(['name', 'area_id', 'genre_id', 'detail']);
            $restaurant->update($restaurant_array);
        }
        $message = '店舗情報を更新しました';
        return view('done', compact('message'));
    }
    //飲食店更新

    // 予約一覧ページの表示
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
        // 現在の日時以降か判定する変数(true:今日より前/false:今日以降)
        $before = false;
        // 日付が送信されなかった場合
        if(empty($request->date)){
            $dt = $dt_now;
        }
        // 日付が送信されてきた場合
        if(!empty($request->date)){
            $year = mb_substr($request->date, 0, 4);
            $month = mb_substr($request->date, 5, 2);
            $day = mb_substr($request->date, 8, 2);
            $dt = Carbon::createFromDate($year, $month, $day);
            // 送信された日付が現在の日時より前の場合
            if ($dt->lt($dt_now)) {
                $before = true;
            }
        }
        // 店舗情報を作成済みの場合
        if ($restaurant != null) {
            $reservations = Reservation::where('restaurant_id', $restaurant->id)->where('date', $dt->format("Y-m-d"))->orderBy('time', 'asc')->get();
            $dt = $dt->isoFormat('YYYY年MM月DD日(ddd)');
            $page_status = 'reservation_record';
            return view('reservation_record', compact('reservations', 'dt', 'before', 'page_status'));
        }
    }

    public function noPermission()
    {
        return view('no_permission');
    }

    public function thanks()
    {
        return view('auth.thanks');
    }
}
