<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number', 30)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_id')->constrained();
            $table->foreignId('agent_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('representative_id')->nullable()->constrained()->nullOnDelete();
            $table->string('referral_code', 20)->nullable();
            $table->enum('source', ['direct', 'agent', 'representative', 'admin'])->default('direct');
            $table->unsignedInteger('total_pilgrims')->default(1);
            $table->enum('room_type', ['quad', 'triple', 'double'])->default('quad');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('outstanding_amount', 15, 2)->default(0);
            $table->enum('booking_status', ['pending', 'dp', 'paid', 'cancelled'])->default('pending');
            $table->enum('document_status', ['incomplete', 'review', 'complete'])->default('incomplete');
            $table->enum('visa_status', ['not_submitted', 'process', 'issued'])->default('not_submitted');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pilgrims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name', 200);
            $table->string('nik', 20)->nullable();
            $table->string('family_card_number', 20)->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('job', 100)->nullable();
            $table->string('education', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('passport_number', 30)->nullable();
            $table->string('passport_issued_place', 100)->nullable();
            $table->date('passport_issued_date')->nullable();
            $table->date('passport_expired_date')->nullable();
            $table->string('passport_name', 200)->nullable();
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable();
            $table->text('medical_history')->nullable();
            $table->text('allergy')->nullable();
            $table->text('special_needs')->nullable();
            $table->string('emergency_contact_name', 200)->nullable();
            $table->string('emergency_contact_relation', 100)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->text('emergency_contact_address')->nullable();
            $table->enum('room_type', ['quad', 'triple', 'double'])->nullable();
            $table->string('room_number', 20)->nullable();
            $table->string('bus_number', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilgrims');
        Schema::dropIfExists('bookings');
    }
};
