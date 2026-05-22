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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['parcel_weight', 'parcel_length','parcel_width','parcel_height','additional_images']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('parcel_weight', 8, 2)->nullable();
            $table->decimal('parcel_length', 8, 2)->nullable();
            $table->decimal('parcel_width', 8, 2)->nullable();
            $table->decimal('parcel_height', 8, 2)->nullable();
            $table->json('additional_images')->nullable(); 
        });
    }
};
