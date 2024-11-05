<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class CardController extends Controller
{
    /**
     * カード情報確認ページの表示
     *
     * @return view card.blade
     */
    public function card()
    {
        \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

        // カード情報を入れる変数
        $default_card = null;

        // ユーザー情報の取得
        $user = Auth::user();

        // Stripe上に顧客として存在する場合
        if (!empty($user->stripe_id)) {
            $card = \Stripe\Customer::allSources(
                $user->stripe_id,
                [
                    'limit'  => 1,
                    'object' => 'card',
                ]
            )->data;

            // カード情報が存在する場合
            if (count($card) > 0) {
                $card = $card[0];
                $default_card = [
                    'number' => str_repeat('*', 8) . $card->last4,
                    'brand' => $card->brand,
                    'exp_month' => $card->exp_month,
                    'exp_year' => $card->exp_year,
                ];
            }
        }

        // カード情報確認画面の表示
        return view('card.card', compact('default_card'));
    }

    /**
     * カード情報登録ページの表示
     *
     * @return view card_create.blade
     */
    public function cardCreateView()
    {
        return view('card.card_create');
    }

    /**
     * カード情報登録
     *
     * @param Request $request リクエスト
     * @return view card_create_done.blade
     */
    public function cardCreate(Request $request)
    {
        // Stripeトークン、ユーザー情報の取得
        $token = $request->stripeToken;
        $user = Auth::user();

        // Stripeトークンが存在する場合
        if ($token) {

            // Stripe上に顧客として存在していない場合
            if (!$user->stripe_id) {
                \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));
                $result = false;

                // Stripe上に顧客情報を保存
                try {
                    $customer = \Stripe\Customer::create([
                        'card' => $token,
                        'name' => $user->name,
                        'description' => 'ユーザーID：' . $user->id
                    ]);
                } catch (\Stripe\Exception\CardException $e) {
                    $message = 'カード情報を登録できませんでした';
                    return view('card.done.card_create_done', compact('message'));
                }

                // Stripe上に顧客情報を保存できた場合
                if (isset($customer->id)) {
                    $user->stripe_id = $customer->id;
                    $user->update();
                    $message = 'カード情報を登録しました';
                    return view('card.done.card_create_done', compact('message'));
                }

                $message = 'カード情報を登録できませんでした';
                return view('card.done.card_create_done', compact('message'));
            }

            // Stripe上に顧客として存在している場合
            if ($user->stripe_id) {
                \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

                // 顧客情報、カード情報を取得
                $customer = \Stripe\Customer::retrieve($user->stripe_id);
                $card = \Stripe\Customer::allSources(
                    $user->stripe_id,
                    [
                        'limit'  => 1,
                        'object' => 'card',
                    ]
                )->data;

                // カード情報が存在している場合
                if (count($card) > 0) {
                    $message = 'カード情報を登録できませんでした';
                    return view('card.done.card_create_done', compact('message'));
                }

                try {
                    $stripe = new \Stripe\StripeClient(Config::get('stripe.stripe_secret_key'));
                    $card = $stripe->customers->createSource($user->stripe_id, ['source' => $token]);

                    if (isset($customer)) {
                        $customer->default_source = $card["id"];
                        $customer->save();
                    }
                } catch (\Stripe\Exception\CardException $e) {
                    $message = 'カード情報を登録できませんでした';
                    return view('card.done.card_create_done', compact('message'));
                }
                $message = 'カード情報を登録しました';
                return view('card.done.card_create_done', compact('message'));
            }
        }

        $message = 'カード情報を登録できませんでした';
        return view('card.done.card_create_done', compact('message'));
    }

    /**
     * カード情報削除
     *
     * @return view card_delete_done.blade
     */
    public function cardDelete()
    {
        // ユーザー情報を取得
        $user = Auth::user();

        // 未支払の予約情報の取得
        $no_payment_reservation = $user->reservations()->where('payment_status', 1)->first();

        // 未支払の予約情報が存在しない場合
        if ($no_payment_reservation == null) {

            // Stripe上に顧客として存在しない場合
            if (empty($user->stripe_id)) {
                $message = 'カード情報が存在しません';
                return view('card.done.card_delete_done', compact('message'));
            }

            // Stripe上に顧客として存在する場合
            if (!empty($user->stripe_id)) {
                \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

                // 顧客情報、カード情報を取得
                $customer = \Stripe\Customer::retrieve($user->stripe_id);
                $card = \Stripe\Customer::allSources(
                    $user->stripe_id,
                    [
                        'limit'  => 1,
                        'object' => 'card',
                    ]
                )->data;

                // カード情報が存在している場合
                if (count($card) > 0) {
                    $card = $card[0];
                    \Stripe\Customer::deleteSource(
                        $user->stripe_id,
                        $card->id
                    );
                    $message = 'カード情報を削除しました';
                    return view('card.done.card_delete_done', compact('message'));
                }

                // カード情報が存在しない場合
                if (count($card) == 0) {
                    $message = 'カード情報が存在しません';
                    return view('card.done.card_delete_done', compact('message'));
                }
            }
        }

        // 未支払の予約情報が存在する場合
        if ($no_payment_reservation != null) {
            $message = '未決済の予約情報が存在するため、カード情報を削除できませんでした';
            return view('card.done.card_delete_done', compact('message'));
        }
    }

    /**
     * カード情報更新ページの表示
     *
     * @return view card_update.blade
     */
    public function cardUpdateView()
    {
        return view('card.card_update');
    }

    /**
     * カード情報更新
     *
     * @param Request $request リクエスト
     * @return view card_update_done.blade
     */
    public function cardUpdate(Request $request)
    {
        // Stripeトークン、ユーザー情報の取得
        $token = $request->stripeToken;
        $user = Auth::user();

        // Stripeトークンが存在する場合
        if ($token) {

            // Stripe上に顧客として存在していない場合
            if (!$user->stripe_id) {
                $message = 'カード情報を更新できませんでした';
                return view('card.done.card_update_done', compact('message'));
            }

            // Stripe上に顧客として存在している場合
            if ($user->stripe_id) {
                \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_secret_key'));

                // 顧客情報、カード情報を取得
                $customer = \Stripe\Customer::retrieve($user->stripe_id);
                $card = \Stripe\Customer::allSources(
                    $user->stripe_id,
                    [
                        'limit'  => 1,
                        'object' => 'card',
                    ]
                )->data;

                // カード情報が存在している場合
                if (count($card) > 0) {
                    $card = $card[0];
                    \Stripe\Customer::deleteSource(
                        $user->stripe_id,
                        $card->id
                    );
                }

                try {
                    $stripe = new \Stripe\StripeClient(Config::get('stripe.stripe_secret_key'));
                    $card = $stripe->customers->createSource($user->stripe_id, ['source' => $token]);

                    if (isset($customer)) {
                        $customer->default_source = $card["id"];
                        $customer->save();
                    }
                } catch (\Stripe\Exception\CardException $e) {
                    $message = 'カード情報を更新できませんでした';
                    return view('card.done.card_update_done', compact('message'));
                }

                $message = 'カード情報を更新しました';
                return view('card.done.card_update_done', compact('message'));
            }
        }

        $message = 'カード情報を更新できませんでした';
        return view('card.done.card_update_done', compact('message'));
    }
}
