@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/restaurant_create.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="detail__content">
    <div class="restaurant__content">
        <h2 id="input_name"></h2>
        <div class="restaurant__img">
            <img src="" id="input_image" alt="" />
        </div>
        <div class="restaurant__tag">
            <span class="tag" id="input_area"></span>
            <span class="tag" id="input_genre"></span>
        </div>
        <div class="restaurant__text">
            <p id="input_detail"></p>
        </div>
    </div>
    <div class="create__content">
        <h2>店舗情報の作成</h2>
        <form class="create__form" id="form" action="" method="post" enctype="multipart/form-data">
            @csrf
            <table class="create__table">
                <tr class="create__row">
                    <th class="create__header">name</th>
                    <td class="create__description">
                        <input type="text" name="name">
                    </td>
                </tr>
                <tr class="create__row">
                    <th class="create__header">image</th>
                    <td class="create__description">
                        <input type="file" name="image">
                    </td>
                </tr>
                <tr class="create__row">
                    <th class="create__header">area</th>
                    <td class="create__description">
                        <select name="area_id">
                            <option hidden>選択してください</option>
                            @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr class="create__row">
                    <th class="create__header">genre</th>
                    <td class="create__description">
                        <select name="genre_id">
                            <option hidden>選択してください</option>
                            @foreach($genres as $genre)
                            <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr class="create__row">
                    <th class="create__header">detail</th>
                    <td class="create__description">
                        <textarea name="detail"></textarea>
                    </td>
                </tr>
            </table>
            @if ($errors->any())
            <p class="create__error-message">・{{$errors->first()}}</p>
            @endif
            <button>作成する</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/restaurant.js') }}"></script>
@endsection