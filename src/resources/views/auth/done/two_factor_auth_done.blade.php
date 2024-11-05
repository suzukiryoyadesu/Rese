<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rese</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/done/two_factor_auth_done.css') }}">
</head>

<body>
    <header>
        <div class="header__inner">
            <h1>Rese</h1>
        </div>
    </header>

    <main>
        <div class="auth__content">
            <div class="auth__message">
                @foreach($messages as $message)
                <p>{{ $message }}</p>
                @endforeach
            </div>
        </div>
    </main>
</body>

</html>