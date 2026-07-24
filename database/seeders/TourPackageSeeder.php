<?php

namespace Database\Seeders;

use App\Models\TourPackage;
use Illuminate\Database\Seeder;

class TourPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Paket Wisata 1 Hari',
                'duration_label' => '1 Hari',
                'destinations' => 'Malioboro, Keraton Yogyakarta, Taman Sari, Pantai Parangtritis',
                'itinerary' => '07:00 - Penjemputan di hotel/penginapan. 08:00 - Kunjungan ke Keraton Yogyakarta. 10:00 - Taman Sari. 12:00 - Makan siang. 14:00 - Malioboro. 16:00 - Pantai Parangtritis. 18:00 - Kembali ke hotel.',
                'price_per_pax' => 250000,
                'price_per_package' => null,
                'amenities' => ['Driver', 'AC', 'BBM', 'Makan Siang', 'HTM'],
                'image' => 'images/packages/paket-1-hari.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Paket Wisata 2 Hari 1 Malam',
                'duration_label' => '2D1N',
                'destinations' => 'Candi Borobudur, Candi Prambanan, Malioboro, Merapi Lava Tour',
                'itinerary' => 'Hari 1: 07:00 - Penjemputan. 09:00 - Candi Borobudur. 12:00 - Makan siang. 14:00 - Candi Prambanan. 18:00 - Check-in hotel. Malam bebas di Malioboro. Hari 2: 08:00 - Sarapan. 09:00 - Merapi Lava Tour. 13:00 - Makan siang. 14:00 - Wisata alam sekitar. 18:00 - Kembali.',
                'price_per_pax' => 500000,
                'price_per_package' => null,
                'amenities' => ['Driver', 'AC', 'BBM', 'Makan Siang (2x)', 'HTM', 'Hotel 1 Malam'],
                'image' => 'images/packages/paket-2d1n.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Paket Tour Custom',
                'duration_label' => 'Custom',
                'destinations' => 'Sesuai permintaan Anda',
                'itinerary' => 'Rencanakan perjalanan impian Anda bersama kami. Kami siap membantu menyusun itinerary sesuai keinginan.',
                'price_per_pax' => null,
                'price_per_package' => null,
                'amenities' => ['Driver Profesional', 'AC', 'BBM', 'Flexible', 'Private Trip'],
                'image' => 'images/packages/paket-custom.jpg',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($packages as $data) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
            TourPackage::create($data);
        }
    }
}
