<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rese</title>
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
</head>

<body>
    <header>
        <div class="header__inner">
            <!-- ハンバーガーメニュー部分 -->
            <div class="hamburger">
                <!-- ハンバーガーメニューの表示・非表示を切り替えるチェックボックス -->
                <input class="hamburger__input" id="hamburger__input" type="checkbox">
                <!-- ハンバーガーアイコン -->
                <label for="hamburger__input" class="hamburger__open"><span></span></label>
                <!-- メニュー -->
                <nav class="hamburger__nav">
                    <ul class="hamburger__nav-list">
                        <li class="hamburger__nav-item"><a href="/" class="hamburger__nav-link">Home</a></li>
                        <li class="hamburger__nav-item">
                            <form action="/logout" method="post">
                                @csrf
                                <button class="hamburger__nav-link">Logout</button>
                            </form>
                        </li>
                        <li class="hamburger__nav-item"><a href="" class="hamburger__nav-link">Mypage</a></li>
                    </ul>
                </nav>
            </div>
            <h1>Rese</h1>
        </div>
    </header>

    <main>
        <div class="detail__content">
            <div class="restaurant__content">
                <a class="restaurant__back-link" href="/search"><button>&lt;</button></a>
                <h2>{{ $restaurant->name }}</h2>
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
                <div class="restaurant__tag">
                    <span class="tag">#{{ $restaurant->area->name }}</span>
                    <span class="tag">#{{ $restaurant->genre->name }}</span>
                </div>
                <div class="restaurant__text">
                    <p>{{ $restaurant->detail }}</p>
                </div>
            </div>
            <div class="reservation__content">
                <h2>予約</h2>
                <form class="reservation__form" action="/reservation" method="post">
                    @csrf
                    <div class="reservation__form-input">
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}" />
                        <input type="date" name="date" value="{{ \Carbon\Carbon::now()->format("Y-m-d") }}" />
                        <br />
                        <select name="time">
                            @for($h = 0; $h < 24; $h++)
                                @for($m=0; $m < 4; $m++)
                                <option value="{{ sprintf('%02d', $h) }}:{{ sprintf('%02d', $m*15) }}">{{ sprintf('%02d', $h) }}:{{ sprintf('%02d', $m*15) }}</option>
                                @endfor
                                @endfor
                        </select>
                        <br />
                        <select name="number">
                            @for($i = 1; $i < 100; $i++)
                                <option value="{{ $i }}">{{ $i }}人</option>
                                @endfor
                        </select>
                    </div>
                    <div>確認用</div>
                    <button>予約する</button>
                </form>
            </div>
        </div>
    </main>
</body>