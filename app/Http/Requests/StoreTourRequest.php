<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'duration_label' => 'required|string|max:50',
            'destinations' => 'required|string|max:2000',
            'itinerary' => 'nullable|string|max:5000',
            'price_per_pax' => 'nullable|numeric|min:0',
            'price_per_package' => 'nullable|numeric|min:0',
            'amenities' => 'required|array|min:1',
            'amenities.*' => 'string|max:50',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }
}
