@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="register__content">
    <div class="register-form__heading">
        <h2>Registration</h2>
    </div>
    <form class="form" action="/register" method="post">
        @csrf
        <input type="hidden" name="role_id" value="1">
        <div class="form__group">
            <div class="form__input">
                <i class="fa-solid fa-user fa-xl" style="color: #4b4b4b;"></i>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Username" />
            </div>
            <div class="form__error">
                @error('name')
                {{ $message }}
                @enderror
            </div>
        </div>
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
        <div class="form__group">
            <div class="form__input">
                <i class="fa-solid fa-lock fa-xl" style="color: #4b4b4b;"></i>
                <input type="password" name="password" placeholder="Password" />
            </div>
            <div class="form__error">
                @error('password')
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