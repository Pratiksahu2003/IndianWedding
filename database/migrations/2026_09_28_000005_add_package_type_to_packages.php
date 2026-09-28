<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('package_type', 20)->default('service')->after('slug');
            $table->index(['organization_id', 'package_type', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndex(['organization_id', 'package_type', 'is_public']);
            $table->dropColumn('package_type');
        });
    }
};
