<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'number' => 'required|integer|min:1|max:99'
        ];
    }

    public function messages()
    {
        return [
            'date.required' => '日付を必ず入力してください',
            'date.date' => '日付を入力してください',
            'date.after_or_equal' => '今日以降の日付を入力してください',
            'time.required' => '時間を必ず入力してください',
            'number.required' => '人数を必ず入力してください',
            'number.integer' => '人数を整数で入力してください',
            'number.min' => '人数を1人以上で入力してください',
            'number.max' => '人数を99人以下で入力してください',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('date') == Carbon::now()->format("Y-m-d") && $this->input('time') <= Carbon::now()->format("H:i")) {
                $validator->errors()->add('time', '現在の時刻以降の時間を入力してください');
            }
        });
    }
}
