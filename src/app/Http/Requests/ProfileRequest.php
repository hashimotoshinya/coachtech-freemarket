<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'postal_code' => 'nullable|regex:/^\d{3}-\d{4}$/',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'お名前を入力してください',
            'postal_code.required' => '郵便番号は入力必須です。',
            'postal_code.regex' => '郵便番号はハイフンありの8文字（例：123-4567）で入力してください。',
            'address.required' => '住所は入力必須です。',
            'building.required' => '建物名は入力必須です。',
            'image.image' => '有効な画像ファイルを選択してください。',
            'image.mimes' => '画像はjpeg、png形式のみ対応しています。',
            'image.max' => '画像ファイルのサイズは2MB以下にしてください。',
        ];
    }
}
