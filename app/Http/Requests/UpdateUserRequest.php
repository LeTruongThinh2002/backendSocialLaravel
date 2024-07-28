<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'date_of_birth' => ['required', 'date_format:Y-m-d'],
            'country' => ['required', 'string', 'max:255'],
        ];
    }
}
