<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 200);
            $table->string('slug', 200)->unique();
            $table->date('departure_date');
            $table->date('return_date');
            $table->unsignedInteger('duration_days');
            $table->string('airline', 100)->nullable();
            $table->string('departure_airport', 100)->nullable();
            $table->string('makkah_hotel', 200)->nullable();
            $table->string('madinah_hotel', 200)->nullable();
            $table->decimal('price_quad', 15, 2)->nullable();
            $table->decimal('price_triple', 15, 2)->nullable();
            $table->decimal('price_double', 15, 2)->nullable();
            $table->decimal('minimum_dp', 15, 2)->nullable();
            $table->unsignedInteger('quota')->default(0);
            $table->unsignedInteger('remaining_seat')->default(0);
            $table->text('facilities')->nullable();
            $table->text('excluded')->nullable();
            $table->text('description')->nullable();
            $table->string('featured_image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('package_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('day_number');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_itineraries');
        Schema::dropIfExists('packages');
    }
};
