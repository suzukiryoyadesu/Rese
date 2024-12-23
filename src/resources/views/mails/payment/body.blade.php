<p>{{ $reservation['user_name'] }} 様</p>
<br />
<p>ご来店ありがとうございました。</p>
<p>決済金額は{{ number_format($reservation['amount']) }}円です。</p>
<br />
<p>【ご予約詳細】</p>
<table style="text-align: left;">
    <tr>
        <th style="font-weight: normal;">Shop</th>
        <td>: {{ $reservation['restaurant_name'] }}</td>
    </tr>
    <tr>
        <th style="font-weight: normal;">Date</th>
        <td>: {{ $reservation['date'] }}</td>
    </tr>
    <tr>
        <th style="font-weight: normal;">Time</th>
        <td>: {{ substr($reservation['time'],0,5) }}</td>
    </tr>
    <tr>
        <th style="font-weight: normal;">Number</th>
        <td>: {{ $reservation['number'] }}人</td>
    </tr>
    <tr>
        <th style="font-weight: normal;">Stripe</th>
        <td>: {{ $reservation['payment_name'] }}</td>
    </tr>
</table>