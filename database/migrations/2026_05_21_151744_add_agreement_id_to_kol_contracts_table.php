<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kol_contracts', function (Blueprint $table) {
            $table->foreignId('agreement_id')->nullable()->constrained('agreements')->nullOnDelete()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('kol_contracts', function (Blueprint $table) {
            $table->dropForeign(['agreement_id']);
            $table->dropColumn('agreement_id');
        });
    }
};