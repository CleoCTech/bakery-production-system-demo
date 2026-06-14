<?php

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


// RESTful resource routes — one line creates all CRUD endpoints:
// GET    /api/products          → ProductController@index
// POST   /api/products          → ProductController@store
// GET    /api/products/{id}     → ProductController@show
// PUT    /api/products/{id}     → ProductController@update
// DELETE /api/products/{id}     → ProductController@destroy
Route::apiResource('products', ProductController::class);


