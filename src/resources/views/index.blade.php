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
            <img src="{{ asset($restaurant->image) }}" alt="no image" />
        </div>
        <div class="restaurant__text">
            <h2>{{ $restaurant->name }}</h2>
            @if($restaurant->review_total == 0)
            <p class="restaurant__review-item">レビューなし</p>
            @else
            <div class="restaurant__review">
                <form class="restaurant__review-evaluation" onsubmit="return false">
                    <input class="restaurant__review-input" id="star5" name="evaluation" type="radio" value="5" disabled="disabled" @if( $restaurant->review_average>=4.5&&$restaurant->review_average<=5 ) checked @endif>
                        <label class="restaurant__review-label" for="star5"><i class="fa-solid fa-star"></i></label>

                        <input class="restaurant__review-input" id="star4" name="evaluation" type="radio" value="4" disabled="disabled" @if( $restaurant->review_average>=3.5&&$restaurant->review_average < 4.5 ) checked @endif>
                            <label class="restaurant__review-label" for="star4"><i class="fa-solid fa-star"></i></label>

                            <input class="restaurant__review-input" id="star3" name="evaluation" type="radio" value="3" disabled="disabled" @if( $restaurant->review_average>=2.5&&$restaurant->review_average<3.5 ) checked @endif>
                                <label class="restaurant__review-label" for="star3"><i class="fa-solid fa-star"></i></label>

                                <input class="restaurant__review-input" id="star2" name="evaluation" type="radio" value="2" disabled="disabled" @if( $restaurant->review_average>=1.5&&$restaurant->review_average<2.5 ) checked @endif>
                                    <label class="restaurant__review-label" for="star2"><i class="fa-solid fa-star"></i></label>

                                    <input class="restaurant__review-input" id="star1" name="evaluation" type="radio" value="1" disabled="disabled" @if( $restaurant->review_average>=1&&$restaurant->review_average<1.5 ) checked @endif>
                                        <label class="restaurant__review-label" for="star1"><i class="fa-solid fa-star"></i></label>
                </form>
                <span class="restaurant__review-item">{{ $restaurant->review_average }}</span>
                <span class="restaurant__review-item">&lpar;{{ $restaurant->review_total }}&rpar;</span>
            </div>
            @endif
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
                <form class="favorite__form" id="favorite__form{{ $loop->iteration }}" action="/favorite/add" method="post">
                    @csrf
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}" />
                    <input type="hidden" name="access_time" value="{{ $search_condition['access_time'] }}" />
                    <input type="hidden" name="position" value="0" id="favorite-form__input-position{{ $loop->iteration }}">
                    <button class="favorite__form-button" id="favorite__form-button{{ $loop->iteration }}"><i class="fa-solid fa-heart fa-2x" style="color: #eeeeee;"></i></button>
                </form>
                @else
                <form class="favorite__form" id="favorite__form{{ $loop->iteration }}" action="/favorite/delete" method="post">
                    @csrf
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}" />
                    <input type="hidden" name="access_time" value="{{ $search_condition['access_time'] }}" />
                    <input type="hidden" name="position" value="0" id="favorite-form__input-position{{ $loop->iteration }}">
                    <button class="favorite__form-button" id="favorite__form-button{{ $loop->iteration }}"><i class="fa-solid fa-heart fa-2x" style="color: #eb3223;"></i></button>
                </form>
                @endif
                @endif
            </div>
        </div>
    </div>
    <script>
        function favoriteFormButtonClick() {
            var favoriteFormInputPosition = document.getElementById('favorite-form__input-position{{ $loop->iteration }}');
            var favoriteForm = document.getElementById('favorite__form{{ $loop->iteration }}');
            favoriteFormInputPosition.value = window.scrollY;
            favoriteForm.submit();
        }
        var favoriteFormButton = document.getElementById('favorite__form-button{{ $loop->iteration }}');
        favoriteFormButton.addEventListener('click', favoriteFormButtonClick);
    </script>
    @endforeach
</div>
@endsection

@section('js')
<script>
    window.addEventListener('load', function() {
        window.scroll(0, '{{ $position }}');
    })
</script>
@endsection