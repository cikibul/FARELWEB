<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicCatalogController;
use App\Http\Controllers\Api\VehicleAdminController;
use App\Http\Controllers\Api\TourAdminController;
use App\Http\Controllers\Api\BookingAdminController;
use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Support\Facades\Route;

// ── Public Routes ─────────────────────────────────────────
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/vehicles', [PublicCatalogController::class, 'vehicles']);
    Route::get('/tours', [PublicCatalogController::class, 'tours']);
    Route::get('/testimonials', [PublicCatalogController::class, 'testimonials']);
    Route::post('/inquiries', [PublicCatalogController::class, 'storeInquiry']);
});

// ── Protected Routes (auth:sanctum) ──────────────────────
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // ── Admin Web Only (role:admin_web) ──────────────────
    Route::middleware('role:admin_web')->group(function () {
        // Vehicles CRUD
        Route::apiResource('/admin/vehicles', VehicleAdminController::class);

        // Testimonials CRUD
        Route::apiResource('/admin/testimonials', \App\Http\Controllers\Api\TestimonialAdminController::class);
        Route::patch('/admin/testimonials/{testimonial}/approve', [\App\Http\Controllers\Api\TestimonialAdminController::class, 'approve']);

        // User Management CRUD
        Route::apiResource('/admin/users', UserManagementController::class);
    });

    // ── Admin Tour Routes (role:admin_tour,admin_web) ────
    Route::middleware('role:admin_tour,admin_web')->group(function () {
        // Tour Packages CRUD
        Route::apiResource('/admin/tours', TourAdminController::class);

        // Bookings/Inquiries
        Route::get('/admin/inquiries', [BookingAdminController::class, 'index']);
        Route::get('/admin/inquiries/{booking}', [BookingAdminController::class, 'show']);
        Route::patch('/admin/inquiries/{booking}/status', [BookingAdminController::class, 'updateStatus']);
        Route::delete('/admin/inquiries/{booking}', [BookingAdminController::class, 'destroy']);
    });
});
