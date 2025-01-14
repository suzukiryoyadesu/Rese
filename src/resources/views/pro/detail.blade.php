@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pro/detail.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="detail__content">
    <div class="restaurant__content">
        @if($page_status == "mypage")
        <a class="restaurant__back-link" href="/mypage"><button>&lt;</button></a>
        @else
        <a class="restaurant__back-link" href="/pro/search/?access_time={{ $access_time }}"><button>&lt;</button></a>
        @endif
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
    <div class="reservation__content">
        <h2>予約</h2>
        <form class="reservation__form" id="reservation__form" action="/reservation" method="post">
            @csrf
            <div class="reservation__form-input">
                <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}" />
                <input type="hidden" name="access_time" value="{{ $access_time }}" />
                <input type="hidden" name="page_status" value="{{ $page_status }}" />
                <input type="date" name="date" value="{{ \Carbon\Carbon::now()->format("Y-m-d") }}" />
                <br />
                <select name="time">
                    @for($h = 0; $h < 24; $h++)
                        @for($m=0; $m < 4; $m++)
                        <option value="{{ sprintf('%02d', $h) }}:{{ sprintf('%02d', $m*15) }}">{{ sprintf('%02d', $h) }}:{{ sprintf('%02d', $m*15) }}</option>
                        @endfor
                        @endfor
                </select>
                <br />
                <select name="number">
                    @for($i = 1; $i < 100; $i++)
                        <option value="{{ $i }}">{{ $i }}人</option>
                        @endfor
                </select>
                <select name="payment_id">
                    @foreach($payments as $payment)
                    <option value="{{ $payment->id }}">{{ $payment->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="reservation__confirmation">
                <table class="confirmation__table">
                    <tr class="confirmation__row">
                        <th class="confirmation__header">Shop</th>
                        <td class="confirmation__description">{{ $restaurant->name }}</td>
                    </tr>
                    <tr class="confirmation__row">
                        <th class="confirmation__header">Date</th>
                        <td class="confirmation__description" id="input_date"></td>
                    </tr>
                    <tr class="confirmation__row">
                        <th class="confirmation__header">Time</th>
                        <td class="confirmation__description" id="input_time"></td>
                    </tr>
                    <tr class="confirmation__row">
                        <th class="confirmation__header">Number</th>
                        <td class="confirmation__description" id="input_number"></td>
                    </tr>
                    <tr class="confirmation__row">
                        <th class="confirmation__header">Payment</th>
                        <td class="confirmation__description" id="input_payment"></td>
                    </tr>
                </table>
            </div>
            @if (!Auth::check())
            <p class="reservation__message">※予約にはログインが必要です。</p>
            @endif
            @if ($errors->any())
            <p class="reservation__error-message">・{{$errors->first()}}</p>
            @endif
            <button>予約する</button>
        </form>
    </div>
    <div class="review__content">
        @if (Auth::check())
        @if(Auth::user()->reviews->where('restaurant_id', '=', $restaurant->id)->first() == null)
        <a href="/pro/review?restaurant_id={{ $restaurant->id }}&access_time={{ $access_time }}&page_status={{ $page_status }}">口コミを投稿する</a>
        @endif
        @endif
        <h2>全ての口コミ情報</h2>
        @foreach($reviews as $review)
        <div class="restaurant__review">
            @if (Auth::check())
            @if(Auth::user()->role_id == 3)
            <div class="review__form-group">
                <form class="review__form" action="/pro/review/edit" method="get">
                    @csrf
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                    <input type="hidden" name="access_time" value="{{ $access_time }}">
                    <input type="hidden" name="page_status" value="{{ $page_status }}">
                    <button>口コミを編集</button>
                </form>
                <form class="review__form" action="/pro/review/delete" method="post">
                    @csrf
                    @method('delete')
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                    <input type="hidden" name="review_id" value="{{ $review->id }}">
                    <input type="hidden" name="access_time" value="{{ $access_time }}">
                    <input type="hidden" name="page_status" value="{{ $page_status }}">
                    <button>口コミを削除</button>
                </form>
            </div>
            @else
            @if($review->user_id == Auth::id())
            <div class="review__form-group">
                <form class="review__form" action="/pro/review/edit" method="get">
                    @csrf
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                    <input type="hidden" name="access_time" value="{{ $access_time }}">
                    <input type="hidden" name="page_status" value="{{ $page_status }}">
                    <button>口コミを編集</button>
                </form>
                <form class="review__form" action="/pro/review/delete" method="post">
                    @csrf
                    @method('delete')
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                    <input type="hidden" name="review_id" value="{{ $review->id }}">
                    <input type="hidden" name="access_time" value="{{ $access_time }}">
                    <input type="hidden" name="page_status" value="{{ $page_status }}">
                    <button>口コミを削除</button>
                </form>
            </div>
            @endif
            @endif
            @endif
            <div class="review__img">
                <img src=" {{ asset($review->image) }}" alt="">
            </div>
            <form class="restaurant__review-evaluation" onsubmit="return false">
                @csrf
                <input class="restaurant__review-input" id="star5" name="evaluation" type="radio" value="5" disabled="disabled" @if( $review->evaluation==5 ) checked @endif>
                <label class="restaurant__review-label" for="star5"><i class="fa-solid fa-star"></i></label>

                <input class="restaurant__review-input" id="star4" name="evaluation" type="radio" value="4" disabled="disabled" @if( $review->evaluation==4 ) checked @endif>
                <label class="restaurant__review-label" for="star4"><i class="fa-solid fa-star"></i></label>

                <input class="restaurant__review-input" id="star3" name="evaluation" type="radio" value="3" disabled="disabled" @if( $review->evaluation==3 ) checked @endif>
                <label class="restaurant__review-label" for="star3"><i class="fa-solid fa-star"></i></label>

                <input class="restaurant__review-input" id="star2" name="evaluation" type="radio" value="2" disabled="disabled" @if( $review->evaluation==2 ) checked @endif>
                <label class="restaurant__review-label" for="star2"><i class="fa-solid fa-star"></i></label>

                <input class="restaurant__review-input" id="star1" name="evaluation" type="radio" value="1" disabled="disabled" @if( $review->evaluation==1 ) checked @endif>
                <label class="restaurant__review-label" for="star1"><i class="fa-solid fa-star"></i></label>
            </form>
            <p>{!! nl2br( $review->comment ) !!}</p>
        </div>
        @endforeach
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('js/detail.js') }}"></script>
@endsection