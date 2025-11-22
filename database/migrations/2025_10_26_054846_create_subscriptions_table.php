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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name', 100);
            $table->decimal('amount', 12, 2);
            $table->unsignedBigInteger('category_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('frequency', ['daily','weekly','monthly','yearly'])->default('monthly');
            $table->boolean('auto_pay')->default(false);
            $table->enum('status', ['active','paused','cancelled'])->default('active');
            $table->date('last_paid');
            $table->enum('paid_status',['paid','unpaid','pending'])->default('paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
