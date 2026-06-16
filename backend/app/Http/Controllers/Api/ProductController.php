<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    // GET /api/products — list all products
    public function index()
    {
        $products= Product::all();
        return response()->json($products);
    }
    // GET /api/products/{id} — show one product
    public function show($id)
    {
        $product= Product::findOrFail($id);
        return response()->json($product);
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
