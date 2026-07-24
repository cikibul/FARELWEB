<?php

namespace Database\Factories;

use App\Models\BookingInquiry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingInquiryFactory extends Factory
{
    protected $model = BookingInquiry::class;

    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'phone_number' => fake()->phoneNumber(),
            'booking_type' => fake()->randomElement(['vehicle', 'tour']),
            'item_id' => fake()->numberBetween(1, 6),
            'booking_date' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'notes' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
        ];
    }
}
