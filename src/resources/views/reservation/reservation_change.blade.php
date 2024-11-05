@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/reservation/reservation_change.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="reservation-change__content">
    <!-- 飲食店情報の表示 -->
    <div class="restaurant__content">
        <a class="restaurant__back-link" href=" {{ $href }}"><button>&lt;</button></a>
        <h2>{{ $reservation->restaurant->name }}</h2>
        <div class="restaurant__img">
            <img src="{{ asset($reservation->restaurant->image) }}" alt="no image" />
        </div>
        <div class="restaurant__tag">
            <span class="tag">#{{ $reservation->restaurant->area->name }}</span>
            <span class="tag">#{{ $reservation->restaurant->genre->name }}</span>
        </div>
        <div class="restaurant__text">
            <p>{!! nl2br( $reservation->restaurant->detail ) !!}</p>
        </div>
    </div>
    <div class="reservation__content">
        <!-- 変更前の予約情報の表示 -->
        <div class="reservation__card">
            <h2>現在の予約</h2>
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
                <tr class="reservation__row">
                    <th class="reservation__header">Stripe</th>
                    <td class="reservation__description">{{ $reservation->payment->name }}</td>
                </tr>
            </table>
        </div>
        <i class="fa-solid fa-caret-down reservation-change__icon"></i>
        <!-- 変更後の予約情報の表示 -->
        <div class="reservation__card">
            <h2>予約の変更</h2>
            <!-- 予約変更フォーム -->
            <form class="reservation__form-change" action="/reservation/change" method="post">
                @csrf
                @method('patch')
                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                <input type="hidden" name="page_status" value="{{ $page_status }}">
                <!-- 予約一覧画面から遷移の場合 -->
                @if($page_status == "reservation_record")
                <input type="hidden" name="date" value="{{ $dt }}">
                @endif
                <table class="reservation__table">
                    <tr class="reservation__row">
                        <th class="reservation__header">Shop</th>
                        <td class="reservation__description">{{ $reservation->restaurant->name }}</td>
                    </tr>
                    <tr class="reservation__row">
                        <th class="reservation__header">Date</th>
                        <td class="reservation__description">
                            <input type="date" name="date" value="{{ $reservation->date }}" />
                        </td>
                    </tr>
                    <tr class="reservation__row">
                        <th class="reservation__header">Time</th>
                        <td class="reservation__description">
                            <select name="time">
                                @for($h = 0; $h < 24; $h++)
                                    @for($m=0; $m < 4; $m++)
                                    <option value="{{ sprintf('%02d', $h) }}:{{ sprintf('%02d', $m*15) }}" @if( sprintf('%02d', $h).":".sprintf('%02d', $m*15)==substr($reservation->time,0,5) ) selected @endif>{{ sprintf('%02d', $h) }}:{{ sprintf('%02d', $m*15) }}</option>
                                    @endfor
                                    @endfor
                            </select>
                        </td>
                    </tr>
                    <tr class="reservation__row">
                        <th class="reservation__header">Number</th>
                        <td class="reservation__description">
                            <select name="number">
                                @for($i = 1; $i < 100; $i++)
                                    <option value="{{ $i }}" @if($i==$reservation->number ) selected @endif>{{ $i }}人</option>
                                    @endfor
                            </select>
                        </td>
                    </tr>
                    <tr class="reservation__row">
                        <th class="reservation__header">Stripe</th>
                        <td class="reservation__description">
                            <select name="payment_id">
                                @foreach($payments as $payment)
                                <option value="{{ $payment->id }}" @if($payment->id == $reservation->payment_id ) selected @endif>{{ $payment->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>
                @if ($errors->any())
                <p class="reservation__error-message">・{{$errors->first()}}</p>
                @endif
                <button class="reservation-form__change-button">変更</button>
            </form>
        </div>
    </div>
</div>
@endsection