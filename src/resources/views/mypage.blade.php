@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="mypage__content">
    <h2>{{ Auth::user()->name }}さん</h2>
    <h3 class="reservation__ttl">予約状況</h3>
    <div class="reservation__content">
        @foreach($reservations as $reservation)
        <div class="reservation__card">
            <form class="reservation__form" action="/reservation/delete" method="post">
                @csrf
                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                <button class="reservation__form-button"><i class="fa-regular fa-circle-xmark fa-xl" style="color: #ffffff;"></i></button>
            </form>
            <i class="fa-regular fa-clock fa-xl" style="color: #ffffff;"></i>
            <h4>予約{{ $loop->iteration	 }}</h4>
            <table class="reservation__table">
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
            </table>
        </div>
        @endforeach
    </div>
    <h3 class="favorite__ttl">お気に入り店舗</h3>
    <div class="favorite__content">
        @foreach($favorites as $favorite)
        <div class="restaurant__card">
            <div class="restaurant__img">
                @switch($favorite->restaurant->genre_id)
                @case(1)
                <img src="https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/sushi.jpg" />
                @break
                @case(2)
                <img src="https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/yakiniku.jpg" />
                @break
                @case(3)
                <img src="https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/izakaya.jpg" />
                @break
                @case(4)
                <img src="https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/italian.jpg" />
                @break
                @case(5)
                <img src="https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/ramen.jpg" />
                @break
                @default
                <img alt="no image" />
                @endswitch
            </div>
            <div class="restaurant__text">
                <h4>{{ $favorite->restaurant->name }}</h4>
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