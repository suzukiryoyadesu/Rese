@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
<script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="mypage__content">
    <h2>{{ Auth::user()->name }}さん</h2>
    <h3 class="reservation__ttl">予約状況</h3>
    <div class="reservation__content">
        @foreach($reservations as $reservation)
        <div class="reservation__card">
            <form class="reservation__form-delete" action="/reservation/delete" method="post">
                @csrf
                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                <button class="reservation__delete-button"><i class="fa-regular fa-circle-xmark fa-xl" style="color: #ffffff;"></i></button>
            </form>
            <i class="fa-regular fa-clock fa-xl" style="color: #ffffff;"></i>
            <h4>予約{{ $loop->iteration	}}</h4>
            <table class="reservation__table" id="reservation__table{{ $loop->iteration }}">
                <tr class="reservation__row">
                    <th class="reservation__header">Shop</th>
                    <td class="reservation__description">{{ $reservation->restaurant->name }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Date</th>
                    <td class="reservation__description">{{ $reservation->date }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Time</th>
                    <td class="reservation__description">{{ substr($reservation->time,0,5) }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Number</th>
                    <td class="reservation__description">{{ $reservation->number }}人</td>
                </tr>
            </table>
            <form class="reservation__form-change" id="reservation__form-change{{ $loop->iteration }}" action="/reservation/change" method="post">
                @csrf
                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                <table class="reservation__table">
                    <tr class="reservation__row">
                        <th class="reservation__header">Shop</th>
                        <td class="reservation__description">{{ $reservation->restaurant->name }}</td>
                    </tr>
                    <tr class="reservation__row">
                        <th class="reservation__header">Date</th>
                        <td class="reservation__description">
                            <input type="date" name="date" value="{{ $reservation->date }}" />
                        </td>
                    </tr>
                    <tr class="reservation__row">
                        <th class="reservation__header">Time</th>
                        <td class="reservation__description">
                            <select name="time">
                                @for($h = 0; $h < 24; $h++)
                                    @for($m=0; $m < 4; $m++)
                                    <option value="{{ sprintf('%02d', $h) }}:{{ sprintf('%02d', $m*15) }}" @if( sprintf('%02d', $h).":".sprintf('%02d', $m*15)==substr($reservation->time,0,5) ) selected @endif>{{ sprintf('%02d', $h) }}:{{ sprintf('%02d', $m*15) }}</option>
                                    @endfor
                                    @endfor
                            </select>
                        </td>
                    </tr>
                    <tr class="reservation__row">
                        <th class="reservation__header">Number</th>
                        <td class="reservation__description">
                            <select name="number">
                                @for($i = 1; $i < 100; $i++)
                                    <option value="{{ $i }}" @if($i==$reservation->number ) selected @endif>{{ $i }}人</option>
                                    @endfor
                            </select>
                        </td>
                    </tr>
                </table>
                <button>変更</button>
            </form>
            <button class="reservation__edit-button" id="reservation__edit-button{{ $loop->iteration }}">編集</button>
        </div>
        <script>
            function reservationEditButtonClick() {
                var reservationTable = document.getElementById('reservation__table{{ $loop->iteration }}');
                var reservationFormChange = document.getElementById('reservation__form-change{{ $loop->iteration }}');
                var reservationEditButtonClass = document.getElementsByClassName('reservation__edit-button');

                if (reservationTable.style.display == 'none') {
                    reservationTable.style.display = 'block';
                    reservationFormChange.style.display = 'none';
                    reservationFormChange.reset();
                    for (i = 0; i < reservationEditButtonClass.length; i++) {
                        if (i + 1 == '{{ $loop->iteration }}') {
                            reservationEditButtonClass[i].textContent = '編集';
                        }
                        reservationEditButtonClass[i].style.display = 'block';
                    }
                } else {
                    reservationTable.style.display = 'none';
                    reservationFormChange.style.display = 'block';
                    for (i = 0; i < reservationEditButtonClass.length; i++) {
                        if (i + 1 == '{{ $loop->iteration }}') {
                            reservationEditButtonClass[i].textContent = 'キャンセル';
                        } else {
                            reservationEditButtonClass[i].style.display = 'none';
                        }
                    }
                }
            }
            var reservationEditButton = document.getElementById('reservation__edit-button{{ $loop->iteration }}');
            reservationEditButton.addEventListener('click', reservationEditButtonClick);
        </script>
        @endforeach
    </div>
    <h3 class="reservation-history__ttl">過去の予約履歴</h3>
    <div class="reservation-history__content">
        @foreach($reservations_history as $reservation)
        <div class="reservation__card">
            <i class="fa-regular fa-clock fa-xl" style="color: #ffffff;"></i>
            <h4>予約{{ $loop->iteration	}}</h4>
            <table class="reservation__table" id="reservation__table{{ $loop->iteration }}">
                <tr class="reservation__row">
                    <th class="reservation__header">Shop</th>
                    <td class="reservation__description">{{ $reservation->restaurant->name }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Date</th>
                    <td class="reservation__description">{{ $reservation->date }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Time</th>
                    <td class="reservation__description">{{ substr($reservation->time,0,5) }}</td>
                </tr>
                <tr class="reservation__row">
                    <th class="reservation__header">Number</th>
                    <td class="reservation__description">{{ $reservation->number }}人</td>
                </tr>
            </table>
            <a class="review__link" href="/review/?restaurant_id={{ $reservation->restaurant->id }}">
                <button class="review__link-button">レビュー</button>
            </a>
        </div>
        @endforeach
    </div>
    <h3 class="favorite__ttl">お気に入り店舗</h3>
    <div class="favorite__content">
        @foreach($favorites as $favorite)
        <div class="restaurant__card">
            <div class="restaurant__img">
                @switch($favorite->restaurant->genre_id)
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
                <h4>{{ $favorite->restaurant->name }}</h4>
                <div class="restaurant__tag">
                    <span class="tag">#{{ $favorite->restaurant->area->name }}</span>
                    <span class="tag">#{{ $favorite->restaurant->genre->name }}</span>
                </div>
                <div class="restaurant__option">
                    <a class="restaurant__option-link" href="/detail/?restaurant_id={{ $favorite->restaurant_id }}&page_status={{ $page_status }}">
                        <button>詳しくみる</button>
                    </a>
                    <form class=" favorite__form" action="/favorite/delete" method="post">
                        @csrf
                        <input type="hidden" name="restaurant_id" value="{{ $favorite->restaurant_id }}" />
                        <input type="hidden" name="page_status" value="{{ $page_status }}" />
                        <button class="favorite__form-button"><i class="fa-solid fa-heart fa-2x" style="color: #eb3223;"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection