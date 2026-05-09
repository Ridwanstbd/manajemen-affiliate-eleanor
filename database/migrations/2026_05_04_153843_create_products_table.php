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
        Schema::create('products', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name', 255);
            $table->string('category',100)->nullable();
            $table->string('sku_id')->nullable();
            $table->string('variation_value')->nullable();
            $table->longText('product_detail')->nullable();
            $table->string('brand')->nullable();

            $table->decimal('price', 15, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('seller_sku')->nullable();
            $table->decimal('parcel_weight', 8, 2)->nullable();
            $table->decimal('parcel_length', 8, 2)->nullable();
            $table->decimal('parcel_width', 8, 2)->nullable();
            $table->decimal('parcel_height', 8, 2)->nullable();

            $table->text('image_path')->nullable();
            $table->json('additional_images')->nullable(); 
            $table->integer('mandatory_video_count')->default(3);
            $table->boolean('is_visible')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
