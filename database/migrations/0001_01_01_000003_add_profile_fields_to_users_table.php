<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->ulid('ulid')->nullable()->unique()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('avatar_path')->nullable()->after('whatsapp');
            $table->boolean('is_super_admin')->default(false)->after('avatar_path');
            $table->boolean('is_active')->default(true)->after('is_super_admin');
            $table->string('timezone')->default('Asia/Kolkata')->after('is_active');
            $table->string('locale', 8)->default('en')->after('timezone');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->json('notification_preferences')->nullable()->after('last_login_ip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ulid', 'phone', 'whatsapp', 'avatar_path', 'is_super_admin',
                'is_active', 'timezone', 'locale', 'last_login_at', 'last_login_ip',
                'notification_preferences',
            ]);
        });
    }
};
