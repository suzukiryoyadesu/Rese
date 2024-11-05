<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorAuthMail;
use Illuminate\Support\Str;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:191'],
            'email' => [
                'required',
                'string',
                'email',
                'max:191',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'role_id' => $input['role_id'],
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $user->tfa_token = Str::random(32);
        $user->tfa_expiration = Carbon::now()->addMinutes(10)->format('Y/m/d  H:i:s');
        $user->update();
        $mail_data['url'] = request()->getSchemeAndHttpHost() . "/two-factor-auth?user_id=" . $user->id . "&tfa_token=" . $user->tfa_token;
        $mail = new TwoFactorAuthMail($mail_data);
        Mail::to($user->email)->send($mail);

        return $user;
    }
}
