<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\Review;
use App\Http\Requests\ReviewRequest;

class ReviewController extends Controller
{
    /**
     * レビューページの表示
     *
     * @param Request $request リクエスト
     * @return view review.blade
     */
    public function review(Request $request)
    {
        // 飲食店情報、レビューの取得
        $restaurant = Restaurant::find($request->restaurant_id);
        $review = Review::where([
            ['user_id', '=', Auth::id()],
            ['restaurant_id', '=', $request->restaurant_id]
        ])->first();

        // レビュー画面の表示
        return view('review.review', compact('restaurant', 'review'));
    }

    /**
     * レビュー投稿
     *
     * @param ReviewRequest $request リクエスト
     * @return view review_done.blade
     */
    public function reviewPost(ReviewRequest $request)
    {
        // レビューの取得
        $review = Review::where([
            ['user_id', '=', Auth::id()],
            ['restaurant_id', '=', $request->restaurant_id]
        ])->first();

        // メッセージのセット
        $message = 'レビューは投稿済です';

        // レビューが存在しない場合
        if (empty($review)) {
            $review_array = $request->only(['user_id', 'restaurant_id', 'evaluation', 'comment']);
            $review = Review::create($review_array);

            // メッセージのセット
            $message = 'レビュー投稿ありがとうございます';
        }

        return view('review.done.review_done', compact('review', 'message'));
    }
}
