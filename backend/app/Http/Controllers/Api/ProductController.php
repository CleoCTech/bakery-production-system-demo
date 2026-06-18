<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    // GET /api/products — list all products
    public function index(Request $request)
    {
       // Eager-load category so each product includes its category name
       //$products = Product::all();
        $query = Product::with('category');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category_id (FK) instead of string matching
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return response()->json($query->get());
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
