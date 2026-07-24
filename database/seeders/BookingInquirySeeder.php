<?php

namespace Database\Seeders;

use App\Models\BookingInquiry;
use Illuminate\Database\Seeder;

class BookingInquirySeeder extends Seeder
{
    public function run(): void
    {
        BookingInquiry::factory()->count(10)->create();
    }
}
