<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorAuthMail;
use Carbon\Carbon;
use App\Models\User;

class AuthenticationController extends Controller
{
    /**
     * 2段階認証ページの表示
     *
     * @param Request $request リクエスト
     * @return view two_factor_auth.blade
     */
    public function twoFactorAuthView(Request $request)
    {
        // ユーザーID、トークンの取得
        $user_id = $request->user_id;
        $tfa_token = $request->tfa_token;

        // 2段階認証画面の表示
        return view('auth.two_factor_auth', compact('user_id', 'tfa_token'));
    }

    /**
     * 2段階認証
     *
     * @param Request $request リクエスト
     * @return view two_factor_auth_done.blade
     */
    public function twoFactorAuth(Request $request)
    {
        $messages = [
            '認証できませんでした',
        ];

        // ユーザーID,トークンが送信されてきた場合
        if ($request->filled('user_id', 'tfa_token')) {

            // ユーザー情報、現在の日時の取得
            $user = User::find($request->user_id);
            $tfa_expiration = new Carbon($user->tfa_expiration);
            $dt_now = Carbon::now();

            // トークン、有効期限のチェック
            if ($user->tfa_token === $request->tfa_token && $tfa_expiration > $dt_now) {
                $user->tfa_token = null;
                $user->tfa_expiration = null;
                $user->update();
                $messages = [
                    '認証が完了しました',
                    '元画面の完了ボタンを押下してください'
                ];
            }
        }

        // 2段階認証完了画面の表示
        return view('auth.done.two_factor_auth_done', compact('messages'));
    }

    /**
     * 2段階認証待機ページの表示
     *
     * @param Request $request リクエスト
     * @return view two_factor_auth_wait.blade
     */
    public function twoFactorAuthWaitView(Request $request)
    {
        //メッセージのセット
        $messages = [
            '送信されたURLより認証してください',
            '認証後、完了ボタンを押下してください',

        ];

        // 2段階認証待機画面の表示
        return view('auth.two_factor_auth_wait', compact('messages'));
    }

    /**
     * 2段階認証メールの再送信
     *
     * @param Request $request リクエスト
     * @return view two_factor_auth_wait.blade
     */
    public function twoFactorAuthMail()
    {
        // ユーザー情報の取得
        $user = Auth::user();

        //メッセージのセット
        $messages = [
            '認証済みです',
            '完了ボタンを押下してください',
        ];

        // 2段階認証前の場合
        if (!empty($user->tfa_token)) {
            //メッセージのセット
            $messages = [
                '送信されたURLより認証してください',
                '認証後、完了ボタンを押下してください',

            ];

            // 認証用のトークン、有効期限のセット
            $user->tfa_token = Str::random(32);
            $user->tfa_expiration = Carbon::now()->addMinutes(10)->format('Y/m/d  H:i:s');
            $user->update();

            // URLのセット及び、メールの送信
            $mail_data['url'] = request()->getSchemeAndHttpHost() . "/two-factor-auth?user_id=" . $user->id . "&tfa_token=" . $user->tfa_token;
            $mail = new TwoFactorAuthMail($mail_data);
            Mail::to($user->email)->send($mail);
        }

        // 2段階認証待機画面の表示
        return view('auth.two_factor_auth_wait', compact('messages'));
    }

    /**
     * 2段階認証後のリダイレクト処理
     *
     */
    public function twoFactorAuthNext()
    {
        return redirect()->intended('/');
    }

    /**
     * サンクスページの表示
     *
     * @return view thanks.blade
     */
    public function thanks()
    {
        return view('auth.done.thanks');
    }
}
