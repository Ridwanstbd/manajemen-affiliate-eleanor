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
        Schema::create('core_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_history_id')->constrained('import_histories')->onDelete('cascade');
            $table->decimal('affiliate_gmv', 15, 2)->default(0);
            $table->integer('items_sold')->default(0);
            $table->decimal('refunds', 15, 2)->default(0);
            $table->integer('items_returned')->default(0);
            $table->integer('avg_daily_buyers')->default(0);
            $table->decimal('aov', 15, 2)->default(0);
            $table->integer('video_count')->default(0);
            $table->integer('live_count')->default(0);
            $table->integer('avg_daily_sales_creators')->default(0);
            $table->integer('avg_daily_posting_creators')->default(0);
            $table->integer('avg_daily_items_sold')->default(0);
            $table->integer('samples_sent')->default(0);
            $table->decimal('estimated_commission', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_metrics');
    }
};
