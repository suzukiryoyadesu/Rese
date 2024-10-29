@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/notification.css') }}">
@endsection

@section('content')
<div class="notification__content">
    <div class="notification-form__heading">
        <h2>Notification</h2>
    </div>
    <!-- メール送信フォーム -->
    <form class="form" action="/notification" method="post">
        @csrf
        <table class="form__table">
            <!-- 件名入力欄 -->
            <tr class="form__row">
                <th class="form__header">subject</th>
                <td class="form__description">
                    <input type="text" name="subject" value="{{ old('subject') }}">
                </td>
            </tr>
            <tr class="form__error">
                <th class="form__header"></th>
                <td class="form__description">
                    @error('subject')
                    {{ $message }}
                    @enderror
                </td>
            </tr>
            <!-- 本文入力欄 -->
            <tr class="form__row">
                <th class="form__header">message</th>
                <td class="form__description">
                    <textarea name="message">{{ old('message') }}</textarea>
                </td>
            </tr>
            <tr class="form__error">
                <th class="form__header"></th>
                <td class="form__description">
                    @error('message')
                    {{ $message }}
                    @enderror
                </td>
            </tr>
        </table>
        <div class="form__button">
            <button class="form__button-submit" type="submit">送信</button>
        </div>
    </form>
</div>
@endsection