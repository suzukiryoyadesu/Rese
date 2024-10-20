@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/no_permission.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        <p>権限がありません</p>
    </div>
    <div class="done__link">
        <a href="/">
            <button class="done__button">ホームへ</button>
        </a>
    </div>
</div>
@endsection