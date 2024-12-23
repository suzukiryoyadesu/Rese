<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReviewRequest extends FormRequest
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
            'evaluation' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:400',
            'image' => 'nullable|file|mimes:jpeg,png',
        ];
    }

    public function messages()
    {
        return [
            'evaluation.required' => '評価を必ず入力してください',
            'evaluation.integer' => '評価を整数で入力してください',
            'evaluation.min' => '評価を1以上で入力してください',
            'evaluation.max' => '評価を5以下で入力してください',
            'comment.required' => 'コメントを必ず入力してください',
            'comment.string' => 'コメントを文字列で入力してください',
            'comment.max' => 'コメントを400文字以下で入力してください',
            'image.file' => '画像をファイルで入力してください',
            'image.mimes' => '画像(.jpeg/.png)を入力してください',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (empty(trim(mb_convert_kana($this->input('comment'), "s")))) {
                $validator->errors()->add('comment', 'コメントを必ず入力してください');
            }
        });
    }
}
