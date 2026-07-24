<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone_number');
            $table->string('booking_type'); // 'vehicle' or 'tour'
            $table->unsignedBigInteger('item_id')->nullable();
            $table->date('booking_date');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings_inquiries');
    }
};
