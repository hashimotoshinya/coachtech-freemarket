<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpeg,png|max:2048',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'condition' => 'required|in:良好,目立った傷や汚れなし,やや傷や汚れあり,状態が悪い',
            'title' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ];
    }
    
    public function messages()
    {
        return [
            'image.required' => '画像ファイルを選択してください。',
            'image.image' => '有効な画像ファイルを選択してください。',
            'image.mimes' => '画像はjpeg、png形式のみ対応しています。',
            'image.max' => '画像ファイルのサイズは2MB以下にしてください。',

            'categories.required' => 'カテゴリを1つ以上選択してください。',
            'categories.array' => 'カテゴリの形式が不正です。',
            'categories.min' => 'カテゴリを1つ以上選択してください。',
            'categories.*.exists' => '選択されたカテゴリが無効です。',

            'condition.required' => '商品の状態を選択してください。',
            'condition.in' => '商品の状態が無効です。',

            'title.required' => '商品名は必須です。',
            'title.max' => '商品名は255文字以内で入力してください。',

            'brand.max' => 'ブランド名は255文字以内で入力してください。',

            'description.required' => '商品の説明は必須です。',
            'description.max' => '商品の説明は255文字以内で入力してください。',

            'price.required' => '販売価格を入力してください。',
            'price.numeric' => '販売価格は数値で入力してください。',
            'price.min' => '販売価格は0円以上で入力してください。',
        ];
    }
    
}
