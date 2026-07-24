<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 'rating', 'content', 'photo', 'is_approved', 'sort_order',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];
}
