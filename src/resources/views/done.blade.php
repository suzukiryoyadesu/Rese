@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        <p>ご予約ありがとうございます</p>
    </div>
    <div class="done__link">
        <a href="/detail/?restaurant_id={{ $reservation_array['restaurant_id'] }}">
            <button class="done__button">戻る</button>
        </a>
    </div>
</div>
@endsection