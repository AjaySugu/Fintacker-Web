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
        Schema::create('MF_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('amc')->nullable();
            $table->string('scheme_code')->unique();
            $table->string('scheme_plan')->nullable();
            $table->string('scheme_option')->nullable();
            $table->string('scheme_category')->nullable();
            $table->string('isin')->unique();
            $table->string('isin_description')->nullable();
            $table->string('ucc')->nullable();
            $table->string('amfi_code')->nullable();
            $table->string('registrar')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MF_schemes');
    }
};
