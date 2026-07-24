<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingInquiry extends Model
{
    use HasFactory;

    protected $table = 'bookings_inquiries';

    protected $fillable = [
        'customer_name', 'phone_number', 'booking_type',
        'item_id', 'booking_date', 'notes', 'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function item()
    {
        if ($this->booking_type === 'vehicle') {
            return $this->belongsTo(Vehicle::class, 'item_id');
        }
        return $this->belongsTo(TourPackage::class, 'item_id');
    }
}
