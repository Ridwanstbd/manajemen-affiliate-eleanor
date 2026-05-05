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
        Schema::create('sample_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['PENDING', 'APPROVED', 'SHIPPED', 'REJECTED'])->default('PENDING');
            $table->string('address', 255);
            $table->string('tracking_number', 100)->nullable();
            $table->string('courier', 100)->nullable();
            $table->decimal('shipping_cost', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_requests');
    }
};
