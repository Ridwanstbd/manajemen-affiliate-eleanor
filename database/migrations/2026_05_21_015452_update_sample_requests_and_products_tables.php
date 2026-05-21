<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE sample_requests MODIFY COLUMN status ENUM('PENDING', 'APPROVED', 'SHIPPED', 'DELIVERED', 'REJECTED') DEFAULT 'PENDING'");

        Schema::table('sample_request_details', function (Blueprint $table) {
            $table->integer('mandatory_video_count')->nullable()->after('quantity');
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING')->after('mandatory_video_count');
            $table->text('reject_reason')->nullable()->after('status');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['mandatory_video_count', 'stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('mandatory_video_count')->default(3);
            $table->integer('stock')->default(0);
        });

        Schema::table('sample_request_details', function (Blueprint $table) {
            $table->dropColumn(['mandatory_video_count', 'status', 'reject_reason']);
        });

        DB::statement("ALTER TABLE sample_requests MODIFY COLUMN status ENUM('PENDING', 'APPROVED', 'SHIPPED', 'REJECTED') DEFAULT 'PENDING'");
    }
};