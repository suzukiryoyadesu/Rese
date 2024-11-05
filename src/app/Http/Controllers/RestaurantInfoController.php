<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Restaurant;
use App\Http\Requests\RestaurantRequest;

class RestaurantInfoController extends Controller
{
    /**
     * 飲食店作成ページの表示
     *
     * @param Request $request リクエスト
     * @return view restaurant_info_create.blade
     */
    public function restaurantCreateView(Request $request)
    {
        // ユーザー情報の取得
        $user = Auth::user();

        // 店舗情報を未作成の場合
        if ($user->toRestaurants()->first() == null) {
            $areas = Area::all();
            $genres = Genre::all();
            return view('restaurant-info.restaurant_info_create', compact('areas', 'genres'));
        }

        // 店舗情報を作成済の場合
        if ($user->toRestaurants()->first() != null) {
            $message = '店舗情報は作成済です';
            return view('restaurant-info.done.restaurant_info_create_done', compact('message'));
        }
    }

    /**
     * 飲食店作成
     *
     * @param RestaurantRequest $request リクエスト
     * @return view restaurant_info_create_done.blade
     */
    public function restaurantCreate(RestaurantRequest $request)
    {
        // ユーザー情報の取得
        $user = Auth::user();

        // 店舗情報を未作成の場合
        if ($user->toRestaurants()->first() == null) {
            $image = $request->file('image')->store('public/images/');
            $restaurant_array = $request->only(['name', 'area_id', 'genre_id', 'detail']);
            $restaurant_array['image'] = 'storage/images/' . basename($image);
            $restaurant = Restaurant::create($restaurant_array);
            $user->toRestaurants()->attach($restaurant->id);
            $message = '店舗情報を作成しました';
            return view('restaurant-info.done.restaurant_info_create_done', compact('message'));
        }

        // 店舗情報を作成済の場合
        if ($user->toRestaurants()->first() != null) {
            $message = '店舗情報は作成済です';
            return view('restaurant-info.done.restaurant_info_create_done', compact('message'));
        }
    }

    /**
     * 飲食店更新ページの表示
     *
     * @param Request $request リクエスト
     * @return view restaurant_info_edit.blade
     */
    public function restaurantEditView(Request $request)
    {
        // ユーザー情報、店舗情報の取得
        $user = Auth::user();
        $restaurant = $user->toRestaurants()->first();

        // 店舗情報が存在するの場合
        if ($restaurant != null) {
            $areas = Area::all();
            $genres = Genre::all();
            return view('restaurant-info.restaurant_info_edit', compact('areas', 'genres', 'restaurant'));
        }

        // 店舗情報が存在しないの場合
        if ($restaurant == null) {
            $message = '店舗情報を作成してください';
            return view('restaurant-info.done.restaurant_info_edit_done', compact('message'));
        }
    }

    /**
     * 飲食店更新
     *
     * @param RestaurantRequest $request リクエスト
     * @return view restaurant_info_edit_done.blade
     */
    public function restaurantEdit(RestaurantRequest $request)
    {
        // 店舗情報の取得
        $restaurant = Restaurant::find($request->restaurant_id);

        // 送信された画像ファイルの取得
        $image = $request->file('image');

        // 画像ファイルが送信された場合
        if (!empty($image)) {
            Storage::disk('public')->delete('images/' . basename($restaurant->image));
            $image_path = $image->store('public/images/');
            $restaurant_array = $request->only(['name', 'area_id', 'genre_id', 'detail']);
            $restaurant_array['image'] = 'storage/images/' . basename($image_path);
            $restaurant->update($restaurant_array);
        }

        // 画像ファイルが送信されなかった場合
        if (empty($image)) {
            $restaurant_array = $request->only(['name', 'area_id', 'genre_id', 'detail']);
            $restaurant->update($restaurant_array);
        }

        $message = '店舗情報を更新しました';
        return view('restaurant-info.done.restaurant_info_edit_done', compact('message'));
    }
}
