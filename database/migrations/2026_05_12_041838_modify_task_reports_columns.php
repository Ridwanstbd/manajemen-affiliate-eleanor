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
        Schema::table('task_reports', function (Blueprint $table) {
            $table->string('tiktok_video_link', 1000)->nullable()->change();
        });

        DB::statement("ALTER TABLE task_reports MODIFY COLUMN task_status ENUM('PROCESSING', 'COMPLETED', 'OVERDUE') DEFAULT 'PROCESSING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE task_reports MODIFY COLUMN task_status ENUM('PROCESSING', 'COMPLETED') DEFAULT 'PROCESSING'");
        
        Schema::table('task_reports', function (Blueprint $table) {
            $table->string('tiktok_video_link', 1000)->nullable(false)->change();
        });
    }
};
