<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'description' => 'required|string|max:255',
            'media' => 'required|url', // Chấp nhận liên kết video
        ];
    }
}