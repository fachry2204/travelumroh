<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->string('group_code', 30)->unique();
            $table->date('departure_date');
            $table->date('return_date');
            $table->string('guide_name', 200)->nullable();
            $table->string('airline', 100)->nullable();
            $table->string('flight_number_departure', 30)->nullable();
            $table->string('flight_number_return', 30)->nullable();
            $table->enum('status', ['preparation', 'ready', 'departed', 'returned', 'cancelled'])->default('preparation');
            $table->timestamps();
        });

        Schema::create('departure_pilgrims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departure_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pilgrim_id')->constrained()->cascadeOnDelete();
            $table->string('room_number', 20)->nullable();
            $table->string('bus_number', 20)->nullable();
            $table->string('manifest_status', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['hero', 'about', 'faq', 'contact', 'seo', 'excellence', 'legality']);
            $table->string('title', 200);
            $table->longText('content')->nullable();
            $table->string('image_path')->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 300);
            $table->string('slug', 300)->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('category', 100)->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('image_path');
            $table->string('caption', 500)->nullable();
            $table->string('category', 100)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question', 500);
            $table->text('answer');
            $table->string('category', 100)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('photo')->nullable();
            $table->string('package_name', 200)->nullable();
            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 300);
            $table->text('message');
            $table->enum('channel', ['app', 'email', 'whatsapp'])->default('app');
            $table->enum('status', ['pending', 'sent', 'failed', 'read'])->default('pending');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100);
            $table->string('table_name', 100)->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('departure_pilgrims');
        Schema::dropIfExists('departures');
    }
};
