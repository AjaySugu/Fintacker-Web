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
        Schema::create('MF_nav_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folio_holding_id');
            $table->decimal('nav', 18, 6)->default(0);
            $table->date('nav_date');
            $table->timestamps();

            $table->unique(['folio_holding_id','nav_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MF_nav_histories');
    }
};
