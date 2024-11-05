@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/payment/payment.css') }}">
@endsection

@section('content')
<div class="payment__content">
    <div class="payment-form__heading">
        <h2>Payment</h2>
    </div>
    <div class="reservation__information">
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
    <!-- 決済フォーム -->
    <form class="form" action="/reservation/payment" method="post">
        @csrf
        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
        <input type="hidden" name="date" value="{{ $dt }}">
        <table class="form__table">
            <!-- 金額入力欄 -->
            <tr class="form__row">
                <th class="form__header">amount</th>
                <td class="form__description">
                    <input type="text" name="amount" value="{{ old('amount') }}">
                </td>
            </tr>
        </table>
        <p class="form__error">
            @error('amount')
            {{ $message }}
            @enderror
        </p>
        <div class="form__button">
            <button class="form__button-submit" type="submit">決済</button>
        </div>
    </form>
</div>
@endsection