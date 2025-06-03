<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseRequest extends FormRequest
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
            'payment_method' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $profile = Auth::user()->profile;

            if (!$profile || !$profile->postal_code || !$profile->address || !$profile->building) {
                $validator->errors()->add('profile', '未設定の方は変更ボタンへ');
            }
        });
    }
}
