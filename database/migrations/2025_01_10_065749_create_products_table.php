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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('slug');
            $table->text('description');
            $table->integer('price');
            $table->integer('stock');
            $table->boolean('is_active')->default(false);
            $table->text('image_url')->nullable();
            $table->integer('weight')->nullable()->default(0)->commentct('weight in grams');
            $table->integer('height')->nullable()->default(0)->commentct('height in cm');
            $table->integer('width')->nullable()->default(0)->commentct('width in cm');
            $table->integer('length')->nullable()->default(0)->commentct('length in cm');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
