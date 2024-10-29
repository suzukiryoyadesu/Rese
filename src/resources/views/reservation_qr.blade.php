@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/reservation_qr.css') }}">
@endsection

@section('content')
<div class="qr__content">
    <div class="qr__heading">
        <h2>Reservation(QR)</h2>
    </div>
    <div class="qr__message">
        <p id="loading">カメラ起動中...</p>
    </div>
    <button id="restartBtn" class="qr__button" hidden>再読み込み</button>
    <video id='video' hidden></video>
    <canvas id="canvas" hidden></canvas>
    <div id="output" hidden>
        <a id="outputData" href="">
            <button class="qr__button">予約情報の表示</button>
        </a>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/app/jsQR.js') }}"></script>
<script src="{{ asset('js/reservation_qr.js') }}"></script>
@endsection