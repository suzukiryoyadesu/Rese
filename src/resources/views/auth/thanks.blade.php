@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/thanks.css') }}">
@endsection

@section('content')
<div class="thanks__content">
    <div class="thanks__message">
        <p>会員登録ありがとうございます</p>
    </div>
    <div class="thanks__link">
        <a href="/">
            <button class="thanks__button">ログインする</button>
        </a>
    </div>
</div>
@endsection