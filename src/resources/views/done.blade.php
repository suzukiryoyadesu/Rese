@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        @if(!empty($message))
        <p>{{ $message }}</p>
        @endif
    </div>
    <div class="done__link">
        @if($message == "ご予約ありがとうございます")
        <a href="/detail/?restaurant_id={{ $reservation_array['restaurant_id'] }}&access_time={{ $access_time }}&page_status={{ $page_status }}">
            <button class="done__button">戻る</button>
        </a>
        @elseif($message == "店舗代表者に登録しました")
        <a href="/representative/register">
            <button class="done__button">戻る</button>
        </a>
        @elseif($message == "店舗情報を作成してください")
        <a href="/restaurant/create">
            <button class="done__button">作成する</button>
        </a>
        @elseif($message == "店舗情報を更新しました")
        <a href="/restaurant/edit">
            <button class="done__button">戻る</button>
        </a>
        @endif
    </div>
</div>
@endsection