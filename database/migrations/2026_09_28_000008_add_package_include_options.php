<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('includes_candid')->default(false)->after('includes_drone');
            $table->boolean('includes_instagram_reels')->default(false)->after('includes_candid');
            $table->boolean('includes_same_day_edit')->default(false)->after('includes_instagram_reels');
            $table->boolean('includes_live_streaming')->default(false)->after('includes_same_day_edit');
            $table->boolean('includes_invitation_video')->default(false)->after('includes_live_streaming');
            $table->boolean('includes_destination')->default(false)->after('includes_invitation_video');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'includes_candid',
                'includes_instagram_reels',
                'includes_same_day_edit',
                'includes_live_streaming',
                'includes_invitation_video',
                'includes_destination',
            ]);
        });
    }
};
