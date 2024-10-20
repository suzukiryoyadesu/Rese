@extends('layouts.app')

@section('css')
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css">
<link rel="stylesheet" href="{{ asset('css/reservation_record.css') }}">
@endsection

@section('content')
<div class="reservation__content">
    <!-- 日付選択フォーム -->
    <form class="date__form" id="date__form" action="/reservation/record" method="get">
        @csrf
        <input class="date__form-input" type="text" id="datepicker" name="date" value="{{ $dt }}" onchange="this.form.submit()" readonly>
        <label class="date__form-label" for="datepicker"><i class="fa-regular fa-calendar-days"></i></label>
    </form>
    <!-- 予約情報の表示 -->
    <div class="reservation__day">
        @foreach($reservations as $reservation)
        <div class="reservation__card">
            <!-- 予約情報が今日以降ならば表示 -->
            @if(!$before)
            <!-- 予約削除フォーム -->
            <form class="reservation__form-delete" action="/reservation/delete" method="post">
                @csrf
                @method('delete')
                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                <input type="hidden" name="page_status" value="{{ $page_status }}">
                <input type="hidden" name="date" value="{{ $dt }}">
                <button class="reservation__delete-button"><i class="fa-regular fa-circle-xmark fa-xl" style="color: #ffffff;"></i></button>
            </form>
            @endif
            <i class="fa-regular fa-clock fa-xl" style="color: #ffffff;"></i>
            <h3>{{ $reservation->user->name }} 様</h3>
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
            </table>
            <!-- 予約情報が今日以降ならば表示 -->
            @if(!$before)
            <!-- 予約変更画面への遷移ボタン -->
            <a class="reservation-change__link" href="/reservation/change/?reservation_id={{ $reservation->id }}&page_status={{ $page_status }}&date={{ $dt }}">
                <button class="reservation-change__link-button">変更</button>
            </a>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
<script src="{{ asset('js/reservation_record.js') }}"></script>
@endsection