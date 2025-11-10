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
        Schema::create('otp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->enum('sent_for',['login-sendOtp', 'login-verifyOtp'])->default('login-sendOtp');
            $table->string('otp')->nullable();
            $table->string('message_sid')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'undelivered','user-verified'])->default('pending');
            $table->string('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_logs');
    }
};
