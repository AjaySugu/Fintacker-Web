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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('Null = default category, Not null = user custom category');
            $table->string('name', 100);
            $table->string('icon', 100)->nullable()->default('default-icon');
            $table->string('color', 20)->nullable()->default('#e5e5e5');
            $table->enum('type', ['income', 'expense', 'investment'])->default('expense');
            $table->boolean('is_ai_suggested')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
