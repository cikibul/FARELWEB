@props(['vehicle'])

<div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition group">
    <div class="relative overflow-hidden">
        <img src="{{ asset($vehicle->image) }}"
             alt="{{ $vehicle->name }}"
             loading="lazy"
             class="w-full h-52 object-cover group-hover:scale-105 transition duration-500">
        @if($vehicle->badge)
            <span class="absolute top-3 left-3 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full shadow">
                <i class="fas fa-star mr-1"></i>{{ $vehicle->badge }}
            </span>
        @endif
    </div>

    <div class="p-5">
        <h3 class="text-lg font-bold text-gray-900 mb-3">{{ $vehicle->name }}</h3>

        <div class="flex flex-wrap gap-3 mb-4 text-sm text-gray-600">
            <span class="inline-flex items-center gap-1 bg-gray-100 px-3 py-1 rounded-full">
                <i class="fas fa-users text-blue-500"></i> {{ $vehicle->passenger_capacity }} Pax
            </span>
            <span class="inline-flex items-center gap-1 bg-gray-100 px-3 py-1 rounded-full">
                <i class="fas fa-cog text-blue-500"></i> {{ $vehicle->transmission }}
            </span>
            @foreach($vehicle->inclusions as $inclusion)
                <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 px-3 py-1 rounded-full">
                    <i class="fas fa-check-circle text-green-500 text-xs"></i> {{ $inclusion }}
                </span>
            @endforeach
        </div>

        <div class="grid grid-cols-2 gap-3 mb-4 p-3 bg-blue-50 rounded-xl">
            <div class="text-center">
                <span class="text-xs text-gray-500 font-medium">Half Day</span>
                <p class="text-lg font-bold text-blue-600">Rp{{ number_format($vehicle->price_half_day, 0, ',', '.') }}</p>
            </div>
            <div class="text-center">
                <span class="text-xs text-gray-500 font-medium">Full Day</span>
                <p class="text-lg font-bold text-blue-600">Rp{{ number_format($vehicle->price_full_day, 0, ',', '.') }}</p>
            </div>
        </div>

        <a href="{{ $vehicle->wa_url }}"
           target="_blank"
           class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-xl transition">
            <i class="fab fa-whatsapp"></i>
            <span>Sewa Unit Ini</span>
        </a>
    </div>
</div>
