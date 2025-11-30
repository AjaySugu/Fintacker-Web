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
        Schema::create('equity_transactions', function (Blueprint $table) {
           $table->id();
            $table->unsignedBigInteger('demat_account_id');
            $table->string('txn_id')->nullable();
            $table->string('txn_type')->nullable();
            $table->string('instrument_type')->nullable();
            $table->string('exchange')->nullable();
            $table->string('isin')->nullable();
            $table->string('symbol')->nullable();
            $table->string('company_name')->nullable();
            $table->decimal('units', 24, 6)->nullable();
            $table->decimal('rate', 24, 6)->nullable();
            $table->decimal('trade_value', 24, 6)->nullable();
            $table->decimal('other_charges', 24, 6)->nullable();
            $table->decimal('total_charge', 24, 6)->nullable();
            $table->dateTime('transaction_date_time')->nullable();
            $table->text('narration')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();
            $table->index('txn_id');
            $table->index('isin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equity_transactions');
    }
};
