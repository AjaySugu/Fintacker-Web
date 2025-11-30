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
        Schema::create('demant_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('consent_id');
            $table->string('link_ref')->nullable();
            $table->string('masked_acc_number')->nullable();
            $table->string('masked_demat_id')->nullable();
            $table->string('fip_id')->nullable();
            $table->string('broker_name')->nullable();
            $table->string('status')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
            $table->index('consent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demant_accounts');
    }
};
