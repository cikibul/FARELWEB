<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInquiryRequest;
use App\Models\BookingInquiry;
use App\Models\Testimonial;
use App\Models\TourPackage;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicCatalogController extends Controller
{
    public function vehicles(Request $request): JsonResponse
    {
        $query = Vehicle::where('is_available', true)->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $vehicles = $query->paginate($request->per_page ?? 12);

        return response()->json([
            'success' => true,
            'message' => 'Vehicles retrieved successfully',
            'data' => $vehicles->items(),
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total(),
            ],
        ]);
    }

    public function tours(Request $request): JsonResponse
    {
        $query = TourPackage::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('duration_label')) {
            $query->where('duration_label', $request->duration_label);
        }

        $tours = $query->paginate($request->per_page ?? 12);

        return response()->json([
            'success' => true,
            'message' => 'Tour packages retrieved successfully',
            'data' => $tours->items(),
            'meta' => [
                'current_page' => $tours->currentPage(),
                'last_page' => $tours->lastPage(),
                'per_page' => $tours->perPage(),
                'total' => $tours->total(),
            ],
        ]);
    }

    public function testimonials(Request $request): JsonResponse
    {
        $testimonials = Testimonial::where('is_approved', true)
            ->orderBy('sort_order')
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'message' => 'Testimonials retrieved successfully',
            'data' => $testimonials->items(),
            'meta' => [
                'current_page' => $testimonials->currentPage(),
                'last_page' => $testimonials->lastPage(),
                'per_page' => $testimonials->perPage(),
                'total' => $testimonials->total(),
            ],
        ]);
    }

    public function storeInquiry(StoreInquiryRequest $request): JsonResponse
    {
        $inquiry = BookingInquiry::create($request->validated());

        $waNumber = config('hiro.whatsapp_number');
        $item = $inquiry->booking_type === 'vehicle'
            ? Vehicle::find($inquiry->item_id)
            : TourPackage::find($inquiry->item_id);

        $itemName = $item?->name ?? 'N/A';
        $waMessage = "Halo, saya {$inquiry->customer_name}. Saya ingin {$inquiry->booking_type} {$itemName} tanggal {$inquiry->booking_date->format('d/m/Y')}.";
        $waUrl = "https://wa.me/{$waNumber}?text=" . urlencode($waMessage);

        return response()->json([
            'success' => true,
            'message' => 'Inquiry submitted successfully. We will contact you via WhatsApp.',
            'data' => [
                'id' => $inquiry->id,
                'customer_name' => $inquiry->customer_name,
                'booking_type' => $inquiry->booking_type,
                'item' => $item ? ['id' => $item->id, 'name' => $item->name] : null,
                'booking_date' => $inquiry->booking_date->format('Y-m-d'),
                'status' => $inquiry->status,
                'wa_url' => $waUrl,
                'created_at' => $inquiry->created_at,
            ],
        ], 201);
    }
}
