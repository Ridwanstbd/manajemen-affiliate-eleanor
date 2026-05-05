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
        Schema::create('sample_task_reports', function (Blueprint $table) {
            $table->foreignId('sample_request_id')->constrained('sample_requests')->onDelete('cascade');
            $table->foreignId('task_report_id')->constrained('task_reports')->onDelete('cascade');
            $table->primary(['sample_request_id', 'task_report_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_task_reports');
    }
};
