@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/card/card_update.css') }}">
@endsection

@section('content')
<div class="card__content">
    <div class="card__heading">
        <h2>Stripe</h2>
    </div>
    <form action="/card/update" class="card__form" id="form_payment" method="POST">
        @csrf
        <div class="form__group">
            <label>カード番号</label>
            <div id="cardNumber"></div>
        </div>

        <div class="form__group">
            <label>セキュリティコード</label>
            <div id="securityCode"></div>
        </div>

        <div class="form__group">
            <label>有効期限</label>
            <div id="expiration"></div>
        </div>

        <div class="button__group">
            <button type="submit" id="create_token" class="btn">更新</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe_public_key = "{{ config('stripe.stripe_public_key') }}";
</script>
<script src="{{ asset('js/card.js') }}"></script>
@endsection