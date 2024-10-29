@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/card.css') }}">
@endsection

@section('content')
<div class="card__content">
    <div class="card__heading">
        <h2>Stripe</h2>
    </div>
    <!-- カード情報が存在した場合 -->
    @if($default_card !=null)
    <div class="card">
        <h3>現在登録されているクレジットカード</h3>
        <div class="card__information">
            <table class="card__table">
                <tr class="card__row">
                    <th class="card__header">カード番号</th>
                    <td class="card__description">{{$default_card["number"]}}</td>
                </tr>
                <tr class="card__row">
                    <th class="card__header">カード有効期限</th>
                    <td class="card__description">{{$default_card["exp_month"]}}/{{$default_card["exp_year"]}}</td>
                </tr>
                <tr class="card__row">
                    <th class="card__header">カードブランド</th>
                    <td class="card__description">{{$default_card["brand"]}}</td>
                </tr>
            </table>
        </div>
        <div class="button__group">
            <form action="/card/delete" method="POST">
                @csrf
                <button class="card__button">削除</button>
            </form>
            <form action="/card/update" method="GET">
                @csrf
                <button class="card__button">更新</button>
            </form>
        </div>
    </div>
    @endif
    <!-- カード情報が存在しない場合 -->
    @if($default_card == null)
    <div class="card__message">
        <p>現在登録されているクレジットカードがありません</p>
    </div>
    <div class="card__link">
        <a href="/card/create">
            <button class="card__button">登録する</button>
        </a>
    </div>
    @endif
</div>
@endsection