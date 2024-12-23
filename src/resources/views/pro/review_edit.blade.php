@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pro/review_edit.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="review__content">
    <div class="restaurant__content">
        <h2>今回のご利用はいかがでしたか？</h2>
        <div class="restaurant__card">
            <div class="restaurant__img">
                <img src="{{ asset($restaurant->image) }}" alt="no image" />
            </div>
            <div class="restaurant__text">
                <h2>{{ $restaurant->name }}</h2>
                <div class="restaurant__tag">
                    <span class="tag">#{{ $restaurant->area->name }}</span>
                    <span class="tag">#{{ $restaurant->genre->name }}</span>
                </div>
                <div class="restaurant__option">
                    <a class="restaurant__option-link" href="/detail/?restaurant_id={{ $restaurant->id }}&access_time={{ $access_time }}&page_status={{ $page_status }}">
                        <button>詳しくみる</button>
                    </a>
                    @if(!Auth::user()->isFavorite($restaurant->id))
                    <!-- お気に入り追加フォーム -->
                    <form class="favorite__form" action="/pro/favorite/add" method="post">
                        @csrf
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}" />
                        <input type="hidden" name="access_time" value="{{ $access_time }}" />
                        <input type="hidden" name="page_status" value="{{ $page_status }}" />
                        <input type="hidden" name="review_status" value="edit" />
                        <button class="favorite__form-button"><i class="fa-solid fa-heart fa-2x" style="color: #eeeeee;"></i></button>
                    </form>
                    @else
                    <!-- お気に入り削除フォーム -->
                    <form class="favorite__form" action="/pro/favorite/delete" method="post">
                        @csrf
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}" />
                        <input type="hidden" name="access_time" value="{{ $access_time }}" />
                        <input type="hidden" name="page_status" value="{{ $page_status }}" />
                        <input type="hidden" name="review_status" value="edit" />
                        <button class="favorite__form-button"><i class="fa-solid fa-heart fa-2x" style="color: #eb3223;"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="review-input__content">
        <form class="review__form" action="/pro/review/edit" id="review__form" enctype="multipart/form-data" method="post">
            @csrf
            @method('patch')
            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
            <input type="hidden" name="access_time" value="{{ $access_time }}" />
            <input type="hidden" name="page_status" value="{{ $page_status }}" />
            <h2>体験を評価してください</h2>
            <div class="review-form__evaluating">
                <input class="review-form__evaluating-input" id="star5" name="evaluation" type="radio" value="5" @if( $review->evaluation==5 ) checked @endif>
                <label class="review-form__evaluating-label" for="star5"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input" id="star4" name="evaluation" type="radio" value="4" @if( $review->evaluation==4 ) checked @endif>
                <label class="review-form__evaluating-label" for="star4"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input" id="star3" name="evaluation" type="radio" value="3" @if( $review->evaluation==3 ) checked @endif>
                <label class="review-form__evaluating-label" for="star3"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input" id="star2" name="evaluation" type="radio" value="2" @if( $review->evaluation==2 ) checked @endif>
                <label class="review-form__evaluating-label" for="star2"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input" id="star1" name="evaluation" type="radio" value="1" @if( $review->evaluation==1 ) checked @endif>
                <label class="review-form__evaluating-label" for="star1"><i class="fa-solid fa-star"></i></label>
            </div>
            <div class="form__error">
                @error('evaluation')
                {{ $message }}
                @enderror
            </div>
            <h2>口コミを投稿</h2>
            <textarea name="comment" onKeyUp="countLength(value, 'text__length');" maxlength="400">{{ $review->comment }}</textarea>
            <p id="text__length">{{ mb_strlen($review->comment) }}/400 (最大文字数)</p>
            <div class="form__error">
                @error('comment')
                {{ $message }}
                @enderror
            </div>
            <h2>画像の追加</h2>
            <div id="drop__zone">
                @if(empty($review->image))
                <p id="message">クリックして写真を追加<br />またはドラッグアンドドロップ</p>
                @else
                <p id="message" style="display: none;">クリックして写真を追加<br />またはドラッグアンドドロップ</p>
                @endif
                <img src="{{ asset($review->image) }}" alt="" id="preview__image">
                <input type="file" id="file__input" name="image" onChange="imagePreview(event)">
            </div>
            <div class="form__error">
                @error('image')
                {{ $message }}
                @enderror
            </div>
        </form>
        <div class="review__form-submit">
            <button class="review-form__button" form="review__form">口コミを編集</button>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/review.js') }}"></script>
@endsection