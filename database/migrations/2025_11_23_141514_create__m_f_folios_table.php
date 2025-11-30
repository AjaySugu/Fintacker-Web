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
        Schema::create('MF_folios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demat_account_id');
            $table->string('folio_no');
            $table->string('masked_folio_no')->nullable();
            $table->string('pan')->nullable();
            $table->string('profile_type')->nullable();
            $table->json('holders')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->unique(['demat_account_id','folio_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MF_folios');
    }
};
