@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/done/card_update_done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        @if(!empty($message))
        <p>{{ $message }}</p>
        @endif
    </div>
    <div class="done__link">
        <a href="/card">
            <button class="done__button">戻る</button>
        </a>
    </div>
</div>
@endsection