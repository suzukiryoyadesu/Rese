@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/payment/done/payment_done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        @if(!empty($message))
        <p>{{ $message }}</p>
        @endif
    </div>
    <div class="done__link">
        @if($message != "決済できませんでした")
        <a href="/reservation/record?date={{ $dt }}">
            <button class="done__button">予約一覧へ</button>
        </a>
        @endif
        @if($message == "決済できませんでした")
        <a href="/reservation/payment/?reservation_id={{ $reservation->id }}&date={{ $dt }}">
            <button class="done__button">戻る</button>
        </a>
        @endif
    </div>
</div>
@endsection