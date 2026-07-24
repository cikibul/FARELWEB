<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'category', 'passenger_capacity', 'transmission',
        'price_half_day', 'price_full_day', 'description', 'image', 'badge',
        'inclusions', 'is_available', 'sort_order',
    ];

    protected $casts = [
        'inclusions' => 'array',
        'is_available' => 'boolean',
        'price_half_day' => 'decimal:2',
        'price_full_day' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Vehicle $vehicle) {
            $vehicle->slug = empty($vehicle->slug) ? Str::slug($vehicle->name) : $vehicle->slug;
        });
    }

    public function getWaUrlAttribute(): string
    {
        $number = config('hiro.whatsapp_number');
        $text = urlencode("Halo, saya tertarik dengan {$this->name}. Mohon info ketersediaan dan harga.");
        return "https://wa.me/{$number}?text={$text}";
    }
}
