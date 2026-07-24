<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 191)->unique();
            $table->string('category');
            $table->integer('passenger_capacity');
            $table->string('transmission');
            $table->decimal('price_half_day', 12, 2);
            $table->decimal('price_full_day', 12, 2);
            $table->text('description')->nullable();
            $table->string('image');
            $table->string('badge')->nullable();
            $table->json('inclusions');
            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
