<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBookingStatusRequest;
use App\Models\BookingInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = BookingInquiry::orderBy('created_at', 'desc');

        // admin_tour can only see tour bookings
        if ($request->user()->isAdminTour()) {
            $query->where('booking_type', 'tour');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('booking_type')) {
            $query->where('booking_type', $request->booking_type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $request->search . '%');
            });
        }

        $bookings = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'message' => 'Bookings retrieved successfully',
            'data' => $bookings->items(),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    public function show(BookingInquiry $booking): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $booking,
        ]);
    }

    public function updateStatus(UpdateBookingStatusRequest $request, BookingInquiry $booking): JsonResponse
    {
        $booking->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully',
            'data' => $booking,
        ]);
    }

    public function destroy(BookingInquiry $booking): JsonResponse
    {
        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully',
        ]);
    }
}
