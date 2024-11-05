@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/restaurant-info/done/restaurant_info_edit_done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        @if(!empty($message))
        <p>{{ $message }}</p>
        @endif
    </div>
    <div class="done__link">
        @if($message == "店舗情報を作成してください")
        <a href="/restaurant/create">
            <button class="done__button">作成する</button>
        </a>
        @endif
        @if($message == "店舗情報を更新しました")
        <a href="/restaurant/edit">
            <button class="done__button">戻る</button>
        </a>
        @endif
    </div>
</div>
@endsection