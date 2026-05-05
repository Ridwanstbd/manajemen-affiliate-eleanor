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
        Schema::create('video_product_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('video_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('import_history_id');
            $table->decimal('video_gmv', 15, 2)->default(0);
            $table->integer('orders')->default(0);
            $table->decimal('aov', 15, 2)->default(0);
            $table->decimal('avg_gmv_per_buyer', 15, 2)->default(0);
            $table->integer('items_sold')->default(0);
            $table->decimal('refunds', 15, 2)->default(0);
            $table->integer('items_returned')->default(0);
            $table->decimal('estimated_commission', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('import_history_id')->references('id')->on('import_histories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_product_metrics');
    }
};
