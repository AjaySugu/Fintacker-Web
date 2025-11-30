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
        Schema::create('equity_holdings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demat_account_id');
            $table->unsignedBigInteger('company_id');
            $table->string('isin');
            $table->string('symbol')->nullable();
            $table->string('issuer_name')->nullable();
            $table->decimal('units', 24, 6)->default(0);
            $table->decimal('avg_rate', 24, 6)->default(0);
            $table->decimal('last_traded_price', 24, 6)->default(0);
            $table->decimal('investment_value', 24, 6)->default(0);
            $table->decimal('current_value', 24, 6)->default(0);
            $table->json('raw')->nullable();
            $table->timestamps();
            $table->unique(['demat_account_id','isin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equity_holdings');
    }
};
