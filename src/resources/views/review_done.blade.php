@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/review_done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        <p>レビュー投稿ありがとうございます</p>
    </div>
    <div class="done__link">
        <a href="/review/?restaurant_id={{ $review_array['restaurant_id'] }}">
            <button class="done__button">戻る</button>
        </a>
    </div>
</div>
@endsection