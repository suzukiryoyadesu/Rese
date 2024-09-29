<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Models\Favorite;

class ShopController extends Controller
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
        $restaurants = Restaurant::all();
        $areas = Area::all();
        $genres = Genre::all();
        return view('index', compact('restaurants', 'areas', 'genres', 'search_condition'));
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
        $request->session()->put('access_time', $request->access_time);
        if ($request->page_status == 'mypage') {
            return redirect('/mypage');
        } else {
            return redirect('/search');
        }
    }
    //お気に入り削除

    public function detail(Request $request)
    {
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $restaurant = Restaurant::find($request->restaurant_id);
        return view('detail', compact('restaurant', 'access_time', 'page_status'));
    }
    //飲食店詳細ページの表示

    public function reservation(Request $request)
    {
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $reservation_array = $request->only(['restaurant_id', 'date', 'time', 'number']);
        $reservation_array['user_id'] = Auth::id();
        Reservation::create($reservation_array);
        return view('done', compact('reservation_array','access_time', 'page_status'));
    }
    //予約

    public function deleteReservation(Request $request)
    {
        Reservation::find($request->reservation_id)->delete();
        return redirect('/mypage');
    }
    //予約削除

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
        $restaurants = Restaurant::AreaSearch($search_condition['search_area_id'])->GenreSearch($search_condition['search_genre_id'])->KeywordSearch($search_condition['search_keyword'])->get();
        $areas = Area::all();
        $genres = Genre::all();
        return view('index', compact('restaurants', 'areas', 'genres', 'search_condition'));
    }
    //検索

    public function mypage(Request $request)
    {
        $dt_now = Carbon::now();
        $reservations = Reservation::where('date', '>=', $dt_now->format("Y-m-d"))->orderBy('date', 'asc')->orderBy('time', 'asc')->get();
        $favorites = Favorite::where('user_id', Auth::id())->get();
        $page_status = 'mypage';
        return view('mypage', compact('reservations', 'favorites', 'page_status'));
    }
    //マイページの表示

    public function thanks()
    {
        return view('auth.thanks');
    }
}
