<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'duration_label', 'destinations', 'itinerary',
        'price_per_pax', 'price_per_package', 'amenities', 'image',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
        'price_per_pax' => 'decimal:2',
        'price_per_package' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (TourPackage $package) {
            $package->slug = empty($package->slug) ? Str::slug($package->name) : $package->slug;
        });
    }
}
