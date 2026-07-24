<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestimonialAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $testimonials = Testimonial::orderBy('sort_order')
            ->paginate($request->per_page ?? 15);

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

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|max:2000',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'is_approved' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        $testimonial = Testimonial::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial created successfully',
            'data' => $testimonial,
        ], 201);
    }

    public function show(Testimonial $testimonial): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $testimonial,
        ]);
    }

    public function update(Request $request, Testimonial $testimonial): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|max:2000',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'is_approved' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        $testimonial->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial updated successfully',
            'data' => $testimonial,
        ]);
    }

    public function destroy(Testimonial $testimonial): JsonResponse
    {
        $testimonial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial deleted successfully',
        ]);
    }

    public function approve(Testimonial $testimonial): JsonResponse
    {
        $testimonial->update(['is_approved' => !$testimonial->is_approved]);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial approval status toggled',
            'data' => $testimonial,
        ]);
    }
}
