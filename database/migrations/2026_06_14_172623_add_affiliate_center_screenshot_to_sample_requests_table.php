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
        Schema::table('sample_requests', function (Blueprint $table) {
            $table->string('affiliate_center_screenshot', 500)
                  ->nullable()
                  ->after('address')
                  ->comment('Path screenshot affiliate center 7 hari terakhir (format .webp, terkompresi)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            $table->dropColumn('affiliate_center_screenshot');
        });
    }
};