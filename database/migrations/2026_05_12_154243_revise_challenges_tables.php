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
        Schema::table('challenges', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('rules');
            $table->date('end_date')->nullable()->after('start_date');
            $table->boolean('is_active')->default(true)->after('banner_image_path');
            
            $table->dropColumn(['target', 'prize']);
        });

        Schema::create('challenge_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained('challenges')->onDelete('cascade');
            $table->string('target_metric');
            $table->decimal('target_value', 15, 2);
            $table->string('reward_description'); 
            $table->timestamps();
        });

        Schema::create('challenge_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained('challenges')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('category');
            $table->string('reward_given'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_winners');
        Schema::dropIfExists('challenge_rewards');

        Schema::table('challenges', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'is_active']);
            
            $table->integer('target')->nullable();
            $table->string('prize', 255)->nullable();
        });
    }
};