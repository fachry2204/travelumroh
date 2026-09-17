<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilgrim_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilgrim_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', ['ktp', 'kk', 'passport', 'photo', 'marriage_book', 'birth_certificate', 'vaccine', 'mahram', 'other']);
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->enum('status', ['pending', 'valid', 'rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('payment_number', 30)->unique();
            $table->enum('payment_type', ['dp', 'installment', 'final', 'refund'])->default('dp');
            $table->decimal('amount', 15, 2);
            $table->enum('method', ['bank_transfer', 'cash', 'payment_gateway'])->default('bank_transfer');
            $table->string('proof_file')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('account_name', 200)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->enum('status', ['pending', 'review', 'approved', 'rejected', 'refunded'])->default('pending');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('commission_value', 10, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('pilgrim_documents');
    }
};
