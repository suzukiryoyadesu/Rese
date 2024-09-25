<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rese</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
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
                        @if (Auth::check())
                        <li class="hamburger__nav-item">
                            <form action="/logout" method="post">
                                @csrf
                                <button class="hamburger__nav-link">Logout</button>
                            </form>
                        </li>
                        <li class="hamburger__nav-item"><a href="" class="hamburger__nav-link">Mypage</a></li>
                        @else
                        <li class="hamburger__nav-item"><a href="/register" class="hamburger__nav-link">Registration</a></li>
                        <li class="hamburger__nav-item"><a href="/login" class="hamburger__nav-link">Login</a></li>
                        @endif
                    </ul>
                </nav>
            </div>
            <h1>Rese</h1>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
    @yield('js')
</body>

</html>