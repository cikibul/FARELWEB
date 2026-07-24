@props(['package'])

@php
    $waNumber = config('hiro.whatsapp_number');
    $waText = urlencode("Halo, saya tertarik dengan paket {$package->name}. Mohon info lebih lanjut.");
    $waUrl = "https://wa.me/{$waNumber}?text={$waText}";
@endphp

<div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition group">
    <div class="relative overflow-hidden">
        <img src="{{ asset($package->image) }}"
             alt="{{ $package->name }}"
             loading="lazy"
             class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
        <span class="absolute top-3 right-3 bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
            <i class="far fa-clock mr-1"></i>{{ $package->duration_label }}
        </span>
    </div>

    <div class="p-5">
        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $package->name }}</h3>

        <div class="mb-3">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Destinasi:</h4>
            <p class="text-sm text-gray-700">{{ $package->destinations }}</p>
        </div>

        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($package->amenities as $amenity)
                <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs px-2 py-1 rounded-full">
                    <i class="fas fa-check-circle text-green-500"></i> {{ $amenity }}
                </span>
            @endforeach
        </div>

        @if($package->price_per_pax || $package->price_per_package)
            <div class="p-3 bg-orange-50 rounded-xl mb-4 text-center">
                @if($package->price_per_pax)
                    <span class="text-xs text-gray-500 font-medium">Mulai dari</span>
                    <p class="text-lg font-bold text-orange-600">Rp{{ number_format($package->price_per_pax, 0, ',', '.') }}/orang</p>
                @endif
                @if($package->price_per_package)
                    <span class="text-xs text-gray-500 font-medium">atau</span>
                    <p class="text-lg font-bold text-orange-600">Rp{{ number_format($package->price_per_package, 0, ',', '.') }}/paket</p>
                @endif
            </div>
        @endif

        <a href="{{ $waUrl }}"
           target="_blank"
           class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-xl transition">
            <i class="fab fa-whatsapp"></i>
            <span>Tanya / Custom Paket</span>
        </a>
    </div>
</div>
