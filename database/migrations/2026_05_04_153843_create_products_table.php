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
            $table->string('image_path',255)->nullable();
            $table->integer('stock',11)->default(0);
            $table->text('product_detail')->nullable();
            $table->integer('mandatory_video_count',11)->default(3);
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
