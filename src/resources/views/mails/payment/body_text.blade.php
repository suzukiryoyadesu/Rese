{{ $reservation['user_name'] }} 様

ご来店ありがとうございました。
決済金額は{{ number_format($reservation['amount']) }}円です。

【ご予約詳細】
Shop   : {{ $reservation['restaurant_name'] }}
Date   : {{ $reservation['date'] }}
Time   : {{ $reservation['time'] }}
Number : {{ $reservation['number'] }}人
Stripe : {{ $reservation['payment_name'] }}