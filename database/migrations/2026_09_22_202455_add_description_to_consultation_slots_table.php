<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_slots', function (Blueprint $table) {
            $table->string('title')->nullable()->after('staff_user_id');
            $table->text('description')->nullable()->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('consultation_slots', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });
    }
};
