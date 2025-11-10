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
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('account_name'); // e.g., HDFC Savings, Google Pay, Cash
            $table->enum('account_type', [
                'bank', 'wallet', 'credit_card', 'upi', 'cash', 'investment'
            ]);
            $table->string('institution_name')->nullable(); // HDFC Bank, Paytm, Visa
            $table->string('account_number')->nullable();
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('credit_limit', 15, 2)->nullable(); // For credit cards
            $table->boolean('is_synced')->default(false); // real-time API sync
            $table->json('sync_metadata')->nullable(); // API reference data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accounts');
    }
};
