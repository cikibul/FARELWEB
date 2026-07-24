<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 191)->unique();
            $table->string('duration_label');
            $table->text('destinations');
            $table->text('itinerary')->nullable();
            $table->decimal('price_per_pax', 12, 2)->nullable();
            $table->decimal('price_per_package', 12, 2)->nullable();
            $table->json('amenities');
            $table->string('image');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};
