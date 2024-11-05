<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rese</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/two_factor_auth.css') }}">
</head>

<body onload="this.form.submit()">
    <header>
        <div class="header__inner">
            <h1>Rese</h1>
        </div>
    </header>

    <main>
        <div class="auth__content">
            <div class="auth__message">
                <p>下記ボタンをクリックして、認証を完了してください</p>
            </div>
            <form class="form" name="form" action="/two-factor-auth" method="post">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user_id }}" />
                <input type="hidden" name="tfa_token" value="{{ $tfa_token }}" />
                <div class="form__button">
                    <button class="form__button-submit" type="submit">認証</button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>