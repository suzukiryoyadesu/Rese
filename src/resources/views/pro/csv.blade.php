@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pro/csv.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="register__content">
    <div class="register-form__heading">
        <h2>CSV(Restaurant)</h2>
    </div>
    <form class="form" action="/pro/csv/import" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form__group">
            <div class="form__input">
                <input type="file" name="csv" id="csv" />
            </div>
            <div class="form__error">
                @if (count($errors) > 0)
                @foreach ($errors->all() as $error)
                {{$error}}<br />
                @endforeach
                @endif
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">インポート</button>
        </div>
    </form>
</div>
@endsection