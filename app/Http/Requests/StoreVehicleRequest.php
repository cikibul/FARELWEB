<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'passenger_capacity' => 'required|integer|min:1|max:60',
            'transmission' => 'required|string|in:Manual,Matic',
            'price_half_day' => 'required|numeric|min:0',
            'price_full_day' => 'required|numeric|min:0|gte:price_half_day',
            'description' => 'nullable|string|max:5000',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'badge' => 'nullable|string|max:50',
            'inclusions' => 'required|array|min:1',
            'inclusions.*' => 'string|max:50',
            'is_available' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }
}
