@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<form class="restaurant__search-form" action="/search" method="get">
    @csrf
    <div class="search-form__item">
        <input type="hidden" name="status" value="search" />
        <input type="hidden" name="access_time" value="{{ $search_condition['access_time'] }}" />
        <select class="search-form__select-left" name="area_id" onchange="this.form.submit()">
            <option value="">All area</option>
            @foreach($areas as $area)
            <option value="{{ $area->id }}" @if( $search_condition['search_area_id']==$area->id ) selected @endif>{{ $area->name }}</option>
            @endforeach
        </select>
        <select class="search-form__select-middle" name="genre_id" onchange="this.form.submit()">
            <option value="">All genre</option>
            @foreach($genres as $genre)
            <option value="{{ $genre->id }}" @if( $search_condition['search_genre_id']==$genre->id ) selected @endif>{{ $genre->name }}</option>
            @endforeach
        </select>
        <i class="fa-solid fa-magnifying-glass fa-lg" style="color: #eeeeee;"></i>
        <input class="search-form__input-right" type="text" name="keyword" value="{{ $search_condition['search_keyword'] }}" onchange="this.form.submit()" placeholder="Search ..." />
    </div>
</form>
<div class="restaurant__content">
    @foreach($restaurants as $restaurant)
    <div class="restaurant__card">
        <div class="restaurant__img">
            @switch($restaurant->genre_id)
            @case(1)
            <img src="https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/sushi.jpg" />
            @break
            @case(2)
            <img src="https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/yakiniku.jpg" />
            @break
            @case(3)
            <img src="https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/izakaya.jpg" />
            @break
            @case(4)
            <img src="https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/italian.jpg" />
            @break
            @case(5)
            <img src="https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/ramen.jpg" />
            @break
            @default
            <img alt="no image" />
            @endswitch
        </div>
        <div class="restaurant__text">
            <h2>{{ $restaurant->name }}</h2>
            <div class="restaurant__tag">
                <span class="tag">#{{ $restaurant->area->name }}</span>
                <span class="tag">#{{ $restaurant->genre->name }}</span>
            </div>
            <div class="restaurant__option">
                <a class="restaurant__option-link" href="/detail/?restaurant_id={{ $restaurant->id }}&access_time={{ $search_condition['access_time'] }}">
                    <button>詳しくみる</button>
                </a>
                @if (Auth::check())
                @if(!Auth::user()->isFavorite($restaurant->id))
                <form class="favorite__form" action="/favorite/add" method="post">
                    @csrf
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}" />
                    <input type="hidden" name="access_time" value="{{ $search_condition['access_time'] }}" />
                    <button class="favorite__form-button"><i class="fa-solid fa-heart fa-2x" style="color: #eeeeee;"></i></button>
                </form>
                @else
                <form class="favorite__form" action="/favorite/delete" method="post">
                    @csrf
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}" />
                    <input type="hidden" name="access_time" value="{{ $search_condition['access_time'] }}" />
                    <button class="favorite__form-button"><i class="fa-solid fa-heart fa-2x" style="color: #eb3223;"></i></button>
                </form>
                @endif
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection