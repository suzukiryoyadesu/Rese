@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/review.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="review__content">
    <div class="restaurant__content">
        <a class="restaurant__back-link" href="/mypage"><button>&lt;</button></a>
        <h2>{{ $restaurant->name }}</h2>
        <div class="restaurant__img">
            <img src="{{ asset($restaurant->image) }}" alt="no image" />
        </div>
        <div class="restaurant__tag">
            <span class="tag">#{{ $restaurant->area->name }}</span>
            <span class="tag">#{{ $restaurant->genre->name }}</span>
        </div>
        <div class="restaurant__text">
            <p>{!! nl2br( $restaurant->detail ) !!}</p>
        </div>
    </div>
    @if($review == null)
    <form class="review__form" action="/review/post" method="post">
        @csrf
        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
        <h2>レビュー</h2>
        <div class="review__form-frame">
            <h3>evaluation</h3>
            <div class="review-form__evaluating">
                <input class="review-form__evaluating-input" id="star5" name="evaluation" type="radio" value="5">
                <label class="review-form__evaluating-label" for="star5"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input" id="star4" name="evaluation" type="radio" value="4">
                <label class="review-form__evaluating-label" for="star4"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input" id="star3" name="evaluation" type="radio" value="3">
                <label class="review-form__evaluating-label" for="star3"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input" id="star2" name="evaluation" type="radio" value="2">
                <label class="review-form__evaluating-label" for="star2"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input" id="star1" name="evaluation" type="radio" value="1">
                <label class="review-form__evaluating-label" for="star1"><i class="fa-solid fa-star"></i></label>
            </div>
            <h3>comment</h3>
            <textarea name="comment"></textarea>
        </div>
        @if ($errors->any())
        <p class="review__error-message">・{{$errors->first()}}</p>
        @endif
        <button class="review-form__button">投稿する</button>
    </form>
    @else
    <div class="review__form">
        <h2>レビュー</h2>
        <div class="review__form-frame">
            <h3>evaluation</h3>
            <div class="review-form__evaluating">
                <input class="review-form__evaluating-input-display" id="star5" name="evaluation" type="radio" value="5" disabled="disabled" @if( $review->evaluation==5 ) checked @endif>
                <label class="review-form__evaluating-label-display" for="star5"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input-display" id="star4" name="evaluation" type="radio" value="4" disabled="disabled" @if( $review->evaluation==4 ) checked @endif>
                <label class="review-form__evaluating-label-display" for="star4"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input-display" id="star3" name="evaluation" type="radio" value="3" disabled="disabled" @if( $review->evaluation==3 ) checked @endif>
                <label class="review-form__evaluating-label-display" for="star3"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input-display" id="star2" name="evaluation" type="radio" value="2" disabled="disabled" @if( $review->evaluation==2 ) checked @endif>
                <label class="review-form__evaluating-label-display" for="star2"><i class="fa-solid fa-star"></i></label>

                <input class="review-form__evaluating-input-display" id="star1" name="evaluation" type="radio" value="1" disabled="disabled" @if( $review->evaluation==1 ) checked @endif>
                <label class="review-form__evaluating-label-display" for="star1"><i class="fa-solid fa-star"></i></label>
            </div>
            <h3>comment</h3>
            <p>{!! nl2br( $review->comment ) !!}</p>
        </div>
        <button class="review-form__button-display" type="button">投稿済</button>
    </div>
    @endif
</div>
@endsection