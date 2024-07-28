<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ];
    }
    public function messages()
    {
        return [
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
