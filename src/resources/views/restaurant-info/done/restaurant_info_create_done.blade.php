@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/restaurant-info/done/restaurant_info_create_done.css') }}">
@endsection

@section('content')
<div class="done__content">
    <div class="done__message">
        @if(!empty($message))
        <p>{{ $message }}</p>
        @endif
    </div>
</div>
@endsection