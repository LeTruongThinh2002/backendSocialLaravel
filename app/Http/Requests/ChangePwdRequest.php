<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePwdRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'new_password' => ['required', 'string', 'min:8', 'max:255'],
            'confirm_password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }
}
