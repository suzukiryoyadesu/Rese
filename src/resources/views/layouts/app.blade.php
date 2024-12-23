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
                        @can('tfAuth')
                        <li class="hamburger__nav-item">
                            <form action="/logout" method="post">
                                @csrf
                                <button class="hamburger__nav-link">Logout</button>
                            </form>
                        </li>
                        <li class="hamburger__nav-item"><a href="/mypage" class="hamburger__nav-link">Mypage</a></li>
                        <li class="hamburger__nav-item"><a href="/card" class="hamburger__nav-link">Stripe</a></li>
                        @can('admin')
                        <li class="hamburger__nav-item"><a href="/representative/register" class="hamburger__nav-link">Registration(Rep)</a></li>
                        <li class="hamburger__nav-item"><a href="/notification" class="hamburger__nav-link">Notification</a></li>
                        <li class="hamburger__nav-item"><a href="/pro/csv" class="hamburger__nav-link">CSV(Restaurant)</a></li>
                        @endcan
                        @can('restaurant')
                        <li class="hamburger__nav-item"><a href="/restaurant/create" class="hamburger__nav-link">Create(Restaurant)</a></li>
                        <li class="hamburger__nav-item"><a href="/restaurant/edit" class="hamburger__nav-link">Edit(Restaurant)</a></li>
                        @endcan
                        @can('reservation')
                        <li class="hamburger__nav-item"><a href="/reservation/record" class="hamburger__nav-link">Reservation</a></li>
                        <li class="hamburger__nav-item"><a href="/reservation/qr" class="hamburger__nav-link">Reservation(QR)</a></li>
                        @endcan
                        @else
                        <li class="hamburger__nav-item"><a href="/two-factor-auth/wait" class="hamburger__nav-link">TwoFactorAuth</a></li>
                        @endcan
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
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('js')
</body>

</html>