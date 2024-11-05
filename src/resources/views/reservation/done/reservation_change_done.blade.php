@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/reservation/done/reservation_change_done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        <p>{{ $message }}</p>
    </div>
    <div class="done__link">
        <a href="{{ $href }}">
            <!-- 予約一覧画面から遷移の場合 -->
            @if($message == "カード情報を登録してください")
            <button class="done__button">登録する</button>
            @endif
            @if($message != "カード情報を登録してください")
            @if($page_status == "reservation_record")
            <button class="done__button">予約一覧へ</button>
            @endif
            <!-- マイページから遷移の場合 -->
            @if($page_status == "mypage")
            <button class="done__button">マイページへ</button>
            @endif
            @endif
        </a>
    </div>
</div>
@endsection