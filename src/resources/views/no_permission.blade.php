@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/no_permission.css') }}">
@endsection

@section('content')
<div class="no-permission__content">
    <div class="no-permission__message">
        <p>権限がありません</p>
    </div>
    <div class="no-permission__link">
        <a href="/">
            <button class="no-permission__button">ホームへ</button>
        </a>
    </div>
</div>
@endsection