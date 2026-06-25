<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    protected $guarded = [];

    protected $casts = [
        'planned_quantity' => 'integer',
        'actual_quantity' => 'integer',
        'wastage_quantity' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // A production batch belongs to one product (M:1)
    // PRODUCTION_BATCHES → PRODUCTS (FK: production_batches.product_id)
    public function product()
    {
        return $this->belongsTo(Product::class);       
    }

    // A batch was created by one user (M:1)
    // PRODUCTION_BATCHES → USERS (FK: production_batches.user_id)
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // HELPER METHODS

    // calculate yield rate: (actual / planned) * 100
    public function yieldRate(): ?int
    {
        if ($this->planned_quantity === 0) {
            return 0;
        }

        return ($this->actual_quantity / $this->planned_quantity) * 100;
    }

    public function isLowYield(): bool
    {
        $yieldRate = $this->yieldRate();
        return $yieldRate < 80; // Example threshold for low yield
    }

    public function durationMinutes(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->completed_at->diffInMinutes($this->started_at);
        }
        return null;
    }

}
