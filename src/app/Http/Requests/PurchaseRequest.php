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
            $sessionAddress = session('purchase_address');
            if ($sessionAddress) {
                $postalCode = $sessionAddress['postal_code'] ?? null;
                $address = $sessionAddress['address'] ?? null;
                $building = $sessionAddress['building'] ?? null;
            } else {
                $profile = Auth::user()->profile;
                $postalCode = $profile->postal_code ?? null;
                $address = $profile->address ?? null;
                $building = $profile->building ?? null;
            }

            if (!$postalCode || !$address || !$building) {
                $validator->errors()->add('profile', '配送先住所が未設定です。変更ボタンから設定してください。');
            }
        });
    }
}
