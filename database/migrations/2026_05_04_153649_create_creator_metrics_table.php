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
            $table->foreignId('import_history_id')->constrained('import_histories')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('affiliate_gmv', 15, 2); // Sebelumnya gmv
            $table->decimal('refunds', 15, 2);
            $table->integer('attributed_orders');
            $table->integer('items_sold');
            $table->integer('items_returned');
            $table->decimal('aov', 15, 2);
            $table->decimal('avg_daily_items_sold', 10, 2); // Sebelumnya 15,2, diperbaiki 10,2
            $table->integer('video_count');
            $table->integer('live_count');
            $table->decimal('estimated_commission', 15, 2);
            $table->integer('samples_sent');
            $table->timestamps();

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
