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
        Schema::create('creator_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('import_history_id');
            $table->decimal('gmv', 15, 2)->default(0);
            $table->decimal('refunds', 15, 2)->default(0);
            $table->integer('attributed_orders')->default(0);
            $table->integer('items_sold')->default(0);
            $table->integer('items_returned')->default(0);
            $table->decimal('aov', 15, 2)->default(0);
            $table->decimal('avg_daily_items_sold', 15, 2)->default(0);
            $table->integer('video_count')->default(0);
            $table->integer('live_count')->default(0);
            $table->decimal('estimated_commission', 15, 2)->default(0);
            $table->integer('samples_sent')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('import_history_id')->references('id')->on('import_histories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creator_metrics');
    }
};
