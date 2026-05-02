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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100);
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('phone_number', 14)->nullable();
            $table->enum('account_status', ['PENDING', 'ACTIVE', 'BANNED'])->default('PENDING');
            $table->boolean('is_claimed')->default(false);
            $table->enum('role', ['ADMINISTRATOR', 'AFFILIATOR'])->default('AFFILIATOR');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};