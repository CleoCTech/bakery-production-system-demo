<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionBatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Assumes BakerySeeder has already run (products + users exist).
     */
    public function run(): void
    {
        $baker = User::where('email', 'baker@bakery.co.ke')->first();

        $bread = Product::where('name', 'White Bread')->first();
        $cake = Product::where('name', 'Chocolate Cake')->first();
        $mandazi = Product::where('name', 'Mandazi')->first();

        if (! $baker || ! $bread || ! $cake || ! $mandazi) {
            $this->command->warn('ProductionBatchSeeder skipped: run BakerySeeder first.');
            return;
        }

        // A planned batch waiting to start
        ProductionBatch::create([
            'product_id' => $bread->id,
            'user_id' => $baker->id,
            'planned_quantity' => 100,
            'status' => 'planned',
        ]);

        // A batch in progress (mixing) — ingredients deducted, started
        ProductionBatch::create([
            'product_id' => $mandazi->id,
            'user_id' => $baker->id,
            'planned_quantity' => 200,
            'status' => 'baking',
            'started_at' => now()->subHours(1),
        ]);

        // A completed batch with a healthy yield
        ProductionBatch::create([
            'product_id' => $cake->id,
            'user_id' => $baker->id,
            'planned_quantity' => 20,
            'actual_quantity' => 19,
            'wastage_quantity' => 1,
            'status' => 'done',
            'started_at' => now()->subHours(4),
            'completed_at' => now()->subHours(2),
        ]);

        // A completed batch with low yield (for analytics / alerts)
        ProductionBatch::create([
            'product_id' => $bread->id,
            'user_id' => $baker->id,
            'planned_quantity' => 100,
            'actual_quantity' => 70,
            'wastage_quantity' => 30,
            'status' => 'done',
            'started_at' => now()->subDay()->subHours(3),
            'completed_at' => now()->subDay(),
        ]);

        // A failed batch
        ProductionBatch::create([
            'product_id' => $mandazi->id,
            'user_id' => $baker->id,
            'planned_quantity' => 150,
            'wastage_quantity' => 150,
            'status' => 'failed',
            'started_at' => now()->subDays(2),
            'completed_at' => now()->subDays(2)->addHour(),
        ]);
    }
}
