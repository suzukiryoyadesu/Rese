@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="detail__content">
    <div class="restaurant__content">
        @if($page_status == "mypage")
        <a class="restaurant__back-link" href="/mypage"><button>&lt;</button></a>
        @else
        <a class="restaurant__back-link" href="/search/?access_time={{ $access_time }}"><button>&lt;</button></a>
        @endif
        <h2>{{ $restaurant->name }}</h2>
        <div class="restaurant__img">
            @switch($restaurant->genre_id)
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
        <div class="restaurant__tag">
            <span class="tag">#{{ $restaurant->area->name }}</span>
            <span class="tag">#{{ $restaurant->genre->name }}</span>
        </div>
        <div class="restaurant__text">
            <p>{{ $restaurant->detail }}</p>
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
                    <tr class="c">
                        <th class="confirmation__header">Time</th>
                        <td class="confirmation__description" id="input_time"></td>
                    </tr>
                    <tr class="confirmation__row">
                        <th class="confirmation__header">Number</th>
                        <td class="confirmation__description" id="input_number"></td>
                    </tr>
                </table>
            </div>
            @if (!Auth::check())
            <p class="reservation__message">※予約にはログインが必要です。</p>
            @endif
            <button>予約する</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/app.js') }}"></script>
@endsection