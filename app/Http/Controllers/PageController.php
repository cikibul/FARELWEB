<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\TourPackage;
use App\Models\Testimonial;

class PageController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::where('is_available', true)->orderBy('sort_order')->get();
        $tourPackages = TourPackage::where('is_active', true)->orderBy('sort_order')->get();
        $testimonials = Testimonial::where('is_approved', true)->orderBy('sort_order')->get();

        return view('index', compact('vehicles', 'tourPackages', 'testimonials'));
    }
}
