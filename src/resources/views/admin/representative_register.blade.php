@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/representative_register.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="register__content">
    <div class="register-form__heading">
        <h2>Registration(Representative)</h2>
    </div>
    <form class="form" action="/representative/register" method="post">
        @csrf
        <div class="form__group">
            <div class="form__input">
                <i class="fa-solid fa-envelope fa-xl" style="color: #4b4b4b;"></i>
                <input type="text" name="email" value="{{ old('email') }}" placeholder="Email" />
            </div>
            <div class="form__error">
                @error('email')
                {{ $message }}
                @enderror
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">登録</button>
        </div>
    </form>
</div>
@endsection