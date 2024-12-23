@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pro/review_delete_done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        <p>{{ $message }}</p>
    </div>
    <div class="done__link">
        <a href="/detail/?restaurant_id={{ $restaurant_id }}&access_time={{ $access_time }}&page_status={{ $page_status }}">
            <button class="done__button">飲食店詳細へ</button>
        </a>
    </div>
</div>
@endsection