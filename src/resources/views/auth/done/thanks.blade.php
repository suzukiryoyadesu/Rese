@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/done/thanks.css') }}">
@endsection

@section('content')
<div class="thanks__content">
    <div class="thanks__message">
        <p>会員登録ありがとうございます</p>
    </div>
    <div class="thanks__link">
        <a href="/thanks/login">
            <button class="thanks__button">ログインする</button>
        </a>
    </div>
</div>
@endsection