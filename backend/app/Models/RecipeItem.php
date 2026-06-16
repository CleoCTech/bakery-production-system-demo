<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeItem extends Model
{
    // The BOM join table — connects Products to Ingredients with quantities\

    protected $guarded = [];   // allow mass assignment for all fields (for simplicity)


    protected $casts = [
        'quantity_needed' => 'decimal:4',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
