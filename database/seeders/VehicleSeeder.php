<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'name' => 'Toyota Avanza',
                'category' => 'MPV',
                'passenger_capacity' => 6,
                'transmission' => 'Manual',
                'price_half_day' => 300000,
                'price_full_day' => 500000,
                'description' => 'Toyota Avanza dengan kabin nyaman, cocok untuk perjalanan keluarga kecil di perkotaan maupun wisata.',
                'image' => 'images/vehicles/avanza.jpg',
                'badge' => 'Terpopuler',
                'inclusions' => ['Driver', 'AC', 'BBM'],
                'is_available' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Toyota Innova Zenix',
                'category' => 'MPV',
                'passenger_capacity' => 7,
                'transmission' => 'Matic',
                'price_half_day' => 500000,
                'price_full_day' => 800000,
                'description' => 'Innova Zenix terbaru dengan kenyamanan maksimal, cocok untuk perjalanan bisnis atau keluarga.',
                'image' => 'images/vehicles/innova-zenix.jpg',
                'badge' => 'Best Value',
                'inclusions' => ['Driver', 'AC', 'BBM'],
                'is_available' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Toyota HiAce Commuter',
                'category' => 'Micro Bus',
                'passenger_capacity' => 12,
                'transmission' => 'Manual',
                'price_half_day' => 600000,
                'price_full_day' => 1000000,
                'description' => 'HiAce Commuter dengan kapasitas 12 penumpang, cocok untuk rombongan keluarga atau rekan kerja.',
                'image' => 'images/vehicles/hiace-commuter.jpg',
                'badge' => null,
                'inclusions' => ['Driver', 'AC', 'BBM'],
                'is_available' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Toyota HiAce Premio',
                'category' => 'Micro Bus',
                'passenger_capacity' => 7,
                'transmission' => 'Matic',
                'price_half_day' => 750000,
                'price_full_day' => 1200000,
                'description' => 'HiAce Premio dengan interior premium, reclining seat, dan kenyamanan ekstra untuk perjalanan jauh.',
                'image' => 'images/vehicles/hiace-premio.jpg',
                'badge' => null,
                'inclusions' => ['Driver', 'AC', 'BBM', 'Snack'],
                'is_available' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Toyota Alphard',
                'category' => 'Premium',
                'passenger_capacity' => 6,
                'transmission' => 'Matic',
                'price_half_day' => 1500000,
                'price_full_day' => 2500000,
                'description' => 'Alphard mewah dengan segala fasilitas premium. Pilihan tepat untuk perjalanan eksekutif dan wisata keluarga.',
                'image' => 'images/vehicles/alphard.jpg',
                'badge' => 'Premium',
                'inclusions' => ['Driver', 'AC', 'BBM', 'Snack', 'Air Mineral'],
                'is_available' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Bus Pariwisata',
                'category' => 'Bus',
                'passenger_capacity' => 35,
                'transmission' => 'Manual',
                'price_half_day' => 2000000,
                'price_full_day' => 3500000,
                'description' => 'Bus pariwisata dengan kapasitas besar, cocok untuk study tour, outing, atau perjalanan rombongan besar.',
                'image' => 'images/vehicles/bus.jpg',
                'badge' => null,
                'inclusions' => ['Driver', 'Co-Driver', 'AC', 'BBM'],
                'is_available' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($vehicles as $data) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
            Vehicle::create($data);
        }
    }
}
