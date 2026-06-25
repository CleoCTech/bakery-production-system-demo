<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    // GET /api/ingredients — list all ingredients (stock levels for the dashboard)
    public function index(Request $request)
    {
        $query = Ingredient::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Only ingredients at/below their reorder level (for reorder views)
        if ($request->boolean('low_stock')) {
            $query->whereColumn('current_stock', '<', 'reorder_level');
        }

        return response()->json($query->orderBy('name')->get());
    }

    // GET /api/ingredients/{id} — show one ingredient
    public function show(string $id)
    {
        $ingredient = Ingredient::findOrFail($id);
        return response()->json($ingredient);
    }
}
