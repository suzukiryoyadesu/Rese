@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/reservation_confirm.css') }}">
@endsection

@section('content')
<div class="confirm__content">
    <div class="confirm__heading">
        <h2>Confirm</h2>
    </div>
    <!-- 予約情報が存在した場合 -->
    @if($reservation !=null)
    <div class="reservation__card">
        <h3>{{ $reservation->user->name }} 様</h3>
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
                <th class="reservation__header">Payment</th>
                <td class="reservation__description">{{ $reservation->payment->name }}</td>
            </tr>
        </table>
    </div>
    @endif
    <!-- 予約情報が存在しない場合 -->
    @if($reservation ==null)
    <div class="confirm__message">
        <p>予約情報がありません</p>
    </div>
    @endif
    <div class="confirm__link">
        <a href="/reservation/qr">
            <button class="confirm__button">戻る</button>
        </a>
    </div>
</div>
@endsection