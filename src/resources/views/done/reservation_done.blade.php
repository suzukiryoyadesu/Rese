@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/done/reservation_done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        @if(!empty($message))
        <p>{{ $message }}</p>
        @endif
    </div>
    <div class="done__link">
        @if($message != "カード情報を登録してください")
        <a href="/detail/?restaurant_id={{ $reservation_array['restaurant_id'] }}&access_time={{ $access_time }}&page_status={{ $page_status }}">
            <button class="done__button">戻る</button>
        </a>
        @endif
        @if($message == "カード情報を登録してください")
        <a href="/card/create">
            <button class="done__button">登録する</button>
        </a>
        @endif
    </div>
</div>
@endsection