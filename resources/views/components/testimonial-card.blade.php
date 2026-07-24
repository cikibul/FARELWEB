@props(['testimonial'])

<div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center h-full">
    <div class="w-16 h-16 rounded-full mx-auto mb-4 overflow-hidden bg-gray-200 flex items-center justify-center">
        @if($testimonial->photo)
            <img src="{{ asset($testimonial->photo) }}" alt="{{ $testimonial->customer_name }}" class="w-full h-full object-cover">
        @else
            <span class="text-2xl font-bold text-gray-400">{{ substr($testimonial->customer_name, 0, 1) }}</span>
        @endif
    </div>

    <div class="flex justify-center gap-1 mb-3">
        @for($i = 1; $i <= 5; $i++)
            @if($i <= $testimonial->rating)
                <i class="fas fa-star text-yellow-400 text-sm"></i>
            @else
                <i class="far fa-star text-gray-300 text-sm"></i>
            @endif
        @endfor
    </div>

    <p class="text-gray-600 text-sm italic mb-4">"{{ $testimonial->content }}"</p>

    <h4 class="font-semibold text-gray-900">{{ $testimonial->customer_name }}</h4>
</div>
