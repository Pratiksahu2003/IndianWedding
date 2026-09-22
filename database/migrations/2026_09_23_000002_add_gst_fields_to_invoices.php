<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('hsn_sac', 16)->nullable()->after('description');
            $table->unsignedTinyInteger('gst_rate')->default(18)->after('hsn_sac');
            $table->unsignedInteger('taxable_amount')->default(0)->after('amount');
            $table->unsignedInteger('cgst_amount')->default(0)->after('taxable_amount');
            $table->unsignedInteger('sgst_amount')->default(0)->after('cgst_amount');
            $table->unsignedInteger('igst_amount')->default(0)->after('sgst_amount');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('place_of_supply')->nullable()->after('notes');
            $table->string('billing_name')->nullable()->after('place_of_supply');
            $table->string('billing_gstin')->nullable()->after('billing_name');
            $table->text('billing_address')->nullable()->after('billing_gstin');
            $table->boolean('is_interstate')->default(false)->after('billing_address');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['hsn_sac', 'gst_rate', 'taxable_amount', 'cgst_amount', 'sgst_amount', 'igst_amount']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['place_of_supply', 'billing_name', 'billing_gstin', 'billing_address', 'is_interstate']);
        });
    }
};
