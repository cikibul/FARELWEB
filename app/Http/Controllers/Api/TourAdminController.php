<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Models\TourPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TourPackage::orderBy('sort_order');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $tours = $query->paginate($request->per_page ?? 15);

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

    public function store(StoreTourRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('packages', 'public');
        }

        $tour = TourPackage::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tour package created successfully',
            'data' => $tour,
        ], 201);
    }

    public function show(TourPackage $tour): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $tour,
        ]);
    }

    public function update(StoreTourRequest $request, TourPackage $tour): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('packages', 'public');
        }

        $tour->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Tour package updated successfully',
            'data' => $tour,
        ]);
    }

    public function destroy(TourPackage $tour): JsonResponse
    {
        $tour->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tour package deleted successfully',
        ]);
    }
}
