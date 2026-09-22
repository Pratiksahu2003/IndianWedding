<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('price_monthly')->default(0);
            $table->unsignedInteger('max_users')->default(5);
            $table->unsignedInteger('max_projects')->default(20);
            $table->unsignedInteger('max_clients')->default(50);
            $table->unsignedBigInteger('max_storage_mb')->default(5120);
            $table->unsignedInteger('max_whatsapp_messages')->default(100);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('legal_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('India');
            $table->string('timezone')->default('Asia/Kolkata');
            $table->string('currency', 8)->default('INR');
            $table->string('locale', 8)->default('en');
            $table->string('tax_id')->nullable();
            $table->string('invoice_prefix')->default('INV');
            $table->unsignedInteger('invoice_next_number')->default(1);
            $table->string('brand_primary')->default('#C4A574');
            $table->string('brand_dark')->default('#1A1612');
            $table->boolean('is_active')->default(true);
            $table->timestamp('trial_ends_at')->nullable();
            $table->string('subscription_status')->default('trial');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('organization_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->boolean('is_owner')->default(false);
            $table->json('permissions')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'user_id']);
            $table->index(['organization_id', 'role']);
        });

        Schema::create('organization_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('payment_milestones')->nullable();
            $table->json('whatsapp')->nullable();
            $table->json('email')->nullable();
            $table->json('storage')->nullable();
            $table->json('google_drive')->nullable();
            $table->json('notifications')->nullable();
            $table->json('seo')->nullable();
            $table->json('branding')->nullable();
            $table->json('feature_flags')->nullable();
            $table->timestamps();
        });

        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->boolean('enabled')->default(true);
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('organization_settings');
        Schema::dropIfExists('organization_users');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('plans');
    }
};
