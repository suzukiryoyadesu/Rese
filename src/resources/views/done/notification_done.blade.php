@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/done/notification_done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        @if(!empty($message))
        <p>{{ $message }}</p>
        @endif
        <ul class="done_error-list">
            @foreach($error_emails as $error_email)
            <li>{{ $error_email }}</li>
            @endforeach
        </ul>
    </div>
    <div class="done__link">
        <a href="/notification">
            <button class="done__button">戻る</button>
        </a>
    </div>
</div>
@endsection