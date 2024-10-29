<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantRequest extends FormRequest
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
        if ($this->isMethod('patch')) {
            return [
                'area_id' => 'required|integer',
                'genre_id' => 'required|integer',
                'name' => 'required|string|max:50',
                'image' => 'file|mimes:jpg,jpeg',
                'detail' => 'required|string|max:300',
            ];
        }else{
            return [
                'area_id' => 'required|integer',
                'genre_id' => 'required|integer',
                'name' => 'required|string|max:50',
                'image' => 'required|file|mimes:jpg,jpeg',
                'detail' => 'required|string|max:300',
            ];
        }
    }

    public function messages()
    {
        return [
            'area_id.required' => 'エリアを必ず入力してください',
            'area_id.integer' => 'エリアを選択してください',
            'genre_id.required' => 'ジャンルを必ず入力してください',
            'genre_id.integer' => 'ジャンルを選択してください',
            'name.required' => '店名を必ず入力してください',
            'name.string' => '店名を文字列で入力してください',
            'name.max' => '店名を50文字以下で入力してください',
            'image.required' => '画像を必ず入力してください',
            'image.file' => '画像をファイルで入力してください',
            'image.mimes' => '画像(.jpg/.jpeg)を入力してください',
            'detail.required' => '詳細を必ず入力してください',
            'detail.string' => '詳細を文字列で入力してください',
            'detail.max' => '詳細を300文字以下で入力してください',
        ];
    }
}
