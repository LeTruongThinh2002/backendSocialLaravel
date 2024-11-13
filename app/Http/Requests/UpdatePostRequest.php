<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => [
                'required',
                'json', // Kiểm tra xem có phải là JSON hợp lệ
                function ($attribute, $value, $fail) {
                    $decodedValue = json_decode($value, true);
                    if (!is_array($decodedValue)) {
                        return $fail("The {$attribute} field must be a valid JSON object.");
                    }

                    if (!isset($decodedValue['html']) || !is_string($decodedValue['html'])) {
                        return $fail("The {$attribute}.html field must be a string.");
                    }

                    if (!isset($decodedValue['json']) || !is_array($decodedValue['json'])) {
                        return $fail("The {$attribute}.json field must be an array.");
                    }
                }
            ],
        ];
    }
}
