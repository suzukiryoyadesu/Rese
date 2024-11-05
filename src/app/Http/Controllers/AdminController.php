<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\NotificationMail;
use App\Models\User;
use App\Models\Role;

class AdminController extends Controller
{
    /**
     * 店舗代表者登録ページの表示
     *
     * @return view representative_register.blade
     */
    public function representativeRegisterView()
    {
        return view('admin.representative_register');
    }

    /**
     * 店舗代表者登録
     *
     * @param Request $request リクエスト
     * @return view representative_register_done.blade
     */
    public function representativeRegister(Request $request)
    {
        // バリデーションルール、メッセージの作成
        $request->validate([
            'email' => [
                function ($attribute, $value, $fail) {
                    // ユーザー情報の取得
                    $user = User::where('email', '=', $value)->first();

                    // ユーザーが存在しない場合
                    if ($user === null) {
                        $fail('ユーザーが存在しません');
                        return;
                    }

                    // ユーザーが存在する場合
                    if($user != null) {
                        // 役割の取得
                        $role = Role::where('id', '=', $user->role_id)->first();

                        // 店舗代表者の場合
                        if ($role->name == '店舗代表者') {
                            $fail('既に店舗代表者です');
                            return;
                        }

                        // 利用者以外の場合
                        if ($role->name != '利用者') {
                            $fail($role->name . 'は店舗代表者になれません');
                            return;
                        }
                    }
                },
            ],
        ]);

        // 店舗代表者登録
        User::where('email', '=', $request->email)->first()->update(['role_id' => 2]);

        $message = '店舗代表者に登録しました';
        return view('admin.done.representative_register_done', compact('message'));
    }

    /**
     * メール送信ページの表示
     *
     * @return view notification.blade
     */
    public function notificationView()
    {
        return view('admin.notification');
    }

    /**
     * メール送信
     *
     * @param Request $request リクエスト
     * @return view notification_done.blade
     */
    public function notification(Request $request)
    {
        // バリデーションルール、メッセージの作成
        $rules = [
            'subject' => 'required|string',
            'message' => 'required|string'
        ];
        $messages = [
            'subject.required' => '件名を必ず入力してください',
            'subject.string' => '件名を文字列で入力してください',
            'message.required' => 'メッセージを入力してください',
            'message.string' => 'メッセージを文字列で入力してください',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        // バリデーションエラーの場合
        if ($validator->fails()) {
            return redirect('/notification')->withErrors($validator)->withInput();
        }

        // バリデーション済みデータの取得
        $mail_data = $validator->validated();

        // ユーザー情報の取得
        $users = User::all();

        // 宛先が存在しない場合
        if (count($users) == 0) {
            $message = '宛先が存在しません';
            return view('admin.done.notification_done', compact('message'));
        }

        // 送信に失敗したアドレスを入れる変数
        $error_emails = [];

        // メール送信処理
        foreach ($users as $user) {
            $mail = new NotificationMail($mail_data);
            Mail::to($user->email)->send($mail);

            // メール送信失敗の場合
            if (count(Mail::failures()) > 0) {
                $error_emails[] = $user->email;
            }
        }

        // メール送信が一つでも失敗の場合
        if (count($error_emails) > 0) {
            $message = '下記アドレスに送信できませんでした';
            return view('admin.done.notification_done', compact('message', 'error_emails'));
        }

        // メール送信がすべて成功の場合
        if (count($error_emails) == 0) {
            $message = 'メールを送信しました';
            return view('admin.done.notification_done', compact('message', 'error_emails'));
        }
    }
}
