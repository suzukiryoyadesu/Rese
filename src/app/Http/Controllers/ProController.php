<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Area;
use App\Models\Genre;
use Illuminate\Support\Facades\Validator;

class ProController extends Controller
{
    /**
     * 口コミページの表示
     *
     * @param Request $request リクエスト
     * @return view review.blade
     */
    public function review(Request $request)
    {
        // セッションキー、画面遷移情報、飲食店情報の取得
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $restaurant = Restaurant::find($request->restaurant_id);

        // 口コミ画面の表示
        return view('pro.review', compact('restaurant', 'access_time', 'page_status'));
    }

    /**
     * お気に入り追加
     *
     * @param Request $request リクエスト
     * @return view review.blade
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

        // セッションキー、画面遷移情報、飲食店情報の取得
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $restaurant = Restaurant::find($restaurant_id);

        $review_status = $request->review_status;

        // 口コミ画面からの遷移の場合
        if ($review_status == 'post') {
            return view('pro.review', compact('restaurant', 'access_time', 'page_status'));
        }

        // 口コミ画面からの遷移の場合
        if ($review_status == 'edit') {
            // 口コミの取得
            $review = Review::where([
                ['user_id', '=', Auth::id()],
                ['restaurant_id', '=', $restaurant_id]
            ])->first();

            // 口コミが存在しない場合
            if (empty($review)) {
                // メッセージのセット
                $message = '口コミが存在しません';

                // 口コミ編集完了画面の表示
                return view('pro.review_edit_done', compact('access_time', 'page_status', 'restaurant_id', 'message'));
            }

            return view('pro.review_edit', compact('restaurant', 'access_time', 'page_status', 'review'));
        }
    }

    /**
     * お気に入り削除
     *
     * @param Request $request リクエスト
     * @return view review.blade
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

        // セッションキー、画面遷移情報、飲食店情報の取得
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $restaurant = Restaurant::find($restaurant_id);

        $review_status = $request->review_status;

        // 口コミ画面からの遷移の場合
        if ($review_status == 'post') {
            return view('pro.review', compact('restaurant', 'access_time', 'page_status'));
        }

        // 口コミ画面からの遷移の場合
        if ($review_status == 'edit') {
            // 口コミの取得
            $review = Review::where([
                ['user_id', '=', Auth::id()],
                ['restaurant_id', '=', $request->restaurant_id]
            ])->first();

            // 口コミが存在しない場合
            if (empty($review)) {
                // メッセージのセット
                $message = '口コミが存在しません';

                // 口コミ編集完了画面の表示
                return view('pro.review_edit_done', compact('access_time', 'page_status', 'restaurant_id', 'message'));
            }

            return view('pro.review_edit', compact('restaurant', 'access_time', 'page_status', 'review'));
        }
    }

    /**
     * レビュー投稿
     *
     * @param ReviewRequest $request リクエスト
     * @return view review_done.blade
     */
    public function reviewPost(ReviewRequest $request)
    {
        // セッションキー、画面遷移情報、飲食店IDの取得
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $restaurant_id = $request->restaurant_id;

        // レビューの取得
        $review = Review::where([
            ['user_id', '=', Auth::id()],
            ['restaurant_id', '=', $restaurant_id]
        ])->first();

        // メッセージのセット
        $message = '口コミは投稿済です';

        // 口コミが存在しない場合
        if (empty($review)) {
            // 送信された画像ファイルの取得
            $image = $request->file('image');

            // 画像ファイルが送信された場合
            if (!empty($image)) {
                $image_path = $image->store('public/images/');
                $review_array = $request->only(['user_id', 'restaurant_id', 'evaluation', 'comment']);
                $review_array['image'] = 'storage/images/' . basename($image_path);
                Review::create($review_array);
            }

            // 画像ファイルが送信されなかった場合
            if (empty($image)) {
                $review_array = $request->only(['user_id', 'restaurant_id', 'evaluation', 'comment']);
                Review::create($review_array);
            }

            // メッセージのセット
            $message = '口コミ投稿ありがとうございます';
        }

        // 口コミ完了画面の表示
        return view('pro.review_done', compact('access_time', 'page_status', 'restaurant_id', 'message'));
    }

    /**
     * 口コミ編集ページの表示
     *
     * @param Request $request リクエスト
     * @return view review.blade
     */
    public function reviewEditView(Request $request)
    {
        // セッションキー、画面遷移情報、飲食店情報の取得
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $restaurant = Restaurant::find($request->restaurant_id);

        // 口コミの取得
        $review = Review::where([
            ['user_id', '=', Auth::id()],
            ['restaurant_id', '=', $request->restaurant_id]
        ])->first();

        // 口コミが存在しない場合
        if ($review == null) {
            return view('pro.review_edit_done', compact('access_time', 'page_status', 'restaurant_id', 'message'));
        }

        // 口コミ編集画面の表示
        return view('pro.review_edit', compact('restaurant', 'access_time', 'page_status', 'review'));
    }

    /**
     * 口コミ編集
     *
     * @param ReviewRequest $request リクエスト
     * @return view review_edit_done.blade
     */
    public function reviewEdit(ReviewRequest $request)
    {
        // セッションキー、画面遷移情報、飲食店IDの取得
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $restaurant_id = $request->restaurant_id;

        // 口コミの取得
        $review = Review::where([
            ['user_id', '=', Auth::id()],
            ['restaurant_id', '=', $restaurant_id]
        ])->first();

        // メッセージのセット
        $message = '口コミが存在しません';

        // 口コミが存在する場合
        if (!empty($review)) {
            // 送信された画像ファイルの取得
            $image = $request->file('image');

            // 画像ファイルが送信された場合
            if (!empty($image)) {
                Storage::disk('public')->delete('images/' . basename($review->image));
                $image_path = $image->store('public/images/');
                $review_array = $request->only(['user_id', 'restaurant_id', 'evaluation', 'comment']);
                $review_array['image'] = 'storage/images/' . basename($image_path);
                $review->update($review_array);
            }

            // 画像ファイルが送信されなかった場合
            if (empty($image)) {
                $review_array = $request->only(['user_id', 'restaurant_id', 'evaluation', 'comment']);
                $review->update($review_array);
            }

            // メッセージのセット
            $message = '口コミを編集しました';
        }

        // 口コミ編集完了画面の表示
        return view('pro.review_edit_done', compact('access_time', 'page_status', 'restaurant_id', 'message'));
    }

    /**
     * レビュー削除
     *
     * @param Request $request リクエスト
     * @return view review_delete_done.blade
     */
    public function reviewDelete(Request $request)
    {
        // セッションキー、画面遷移情報、飲食店ID、口コミID、ユーザー情報の取得
        $access_time = $request->access_time;
        $page_status = $request->page_status;
        $restaurant_id = $request->restaurant_id;
        $review_id = $request->review_id;
        $user = Auth::User();

        // レビューの取得
        $review = Review::find($review_id);

        // メッセージのセット
        $message = '口コミが存在しません';

        // 口コミが存在する場合
        if (!empty($review)) {
            // メッセージのセット
            $message = '口コミを削除できませんでした';

            // ユーザーが管理者の場合
            if ($user->role_id == 3) {
                Storage::disk('public')->delete('images/' . basename($review->image));
                $review->delete();

                // メッセージのセット
                $message = '口コミを削除しました';
            }

            // ユーザーが管理者以外の場合
            if ($user->role_id != 3) {
                if ($user->id == $review->user_id) {
                    Storage::disk('public')->delete('images/' . basename($review->image));
                    $review->delete();

                    // メッセージのセット
                    $message = '口コミを削除しました';
                }
            }
        }

        // 口コミ削除完了画面の表示
        return view('pro.review_delete_done', compact('access_time', 'page_status', 'restaurant_id', 'message'));
    }

    /**
     * ソート
     *
     * @param Request $request リクエスト
     * @return view index.blade
     */
    public function searchSort(Request $request)
    {
        $access_time = $request->access_time;
        $position = $request->session()->pull('position', 0);

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
            $sort_id = $request->session()->get($access_time . "sort", '');
        } elseif ($request->status == "sort") {
            $search_condition_default = [
                'access_time' => $access_time,
                'search_area_id' => '',
                'search_genre_id' => '',
                'search_keyword' => '',
            ];
            $search_condition = $request->session()->get($access_time, $search_condition_default);
            $sort_id = $request->sort_id;
            $request->session()->put($access_time . "sort", $sort_id);
        } else {
            $search_condition_default = [
                'access_time' => $access_time,
                'search_area_id' => '',
                'search_genre_id' => '',
                'search_keyword' => '',
            ];
            $search_condition = $request->session()->get($access_time, $search_condition_default);
            $sort_id = $request->session()->get($access_time . "sort", '');
        }

        // 飲食店情報の取得
        $restaurants = Restaurant::AreaSearch($search_condition['search_area_id'])->GenreSearch($search_condition['search_genre_id'])->KeywordSearch($search_condition['search_keyword'])->get();

        // レビューの取得、計算
        foreach ($restaurants as $restaurant) {
            // レビュー件数の取得
            $restaurant['review_total'] = Review::where('restaurant_id', $restaurant->id)->count();

            // レビューが存在する場合
            if ($restaurant['review_total'] > 0) {
                $reviews = Review::where('restaurant_id', $restaurant->id)->get();

                // レビューの平均計算
                $i = 0;
                foreach ($reviews as $review) {
                    $i += $review->evaluation;
                }
                $restaurant['review_average'] = number_format($i / $restaurant['review_total'], 1);
            }
        }

        if ($sort_id == 1) {
            $restaurants = $restaurants->shuffle();
        }

        if ($sort_id == 2) {
            $restaurants = $restaurants->sortByDesc('review_average');
        }

        if ($sort_id == 3) {
            $restaurants_review = $restaurants->where('review_average', '>', 0)->sortBy('review_average');
            $restaurants_review_no = $restaurants->where('review_average', '=', null);
            $restaurants = $restaurants_review->concat($restaurants_review_no);
        }

        // エリア、ジャンルの取得
        $areas = Area::all();
        $genres = Genre::all();

        return view('pro.index', compact('restaurants', 'areas', 'genres', 'search_condition', 'position', 'sort_id'));
    }

    /**
     * CSVインポートページの表示
     *
     * @return view csv.blade
     */
    public function Csv()
    {
        // CSVインポートの表示
        return view('pro.csv');
    }

    /**
     * CSVインポート
     *
     * @param Request $request リクエスト
     * @return view csv.blade
     */
    public function CsvImport(Request $request)
    {
        $message = '店舗情報を作成できませんでした';

        if ($request->hasFile('csv')) {
            // リクエストからファイルを取得
            $file = $request->file('csv');
            $path = $file->getRealPath();

            // ファイルを開く
            $fp = fopen($path, 'r');

            // ヘッダー行をスキップ
            fgetcsv($fp);

            $i = 2;

            // 1行ずつ読み込む
            while (($csv_data = fgetcsv($fp)) !== FALSE) {
                $restaurant_array = [
                    'area_id' => $csv_data[0],
                    'genre_id' => $csv_data[1],
                    'name' => $csv_data[2],
                    'image' => $csv_data[3],
                    'detail' => $csv_data[4],
                ];

                // バリデーションルール、メッセージの作成
                $rules = [
                    'area_id' => 'required|integer|min:1|max:3',
                    'genre_id' => 'required|integer|min:1|max:5',
                    'name' => 'required|string|max:50',
                    'image' => 'ends_with:.jpeg,.png,.jpg',
                    'detail' => 'required|string|max:400',
                ];
                $messages = [
                    'area_id.required' => $i . '行目：' . 'エリアを必ず入力してください',
                    'area_id.integer' => $i . '行目：' . 'エリアを選択してください',
                    'area_id.min' => $i . '行目：' . '指定されているエリアを選択してください',
                    'area_id.max' => $i . '行目：' . '指定されているエリアを選択してください',
                    'genre_id.required' => $i . '行目：' . 'ジャンルを必ず入力してください',
                    'genre_id.integer' => $i . '行目：' . 'ジャンルを選択してください',
                    'genre_id.min' => $i . '行目：' . '指定されているジャンルを選択してください',
                    'genre_id.max' => $i . '行目：' . '指定されているジャンルを選択してください',
                    'name.required' => $i . '行目：' . '店名を必ず入力してください',
                    'name.string' => $i . '行目：' . '店名を文字列で入力してください',
                    'name.max' => $i . '行目：' . '店名を50文字以下で入力してください',
                    'image.ends_with' => $i . '行目：' . '画像(.png/.jpeg)を入力してください',
                    'detail.required' => $i . '行目：' . '詳細を必ず入力してください',
                    'detail.string' => $i . '行目：' . '詳細を文字列で入力してください',
                    'detail.max' => $i . '行目：' . '詳細を400文字以下で入力してください',
                ];
                $validator = Validator::make($restaurant_array, $rules, $messages);

                $i += 1;

                // バリデーションエラーの場合
                if ($validator->fails()) {
                    return redirect('/pro/csv')->withErrors($validator)->withInput();
                }
            }

            // ファイルを開く
            $fp = fopen($path, 'r');

            // ヘッダー行をスキップ
            fgetcsv($fp);

            // 1行ずつ読み込む
            while (($csv_data = fgetcsv($fp)) !== FALSE) {
                $restaurant_array = [
                    'area_id' => $csv_data[0],
                    'genre_id' => $csv_data[1],
                    'name' => $csv_data[2],
                    'image' => $csv_data[3],
                    'detail' => $csv_data[4],
                ];
                Restaurant::create($restaurant_array);
                $message = '店舗情報を作成しました';
            }
            // ファイルを閉じる
            fclose($fp);
        }
        return view('pro.csv_done', compact('message'));
    }
}
