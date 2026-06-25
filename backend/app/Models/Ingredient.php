<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    
    // ---------------------------------------------------------------
    // MODEL: Ingredient — maps to the INGREDIENTS table
    //
    // Eloquent maps automatically:
    //   Ingredient::all()           → SELECT * FROM ingredients
    //   Ingredient::find(1)         → SELECT * FROM ingredients WHERE id = 1
    //   Ingredient::whereHas('products', ...)->get()  → ... WHERE product_id IN (SELECT ...)
    //
    // Relationships define how tables connect (mirrors the ERD):
    //   $ingredient->recipeItems     → RECIPE_ITEMS where ingredient_id = this.id
    //   $ingredient->products        → PRODUCTS via RECIPE_ITEMS (M:M with pivot)
    // ---------------------------------------------------------------

    protected $guarded = [];   // allow mass assignment for all fields (for simplicity)

    // Type casting: ensures PHP receives correct types
    // Cast the real decimal columns to float so they serialize as JSON numbers
    // (not strings), keeping the frontend's numeric stock logic sound.
    protected $casts = [
        'current_stock' => 'float',
        'reorder_level' => 'float',
        'cost_per_unit' => 'float',
    ];

    // ==================== RELATIONSHIPS ====================

    // One ingredient has MANY recipe items (1:M)
    // Ingredient → RECIPE_ITEMS (FK: recipe_items.ingredient_id)
    public function recipeItems()
    {
        return $this->hasMany(RecipeItem::class);
    }

    // Many-to-many: ingredient is used in many products, via recipe_items
    // INGREDIENTS M ←→ M PRODUCTS through RECIPE_ITEMS
    public function products()
    {
        return $this->belongsToMany(Product::class, 'recipe_items')
                    ->withPivot('quantity_needed', 'notes')
                    ->withTimestamps();
    }

    // Helper: check if stock is below reorder level
    // This is the same logic as your useStockStatus() composable!
    public function isLowStock(): bool
    {
        return $this->current_stock < $this->reorder_level;
    }
}
