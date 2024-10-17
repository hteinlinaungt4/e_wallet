<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferFormValidate extends FormRequest
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
            'to_phone' => 'required',
            'amount' => 'required|numeric|between:1000,1000000',
            'hash_value' => 'required',
        ];
    }
    public function messages(): array
    {
        return [
            'to_phone.required' => 'The recieve phone number field is required.',
            'amount.between' => 'The amount must be between 1000 (MMK) and 1000000 (MMK).',
        ];
    }
}
