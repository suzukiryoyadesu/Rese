@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="mypage__content">
    <h2>{{ Auth::user()->name }}さん</h2>
    <h3 class="reservation__ttl">予約状況</h3>
    <!-- 予約情報の表示 -->
    <div class="reservation__content">
        @foreach($reservations as $reservation)
        <div class="reservation__card">
            <div class="reservation__card-ttl">
                <div class="card__ttl-left">
                    <i class="fa-regular fa-clock fa-xl" style="color: #ffffff;"></i>
                    <h4>予約{{ $loop->iteration	}}</h4>
                </div>
                <!-- 予約削除フォーム -->
                <div class="card__ttl-right">
                    <form class="reservation__form-delete" action="/reservation/delete" method="post">
                        @csrf
                        @method('delete')
                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                        <input type="hidden" name="page_status" value="{{ $page_status }}">
                        <button class="reservation__delete-button"><i class="fa-regular fa-circle-xmark fa-xl" style="color: #ffffff;"></i></button>
                    </form>
                </div>
            </div>
            <table class="reservation__table" id="reservation__table{{ $loop->iteration }}">
                <tr class="reservation__row">
                    <th class="reservation__header">Shop</th>
                    <td class="reservation__description">{{ $reservation->restaurant->name }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Date</th>
                    <td class="reservation__description">{{ $reservation->date }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Time</th>
                    <td class="reservation__description">{{ substr($reservation->time,0,5) }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Number</th>
                    <td class="reservation__description">{{ $reservation->number }}人</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Stripe</th>
                    <td class="reservation__description">{{ $reservation->payment->name }}</td>
                </tr>
            </table>
            <!-- 予約変更画面への遷移ボタン -->
            <div class="reservation__link">
                <a href="/reservation/change/?reservation_id={{ $reservation->id }}&page_status={{ $page_status }}">
                    <button class="reservation__link-button">変更</button>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    <h3 class="reservation-history__ttl">過去の予約履歴</h3>
    <!-- 予約履歴の表示 -->
    <div class="reservation-history__content">
        @foreach($reservations_history as $reservation)
        <div class="reservation__card">
            <div class="reservation-history__card-ttl">
                <i class="fa-regular fa-clock fa-xl" style="color: #ffffff;"></i>
                <h4>予約{{ $loop->iteration	}}</h4>
            </div>
            <table class="reservation__table" id="reservation__table{{ $loop->iteration }}">
                <tr class="reservation__row">
                    <th class="reservation__header">Shop</th>
                    <td class="reservation__description">{{ $reservation->restaurant->name }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Date</th>
                    <td class="reservation__description">{{ $reservation->date }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Time</th>
                    <td class="reservation__description">{{ substr($reservation->time,0,5) }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Number</th>
                    <td class="reservation__description">{{ $reservation->number }}人</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Stripe</th>
                    <td class="reservation__description">{{ $reservation->payment->name }}</td>
                </tr>
            </table>
            <div class="reservation__link">
                <a href="/review/?restaurant_id={{ $reservation->restaurant->id }}">
                    <button class="reservation__link-button">レビュー</button>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    <h3 class="favorite__ttl">お気に入り店舗</h3>
    <!-- お気に入り飲食店の表示 -->
    <div class="favorite__content">
        @foreach($favorites as $favorite)
        <div class="restaurant__card">
            <div class="restaurant__img">
                <img src="{{ asset($favorite->restaurant->image) }}" alt="no image" />
            </div>
            <div class="restaurant__text">
                <h4>{{ $favorite->restaurant->name }}</h4>
                @if($favorite->review_total == 0)
                <p class="restaurant_review-item">レビューなし</p>
                @else
                <div class="restaurant_review">
                    <div class="restaurant_review-evaluation">
                        <input class="restaurant_review-input" id="star5" name="evaluation" type="radio" value="5" disabled="disabled" @if( $favorite->review_average>=4.5&&$favorite->review_average<=5 ) checked @endif>
                            <label class="restaurant_review-label" for="star5"><i class="fa-solid fa-star"></i></label>

                            <input class="restaurant_review-input" id="star4" name="evaluation" type="radio" value="4" disabled="disabled" @if( $favorite->review_average>=3.5&&$favorite->review_average < 4.5 ) checked @endif>
                                <label class="restaurant_review-label" for="star4"><i class="fa-solid fa-star"></i></label>

                                <input class="restaurant_review-input" id="star3" name="evaluation" type="radio" value="3" disabled="disabled" @if( $favorite->review_average>=2.5&&$favorite->review_average<3.5 ) checked @endif>
                                    <label class="restaurant_review-label" for="star3"><i class="fa-solid fa-star"></i></label>

                                    <input class="restaurant_review-input" id="star2" name="evaluation" type="radio" value="2" disabled="disabled" @if( $favorite->review_average>=1.5&&$favorite->review_average<2.5 ) checked @endif>
                                        <label class="restaurant_review-label" for="star2"><i class="fa-solid fa-star"></i></label>

                                        <input class="restaurant_review-input" id="star1" name="evaluation" type="radio" value="1" disabled="disabled" @if( $favorite->review_average>=1&&$favorite->review_average<1.5 ) checked @endif>
                                            <label class="restaurant_review-label" for="star1"><i class="fa-solid fa-star"></i></label>
                    </div>
                    <span class="restaurant_review-item">{{ $favorite->review_average }}</span>
                    <span class="restaurant_review-item">&lpar;{{ $favorite->review_total }}&rpar;</span>
                </div>
                @endif
                <div class="restaurant__tag">
                    <span class="tag">#{{ $favorite->restaurant->area->name }}</span>
                    <span class="tag">#{{ $favorite->restaurant->genre->name }}</span>
                </div>
                <div class="restaurant__option">
                    <a class="restaurant__option-link" href="/detail/?restaurant_id={{ $favorite->restaurant_id }}&page_status={{ $page_status }}">
                        <button>詳しくみる</button>
                    </a>
                    <form class=" favorite__form" action="/favorite/delete" method="post">
                        @csrf
                        <input type="hidden" name="restaurant_id" value="{{ $favorite->restaurant_id }}" />
                        <input type="hidden" name="page_status" value="{{ $page_status }}" />
                        <button class="favorite__form-button"><i class="fa-solid fa-heart fa-2x" style="color: #eb3223;"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection