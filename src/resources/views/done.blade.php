<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rese</title>
    <link rel="stylesheet" href="{{ asset('css/done.css') }}">
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
        <div class="done__content">
            <div class="done__message">
                <p>ご予約ありがとうございます</p>
            </div>
            <div class="done__link">
                <a href="/detail">
                    <button class="done__button">戻る</button>
                </a>
            </div>
        </div>
    </main>
</body>

</html>