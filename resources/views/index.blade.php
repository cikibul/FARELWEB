@extends('layouts.app')

@section('content')

<section id="home" class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0" style="background-image: url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=1920&q=80'); background-size: cover; background-position: center;"></div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-blue-900/60 via-transparent to-blue-900/30"></div>

    <div class="relative z-10 text-center px-4 sm:px-6 max-w-4xl mx-auto pt-20 pb-16">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
            Sewa Mobil & Paket Tour Jogja<br>
            <span class="text-yellow-400">Terpercaya</span>
        </h1>
        <p class="text-lg sm:text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Armada bersih & terawat, driver ramah & berpengalaman, harga all-in tanpa biaya tersembunyi. 
            Siap menemani perjalanan Anda di Yogyakarta.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="https://wa.me/{{ config('hiro.whatsapp_number') }}?text={{ urlencode('Halo, saya ingin menyewa mobil/paket tour. Mohon informasinya.') }}"
               target="_blank"
               class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold px-8 py-4 rounded-full text-lg shadow-lg hover:shadow-xl transition">
                <i class="fab fa-whatsapp text-xl"></i>
                Pesan Sekarang via WhatsApp
            </a>
            <a href="#armada"
               class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold px-8 py-4 rounded-full text-lg border-2 border-white/30 hover:border-white/50 transition backdrop-blur">
                <i class="fas fa-car"></i>
                Lihat Armada
            </a>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent"></div>
</section>

<section id="armada" class="py-16 sm:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Katalog Armada</h2>
            <p class="text-gray-600 max-w-xl mx-auto">Pilih kendaraan sesuai kebutuhan perjalanan Anda. Semua unit dalam kondisi prima dan siap pakai.</p>
        </div>

        @if($vehicles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($vehicles as $vehicle)
                    <x-vehicle-card :vehicle="$vehicle" />
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500">Belum ada armada tersedia.</p>
        @endif
    </div>
</section>

<section id="paket-tour" class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Paket Wisata</h2>
            <p class="text-gray-600 max-w-xl mx-auto">Nikmati liburan seru dengan paket tour lengkap kami. Harga bersahabat, pengalaman tak terlupakan.</p>
        </div>

        @if($tourPackages->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tourPackages as $package)
                    <x-package-card :package="$package" />
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500">Belum ada paket tour tersedia.</p>
        @endif
    </div>
</section>

<section id="keunggulan" class="py-16 sm:py-20 bg-blue-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Keunggulan Kami</h2>
            <p class="text-gray-600 max-w-xl mx-auto">Mengapa memilih kami untuk perjalanan Anda?</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <i class="fas fa-smile text-3xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Driver Ramah & Berpengalaman</h3>
                <p class="text-sm text-gray-600">Supir profesional, hafal rute wisata Jogja, dan siap membantu perjalanan Anda.</p>
            </div>

            <div class="text-center p-6">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <i class="fas fa-car text-3xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Kendaraan Bersih & Terawat</h3>
                <p class="text-sm text-gray-600">Setiap unit rutin diservis, dibersihkan, dan dalam kondisi prima.</p>
            </div>

            <div class="text-center p-6">
                <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <i class="fas fa-receipt text-3xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Harga Transparan / All-In</h3>
                <p class="text-sm text-gray-600">Harga sudah termasuk BBM, driver, dan parkir. Tanpa biaya tersembunyi.</p>
            </div>

            <div class="text-center p-6">
                <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <i class="fas fa-clock text-3xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Ketepatan Waktu</h3>
                <p class="text-sm text-gray-600">Kami selalu on-time untuk penjemputan, memastikan perjalanan Anda lancar.</p>
            </div>
        </div>
    </div>
</section>

<section id="galeri" class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Galeri</h2>
            <p class="text-gray-600 max-w-xl mx-auto">Momen perjalanan bersama pelanggan kami.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @php
                $galleryImages = ['gallery-1.jpg', 'gallery-2.jpg', 'gallery-3.jpg', 'gallery-4.jpg', 'gallery-5.jpg', 'gallery-6.jpg', 'gallery-7.jpg', 'gallery-8.jpg'];
            @endphp
            @foreach($galleryImages as $index => $img)
                <div class="relative group overflow-hidden rounded-xl aspect-[4/3] bg-gray-100">
                    <img src="{{ asset('images/gallery/' . $img) }}"
                         alt="Galeri {{ $index + 1 }}"
                         loading="lazy"
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                    <div class="hidden w-full h-full items-center justify-center text-gray-400 text-sm">
                        <i class="fas fa-image text-4xl"></i>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="testimoni" class="py-16 sm:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Testimoni Pelanggan</h2>
            <p class="text-gray-600 max-w-xl mx-auto">Apa kata mereka tentang layanan kami?</p>
        </div>

        @if($testimonials->count() > 0)
            <div x-data="{
                testimonials: {{ json_encode($testimonials->toArray()) }},
                current: 0,
                interval: null,
                start() {
                    this.interval = setInterval(() => {
                        this.current = (this.current + 1) % this.testimonials.length;
                    }, 4000);
                },
                stop() {
                    clearInterval(this.interval);
                }
            }"
            x-init="start()"
            @mouseenter="stop()"
            @mouseleave="start()"
            class="relative max-w-2xl mx-auto">

                <template x-for="(item, index) in testimonials" :key="index">
                    <div x-show="current === index"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 translate-x-8"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-8"
                         class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 text-center">
                        <div class="w-16 h-16 rounded-full mx-auto mb-4 bg-gray-200 flex items-center justify-center overflow-hidden">
                            <template x-if="item.photo">
                                <img :src="'{{ asset('') }}' + item.photo" :alt="item.customer_name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!item.photo">
                                <span class="text-2xl font-bold text-gray-400" x-text="item.customer_name.charAt(0)"></span>
                            </template>
                        </div>
                        <div class="flex justify-center gap-1 mb-3">
                            <template x-for="star in 5" :key="star">
                                <i :class="star <= item.rating ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-300'" class="text-sm"></i>
                            </template>
                        </div>
                        <p class="text-gray-600 italic mb-4" x-text="'\"' + item.content + '\"'"></p>
                        <h4 class="font-semibold text-gray-900" x-text="item.customer_name"></h4>
                    </div>
                </template>

                <div class="flex justify-center gap-2 mt-6">
                    <template x-for="(item, index) in testimonials" :key="'dot-' + index">
                        <button @click="current = index"
                                :class="current === index ? 'bg-blue-600 w-6' : 'bg-gray-300 w-2'"
                                class="h-2 rounded-full transition-all duration-300"></button>
                    </template>
                </div>
            </div>
        @else
            <p class="text-center text-gray-500">Belum ada testimoni.</p>
        @endif
    </div>
</section>

<section id="cta" class="py-16 sm:py-20 bg-gradient-to-r from-blue-600 to-blue-800">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Siap untuk Perjalanan Anda?</h2>
        <p class="text-blue-100 mb-8">Hubungi kami sekarang dan dapatkan penawaran terbaik untuk perjalanan Anda di Yogyakarta.</p>
        <a href="https://wa.me/{{ config('hiro.whatsapp_number') }}?text={{ urlencode('Halo, saya ingin menyewa mobil/paket tour. Mohon informasinya.') }}"
           target="_blank"
           class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold px-8 py-4 rounded-full text-lg shadow-lg transition">
            <i class="fab fa-whatsapp text-xl"></i>
            Konsultasi Gratis via WhatsApp
        </a>
    </div>
</section>

@endsection
