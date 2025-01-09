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
        Schema::create('raja_ongkir_settings', function (Blueprint $table) {
            $table->id();
            $table->string('api_key')->nullable();
            $table->enum('api_type', ['starter', 'pro'])->default('starter');
            $table->string('couriers')->nullable();
            $table->boolean('is_valid')->default(false);
            $table->text('error_message')->nullable(); // Fixed column name
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raja_ongkir_settings');
    }
};
