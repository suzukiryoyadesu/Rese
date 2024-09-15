<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rese</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <header>
        <div class="header__inner">
            <h1>Rese</h1>
        </div>
    </header>

    <main>
        <div class="login__content">
            <div class="login-form__heading">
                <h2>Login</h2>
            </div>
            <form class="form" action="/login" method="post">
                @csrf
                <div class="form__group">
                    <div class="form__input--text">
                        <input type="text" name="email" value="{{ old('email') }}" placeholder="Email" />
                    </div>
                    <div class="form__error">
                        @error('email')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group">
                    <div class="form__input--text">
                        <input type="password" name="password" placeholder="Password" />
                    </div>
                    <div class="form__error">
                        @error('password')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__button">
                    <button class="form__button-submit" type="submit">ログイン</button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>