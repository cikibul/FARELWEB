<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ config('hiro.company_name') }} - Sewa Mobil & Paket Tour Jogja Terpercaya. Armada lengkap, harga all-in, driver berpengalaman.">
    <title>{{ config('hiro.company_name') }} | Sewa Mobil & Paket Tour Jogja</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased text-gray-800 bg-white">

    <nav x-data="{ mobileOpen: false }" class="fixed top-0 left-0 right-0 z-40 bg-white/95 backdrop-blur shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="#home" class="text-xl font-bold text-blue-600">
                    <i class="fas fa-car-side mr-2"></i>{{ config('hiro.company_name') }}
                </a>

                <div class="hidden lg:flex items-center space-x-6">
                    <a href="#home" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Beranda</a>
                    <a href="#armada" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Armada</a>
                    <a href="#paket-tour" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Paket Tour</a>
                    <a href="#keunggulan" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Keunggulan</a>
                    <a href="#galeri" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Galeri</a>
                    <a href="#testimoni" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Testimoni</a>
                    <a href="#kontak" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Kontak</a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="https://wa.me/{{ config('hiro.whatsapp_number') }}?text={{ urlencode(config('hiro.whatsapp_message')) }}"
                       target="_blank"
                       class="hidden sm:inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition">
                        <i class="fab fa-whatsapp"></i>
                        <span>Hubungi Kami</span>
                    </a>

                    <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileOpen" @click.outside="mobileOpen = false" class="lg:hidden border-t bg-white">
            <div class="px-4 py-3 space-y-2">
                <a href="#home" @click="mobileOpen = false" class="block py-2 text-sm font-medium text-gray-700 hover:text-blue-600">Beranda</a>
                <a href="#armada" @click="mobileOpen = false" class="block py-2 text-sm font-medium text-gray-700 hover:text-blue-600">Armada</a>
                <a href="#paket-tour" @click="mobileOpen = false" class="block py-2 text-sm font-medium text-gray-700 hover:text-blue-600">Paket Tour</a>
                <a href="#keunggulan" @click="mobileOpen = false" class="block py-2 text-sm font-medium text-gray-700 hover:text-blue-600">Keunggulan</a>
                <a href="#galeri" @click="mobileOpen = false" class="block py-2 text-sm font-medium text-gray-700 hover:text-blue-600">Galeri</a>
                <a href="#testimoni" @click="mobileOpen = false" class="block py-2 text-sm font-medium text-gray-700 hover:text-blue-600">Testimoni</a>
                <a href="#kontak" @click="mobileOpen = false" class="block py-2 text-sm font-medium text-gray-700 hover:text-blue-600">Kontak</a>
                <a href="https://wa.me/{{ config('hiro.whatsapp_number') }}?text={{ urlencode(config('hiro.whatsapp_message')) }}"
                   target="_blank"
                   class="flex items-center gap-2 text-green-600 font-semibold py-2">
                    <i class="fab fa-whatsapp text-lg"></i>
                    <span>Hubungi Kami via WA</span>
                </a>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer id="kontak" class="bg-gray-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                <div>
                    <h4 class="text-white font-bold text-lg mb-4">
                        <i class="fas fa-car-side text-blue-400 mr-2"></i>{{ config('hiro.company_name') }}
                    </h4>
                    <p class="text-sm leading-relaxed">
                        Penyedia layanan sewa mobil dan paket tour di Yogyakarta. Armada terawat, driver profesional, harga all-in.
                    </p>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm">
                        <li><i class="fas fa-map-marker-alt w-5 text-blue-400"></i> {{ config('hiro.company_address') }}</li>
                        <li><i class="fab fa-whatsapp w-5 text-green-400"></i>
                            <a href="https://wa.me/{{ config('hiro.whatsapp_number') }}" class="hover:text-white transition">+{{ config('hiro.whatsapp_number') }}</a>
                        </li>
                        <li><i class="fas fa-envelope w-5 text-blue-400"></i> {{ config('hiro.company_email') }}</li>
                        <li><i class="fas fa-clock w-5 text-blue-400"></i> {{ config('hiro.operational_hours') }}</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#home" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="#armada" class="hover:text-white transition">Armada</a></li>
                        <li><a href="#paket-tour" class="hover:text-white transition">Paket Tour</a></li>
                        <li><a href="#testimoni" class="hover:text-white transition">Testimoni</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-4">Lokasi Kami</h4>
                    <div class="w-full h-36 rounded-lg overflow-hidden">
                        @if(config('hiro.google_maps_embed'))
                            <iframe src="{{ config('hiro.google_maps_embed') }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        @else
                            <div class="w-full h-full bg-gray-700 flex items-center justify-center text-gray-500 text-sm">
                                <i class="fas fa-map-marked-alt text-3xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex gap-3 mt-4">
                        <a href="{{ config('hiro.instagram') }}" target="_blank" class="text-gray-400 hover:text-pink-400 transition text-xl">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="{{ config('hiro.facebook') }}" target="_blank" class="text-gray-400 hover:text-blue-400 transition text-xl">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://wa.me/{{ config('hiro.whatsapp_number') }}" target="_blank" class="text-gray-400 hover:text-green-400 transition text-xl">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ config('hiro.company_name') }}. All rights reserved.
            </div>
        </div>
    </footer>

    <a href="https://wa.me/{{ config('hiro.whatsapp_number') }}?text={{ urlencode(config('hiro.whatsapp_message')) }}"
       target="_blank"
       class="floating-wa pulse-animation">
        <i class="fab fa-whatsapp text-3xl"></i>
    </a>

    @stack('scripts')
</body>
</html>
