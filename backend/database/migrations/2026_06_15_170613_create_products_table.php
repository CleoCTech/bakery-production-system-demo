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
            $table->string('name', 100);                     // VARCHAR(100) NOT NULL
            $table->foreignId('category_id')                 // FK → categories.id
                  ->constrained()
                  ->onDelete('restrict');                     // can't delete a category that has products
            $table->decimal('selling_price', 10, 2);         // DECIMAL(10,2) — KES
            $table->string('unit', 20)->default('piece');    // loaf, piece, etc.
            $table->integer('shelf_life_hours')->default(24);
            $table->boolean('is_active')->default(true);
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
