<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    // ---------------------------------------------------------------
// MODEL: Sale — maps to the SALES table
//
// Records each sale of a finished product.
// Tracks: which product, how many, payment method, and total.
//
// SALES.product_id → PRODUCTS.id
// SALES.mpesa_ref is nullable — only required for M-Pesa payments
// ---------------------------------------------------------------

    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sold_at' => 'datetime',
    ];

    // A sale belongs to one product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Sale belongs to one user (the seller)
    public function user()
    {
        return $this->belongsTo(User::class);       
    }
}
