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
        Schema::create('production_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('user_id')
                  ->constrained();          // FK → users.id (the baker)
            $table->integer('planned_quantity');
            $table->integer('actual_quantity')->nullable();    // null until done
            $table->integer('wastage_quantity')->default(0);
            $table->enum('status', ['planned', 'mixing', 'baking', 'cooling', 'done', 'failed'])
                  ->default('planned');
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_batches');
    }
};
