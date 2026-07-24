<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'customer_name' => 'Budi Santoso',
                'rating' => 5,
                'content' => 'Pelayanan sangat memuaskan! Mobil bersih, driver ramah dan tahu tempat-tempat wisata. Recommended banget buat liburan ke Jogja.',
                'is_approved' => true,
                'sort_order' => 1,
            ],
            [
                'customer_name' => 'Siti Rahmawati',
                'rating' => 5,
                'content' => 'Paket tour 2D1N-nya luar biasa. Semua teratur, hotel nyaman, makanannya enak. Driver-nya juga sangat helpful. Makasih banyak!',
                'is_approved' => true,
                'sort_order' => 2,
            ],
            [
                'customer_name' => 'Andi Pratama',
                'rating' => 5,
                'content' => 'Harga all-in bikin tenang, tidak ada biaya tambahan di jalan. Mobil Innova Zenix-nya baru dan sangat nyaman. Next trip pasti pakai lagi.',
                'is_approved' => true,
                'sort_order' => 3,
            ],
            [
                'customer_name' => 'Dewi Lestari',
                'rating' => 5,
                'content' => 'Driver tepat waktu, sopan, dan mengemudi dengan hati-hati. Cocok untuk keluarga yang bawa anak kecil. Pelayanan 5 bintang!',
                'is_approved' => true,
                'sort_order' => 4,
            ],
            [
                'customer_name' => 'Rudi Haryanto',
                'rating' => 5,
                'content' => 'Sewa Alphard untuk acara pernikahan anak saya. Mobil mewah, driver berseragam rapi, semua tamu terkesan. Terima kasih Farel Transport!',
                'is_approved' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
