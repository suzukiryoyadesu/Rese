<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rese</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <script src="https://kit.fontawesome.com/0e19cc0cb9.js" crossorigin="anonymous"></script>



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
                        <li class="hamburger__nav-item"><a href="/register" class="hamburger__nav-link">Registration</a></li>
                        <li class="hamburger__nav-item"><a href="/login" class="hamburger__nav-link">Login</a></li>
                    </ul>
                </nav>
            </div>
            <h1>Rese</h1>
        </div>
    </header>

    <main>
        <div class="register__content">
            <div class="register-form__heading">
                <h2>Registration</h2>
            </div>
            <form class="form" action="/register" method="post">
                @csrf
                <div class="form__group">
                    <div class="form__input">
                        <i class="fa-solid fa-user fa-xl" style="color: #4b4b4b;"></i>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Username" />
                    </div>
                    <div class="form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group">
                    <div class="form__input">
                        <i class="fa-solid fa-envelope fa-xl" style="color: #4b4b4b;"></i>
                        <input type="text" name="email" value="{{ old('email') }}" placeholder="Email" />
                    </div>
                    <div class="form__error">
                        @error('email')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group">
                    <div class="form__input">
                        <i class="fa-solid fa-lock fa-xl" style="color: #4b4b4b;"></i>
                        <input type="password" name="password" placeholder="Password" />
                    </div>
                    <div class="form__error">
                        @error('password')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__button">
                    <button class="form__button-submit" type="submit">登録</button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>