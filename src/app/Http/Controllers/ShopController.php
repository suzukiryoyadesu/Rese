<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $search_condition = [
            'area_id' => '',
            'genre_id' => '',
            'keyword' => ''
        ];
        $request->session()->put('search_condition', $search_condition);
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
        return redirect('/search');
    }
    //お気に入り削除

    public function detail(Request $request)
    {
        $restaurant = Restaurant::find($request->restaurant_id);
        return view('detail', compact('restaurant'));
    }
    //飲食店詳細ページの表示

    public function reservation(Request $request)
    {
        $reservation_array = $request->only(['restaurant_id', 'date', 'time', 'number']);
        $reservation_array['user_id'] = Auth::id();
        Reservation::create($reservation_array);
        return view('done', compact('reservation_array'));
    }
    //予約

    public function search(Request $request)
    {
        if ($request->status === "search") {
            $search_condition = [
                'area_id' => $request->area_id,
                'genre_id' => $request->genre_id,
                'keyword' => $request->keyword
            ];
            $request->session()->put('search_condition', $search_condition);
        } else {
            $search_condition_default = [
                'area_id' => '',
                'genre_id' => '',
                'keyword' => ''
            ];
            $search_condition = $request->session()->get('search_condition', $search_condition_default);
        }
        $restaurants = Restaurant::AreaSearch($search_condition['area_id'])->GenreSearch($search_condition['genre_id'])->KeywordSearch($search_condition['keyword'])->get();
        $areas = Area::all();
        $genres = Genre::all();
        return view('index', compact('restaurants', 'areas', 'genres', 'search_condition'));
    }
    //検索

    public function thanks()
    {
        return view('auth.thanks');
    }
}
