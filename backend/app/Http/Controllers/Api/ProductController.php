<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    // GET /api/products — list all products
    public function index()
    {
        // For now: hardcoded. Feature 3: Product::all()
        $products = [
            [
                'id' => 1,
                'name' => 'White Bread',
                'category' => 'bread',
                'selling_price' => 60,
                'shelf_life_hours' => 24,
                'unit' => 'loaf',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Chocolate Cake',
                'category' => 'cake',
                'selling_price' => 350,
                'shelf_life_hours' => 72,
                'unit' => 'piece',
                'is_active' => true,
            ],
        ];

        return response()->json($products);
    }
    // GET /api/products/{id} — show one product
    public function show($id)
    {
        // Feature 3: Product::findOrFail($id)
        return response()->json([
            'id' => $id,
            'message' => 'Product detail — will come from database in Feature 3',
        ]);
    }

    // POST /api/products — create a product
    public function store(Request $request)
    {
        //Log::alert('Received request to create product: ' . json_encode($request->all()));
        info('Received request to create product: ' . json_encode($request->all()));
        // Feature 3: Product::create($request->validated())
        return response()->json([
            'message' => 'Product created' . ' — ID:' .$request->input('id'),
            'data' => $request->all(),
        ], 201);
    }

}
