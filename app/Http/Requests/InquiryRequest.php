<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'wedding_date' => ['nullable', 'date', 'after:today'],
            'venue' => ['nullable', 'string', 'max:180'],
            'city' => ['nullable', 'string', 'max:120'],
            'budget' => ['nullable', 'integer', 'min:0'],
            'guest_count' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'services' => ['nullable', 'array'],
            'services.*' => ['string', 'max:80'],
            'package_id' => ['nullable', 'integer'],
            'message' => ['nullable', 'string', 'max:4000'],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }
}
