@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/done/representative_register_done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        @if(!empty($message))
        <p>{{ $message }}</p>
        @endif
    </div>
    <div class="done__link">
        <a href="/representative/register">
            <button class="done__button">戻る</button>
        </a>
    </div>
</div>
@endsection