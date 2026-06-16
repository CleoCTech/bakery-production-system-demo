<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    
    // ---------------------------------------------------------------
    // MODEL: Product — maps to the PRODUCTS table
    //
    // Eloquent maps automatically:
    //   Product::all()              → SELECT * FROM products
    //   Product::find(1)            → SELECT * FROM products WHERE id = 1
    //   Product::whereHas('category', ...)->get()  → ... WHERE category_id IN (SELECT ...)
    //
    // Relationships define how tables connect (mirrors the ERD):
    //   $product->recipeItems       → RECIPE_ITEMS where product_id = this.id
    //   $product->ingredients       → INGREDIENTS via RECIPE_ITEMS (M:M with pivot)
    //   $product->batches           → PRODUCTION_BATCHES where product_id = this.id
    // ---------------------------------------------------------------

    protected $guarded = [];   // allow mass assignment for all fields (for simplicity)

    // Type casting: ensures PHP receives correct types
    protected $casts = [
        'selling_price' => 'decimal:2',
        'shelf_life_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    // A product belongs to one category (M:1)
    // Product → CATEGORIES (FK: products.category_id)

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // One product has MANY recipe items (1:M)
    // Product → RECIPE_ITEMS (FK: recipe_items.product_id)
    public function recipeItems()
    {
        return $this->hasMany(RecipeItem::class);
    }

    // Many-to-many: product uses many ingredients, via recipe_items
    // PRODUCTS M ←→ M INGREDIENTS through RECIPE_ITEMS
    // The pivot table holds quantity_needed (extra data on the relationship)
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_items')
                    ->withPivot('quantity_needed', 'notes')
                    ->withTimestamps();
    }

    // One product has many production batches
    public function batches()
    {
        return $this->hasMany(ProductionBatch::class);
    }

    // One product has many sales
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }


}
