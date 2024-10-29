{{ $reservation['user_name'] }} 様

ご予約の日時が近づいてまいりました。
ご来店お待ちしております。

【ご予約詳細】
Shop   : {{ $reservation['restaurant_name'] }}
Date   : {{ $reservation['date'] }}
Time   : {{ $reservation['time'] }}
Number : {{ $reservation['number'] }}人
Stripe : {{ $reservation['payment_name'] }}