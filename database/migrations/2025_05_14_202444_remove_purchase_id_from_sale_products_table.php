<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sale_products', function (Blueprint $table) {
           // First drop the foreign key
           $table->dropForeign(['purchase_id']);

           // Then drop the column
           $table->dropColumn('purchase_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_products', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_id')->nullable();

            // Restore the foreign key (if needed)
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
        });
    }
};
