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
                    <div class="form__input--text">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Username" />
                    </div>
                    <div class="form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
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
                    <button class="form__button-submit" type="submit">登録</button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>