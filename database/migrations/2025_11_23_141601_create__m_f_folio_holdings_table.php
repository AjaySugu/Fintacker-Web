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
        Schema::create('MF_folio_holdings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folio_id');
            $table->unsignedBigInteger('scheme_id');
            $table->decimal('units', 18, 6)->default(0);
            $table->decimal('lien_units', 18, 6)->default(0);
            $table->decimal('lockin_units', 18, 6)->default(0);
            $table->decimal('nav', 18, 6)->default(0);
            $table->date('nav_date')->nullable();
            $table->decimal('cost_value', 18, 2)->default(0);
            $table->decimal('current_value', 18, 2)->default(0);
            $table->string('fatca_status')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->unique(['folio_id','scheme_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MF_folio_holdings');
    }
};
