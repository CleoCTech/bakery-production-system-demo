<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// ---------------------------------------------------------------
// Your first API endpoint!
// When the Vue app calls: axios.get('/api/products')
// This is what responds.
//
// For now, we return hardcoded data. In Feature 3, this will
// come from the database via Eloquent models.
// ---------------------------------------------------------------

// Simple health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Bakery API is running',
        'timestamp' => now()->toISOString(),
    ]);
});

// CORS = Cross-Origin Resource Sharing
// This is handled in app/Http/Middleware/HandleCors.php and config/cors.php


// RESTful resource routes — one line creates all CRUD endpoints:
// GET    /api/products          → ProductController@index
// POST   /api/products          → ProductController@store
// GET    /api/products/{id}     → ProductController@show
// PUT    /api/products/{id}     → ProductController@update
// DELETE /api/products/{id}     → ProductController@destroy
// Route::apiResource('products', ProductController::class);


// Public routes (no token needed)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


// Protected routes (token required)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Only admins can create/update/delete products
    Route::apiResource('products', ProductController::class);
    Route::apiResource('batches', BatchController::class);

    Route::patch('/batches/{batch}/advance', [BatchController::class, 'advance']);
    Route::patch('/batches/{batch}/complete', [BatchController::class, 'complete']);

    // Add more protected routes here
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Read-only ingredient stock — needed by the dashboard for every role
    Route::get('/ingredients', [IngredientController::class, 'index']);
    Route::get('/ingredients/{ingredient}', [IngredientController::class, 'show']);
});



