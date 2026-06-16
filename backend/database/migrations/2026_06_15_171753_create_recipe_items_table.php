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
        Schema::create('recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained()                // FK → products.id
                  ->onDelete('cascade');          // delete product → delete its recipes
            $table->foreignId('ingredient_id')
                  ->constrained()                // FK → ingredients.id
                  ->onDelete('cascade');
            $table->decimal('quantity_needed', 10, 4); // 0.5000 kg per loaf
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_items');
    }
};
