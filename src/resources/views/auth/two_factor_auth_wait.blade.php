@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/two_factor_auth_wait.css') }}">
@endsection

@section('content')
<div class="auth__content">
    <div class="auth__message">
        @foreach($messages as $message)
        <p>{{ $message }}</p>
        @endforeach
    </div>
    <div class="auth__form">
        <form class="form" name="form" action="/two-factor-auth/wait/mail" method="post">
            @csrf
            <div class="form__button">
                <button class="form__button-submit" type="submit">再送信</button>
            </div>
        </form>
        <form class="form" name="form" action="/two-factor-auth/next" method="get">
            @csrf
            <div class="form__button">
                <button class="form__button-submit" type="submit">完了</button>
            </div>
        </form>
    </div>
</div>
@endsection