<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\ProductionBatch;
use App\Models\RecipeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $batches = ProductionBatch::with('product.category')->get();
        return response()->json($batches);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'planned_quantity' => 'required|integer|min:1',
        ]);

        $batch = ProductionBatch::create([
            ...$validated,
            'status' => 'planned',
            'user_id' => $request->user()->id,
        ]);

        return response()->json($batch->load('product'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $batch = ProductionBatch::with('product.category')->findOrFail($id);
        return response()->json($batch);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function advance(Request $request, ProductionBatch $batch)
    {
        $flow = [
            'planned' => 'mixing',
            'mixing' => 'baking',
            'baking' => 'cooling',
            'cooling' => 'done',
        ];

        if (!isset($flow[$batch->status])) {
            return response()->json([
                'message' => "Cannot advance from '{$batch->status}' status"
            ], 422);
        }

        $newStatus = $flow[$batch->status];

        try {
            DB::transaction(function () use ($batch, $newStatus) {
                // If entering 'mixing', deduct ingredient stock
                if ($newStatus === 'mixing') {
                    $recipeItems = RecipeItem::where('product_id', $batch->product_id)->get();

                    if ($recipeItems->isEmpty()) {
                        throw new \Exception('No recipe found for this product. Add recipe items first.');
                    }

                    foreach ($recipeItems as $item) {
                        $ingredient = Ingredient::lockForUpdate()->find($item->ingredient_id);
                        $needed = $item->quantity_needed * $batch->planned_quantity;

                        if ($ingredient->current_stock < $needed) {
                            throw new \Exception(
                                "Insufficient {$ingredient->name}: have {$ingredient->current_stock}{$ingredient->unit}, need {$needed}{$ingredient->unit}"
                            );
                        }

                        $ingredient->current_stock -= $needed;
                        $ingredient->save();
                    }

                    $batch->started_at = now();
                }

                if ($newStatus === 'done') {
                    $batch->completed_at = now();
                }

                $batch->status = $newStatus;
                $batch->save();
            });

            return response()->json($batch->fresh()->load('product'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

        // Mark a batch as done with actual output
    public function complete(Request $request, ProductionBatch $batch)
    {
        $request->validate([
            'actual_quantity' => 'required|integer|min:0',
            'wastage_quantity' => 'required|integer|min:0',
        ]);

        $batch->update([
            'status' => 'done',
            'actual_quantity' => $request->actual_quantity,
            'wastage_quantity' => $request->wastage_quantity,
            'completed_at' => now(),
        ]);

        return response()->json($batch->load('product'));
    }
}
