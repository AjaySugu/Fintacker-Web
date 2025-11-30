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
        Schema::create('MF_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folio_id');
            $table->unsignedBigInteger('scheme_id');
            $table->string('txn_id')->nullable();
            $table->string('txn_type')->nullable();
            $table->string('mode')->nullable();
            $table->decimal('units',18,6)->default(0);
            $table->decimal('amount',18,2)->default(0);
            $table->decimal('nav',18,6)->default(0);
            $table->dateTime('transaction_date')->nullable();
            $table->string('narration')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->unique(['folio_id','txn_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MF_transactions');
    }
};
