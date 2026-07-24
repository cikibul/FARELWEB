<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'booking_type' => 'required|in:vehicle,tour',
            'item_id' => 'required|integer',
            'booking_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
