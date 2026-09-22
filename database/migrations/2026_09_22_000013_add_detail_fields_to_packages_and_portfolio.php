<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->longText('body')->nullable()->after('description');
            $table->string('cover_image')->nullable()->after('body');
        });

        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->string('couple')->nullable()->after('location');
            $table->date('event_date')->nullable()->after('couple');
            $table->longText('story')->nullable()->after('event_date');
            $table->index(['organization_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['body', 'cover_image']);
        });

        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->dropIndex(['organization_id', 'slug']);
            $table->dropColumn(['slug', 'couple', 'event_date', 'story']);
        });
    }
};
