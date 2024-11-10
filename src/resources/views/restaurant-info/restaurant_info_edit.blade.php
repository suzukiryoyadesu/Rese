@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/restaurant-info/restaurant_info_edit.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="detail__content">
    <div class="restaurant__content">
        <h2 id="input_name">{{ $restaurant->name }}</h2>
        <div class="restaurant__img">
            <img src="{{ asset($restaurant->image) }}" id="input_image" alt="" />
        </div>
        <div class="restaurant__tag">
            <span class="tag" id="input_area">#{{ $restaurant->area->name }}</span>
            <span class="tag" id="input_genre">#{{ $restaurant->genre->name }}</span>
        </div>
        <div class="restaurant__text">
            <p id="input_detail">{!! nl2br( $restaurant->detail ) !!}</p>
        </div>
    </div>
    <div class="edit__content">
        <h2>店舗情報の更新</h2>
        <form class="edit__form" id="form" action="/restaurant/edit" method="post" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
            <table class="edit__table">
                <tr class="edit__row">
                    <th class="edit__header">name</th>
                    <td class="edit__description">
                        <input type="text" name="name" value="{{ $restaurant->name }}">
                    </td>
                </tr>
                <tr class="edit__row">
                    <th class="edit__header">image</th>
                    <td class="edit__description">
                        <input type="file" name="image">
                    </td>
                </tr>
                <tr class="edit__row">
                    <th class="edit__header">area</th>
                    <td class="edit__description">
                        <select name="area_id">
                            <option hidden>選択してください</option>
                            @foreach($areas as $area)
                            <option value="{{ $area->id }}" @if( $restaurant->area_id==$area->id ) selected @endif>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr class="edit__row">
                    <th class="edit__header">genre</th>
                    <td class="edit__description">
                        <select name="genre_id">
                            <option hidden>選択してください</option>
                            @foreach($genres as $genre)
                            <option value="{{ $genre->id }}" @if( $restaurant->genre_id==$genre->id ) selected @endif>{{ $genre->name }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr class="edit__row">
                    <th class="edit__header">detail</th>
                    <td class="edit__description">
                        <textarea name="detail">{{ $restaurant->detail }}</textarea>
                    </td>
                </tr>
            </table>
            @if ($errors->any())
            <p class="edit__error-message">{{$errors->first()}}</p>
            @endif
            <button>更新する</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/restaurant.js') }}"></script>
@endsection