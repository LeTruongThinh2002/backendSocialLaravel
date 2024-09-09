<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePwdRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password',
        ];
    }

    public function messages(): array
    {
        return [
            'confirm_password.same' => 'New password and confirmation password do not match.',
        ];
    }
}
