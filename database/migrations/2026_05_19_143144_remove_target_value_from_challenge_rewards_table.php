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
        Schema::table('challenge_rewards', function (Blueprint $table) {
            $table->dropColumn('target_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challenge_rewards', function (Blueprint $table) {
            $table->decimal('target_value', 15, 2)->after('target_metric');
        });
    }
};