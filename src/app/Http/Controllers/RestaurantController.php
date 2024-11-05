<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Models\Favorite;
use App\Models\Review;

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

    /**
     * お気に入り追加
     *
     * @param Request $request リクエスト
     */
    public function favoriteAdd(Request $request)
    {
        // ユーザー情報、飲食店情報の取得
        $user = Auth::user();
        $restaurant_id = $request->restaurant_id;

        // お気に入りでない場合
        if (!$user->isFavorite($restaurant_id)) {
            $user->favoriteToRestaurants()->attach($restaurant_id);
        }

        $request->session()->put('access_time', $request->access_time);
        $request->session()->put('position', $request->position);
        return redirect('/search');
    }

    /**
     * お気に入り削除
     *
     * @param Request $request リクエスト
     */
    public function favoriteDelete(Request $request)
    {
        // ユーザー情報、飲食店情報の取得
        $user = Auth::user();
        $restaurant_id = $request->restaurant_id;

        // お気に入りの場合
        if ($user->isFavorite($restaurant_id)) {
            $user->favoriteToRestaurants()->detach($restaurant_id);
        }

        // マイページからの遷移の場合
        if ($request->page_status == 'mypage') {
            return redirect('/mypage');
        }

        $request->session()->put('access_time', $request->access_time);
        $request->session()->put('position', $request->position);
        return redirect('/search');
    }

    /**
     * 飲食店詳細ページの表示
     *
     * @param Request $request リクエスト
     * @return view detail.blade
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
}
