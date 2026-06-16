<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\RecipeItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BakerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Users (3 roles)
        User::create(['name' => 'Mama Njeri', 'email' => 'admin@bakery.co.ke', 'password' => bcrypt('password'), 'role' => 'admin']);
        User::create(['name' => 'James Oloo', 'email' => 'baker@bakery.co.ke', 'password' => bcrypt('password'), 'role' => 'baker']);
        User::create(['name' => 'Grace Wanjiku', 'email' => 'cashier@bakery.co.ke', 'password' => bcrypt('password'), 'role' => 'cashier']);

        // Categories (matches your ERD — separate entity, not ENUM)
        $breadCat = Category::create(['name' => 'bread', 'description' => 'Loaves and sliced bread']);
        $cakeCat = Category::create(['name' => 'cake', 'description' => 'Cakes and gateaux']);
        $pastryCat = Category::create(['name' => 'pastry', 'description' => 'Pastries and pies']);
        $bunCat = Category::create(['name' => 'bun', 'description' => 'Buns and mandazi']);

        // Products (same data as your Vue frontend! Now with category_id FK)
        $bread = Product::create(['name' => 'White Bread', 'category_id' => $breadCat->id, 'selling_price' => 60, 'unit' => 'loaf', 'shelf_life_hours' => 24]);
        $cake = Product::create(['name' => 'Chocolate Cake', 'category_id' => $cakeCat->id, 'selling_price' => 350, 'unit' => 'piece', 'shelf_life_hours' => 72]);
        $mandazi = Product::create(['name' => 'Mandazi', 'category_id' => $bunCat->id, 'selling_price' => 10, 'unit' => 'piece', 'shelf_life_hours' => 12]);
        $cinnamon = Product::create(['name' => 'Cinnamon Roll', 'category_id' => $pastryCat->id, 'selling_price' => 40, 'unit' => 'piece', 'shelf_life_hours' => 12]);

        // Ingredients
        $flour = Ingredient::create(['name' => 'Wheat Flour', 'unit' => 'kg', 'current_stock' => 50, 'reorder_level' => 20, 'cost_per_unit' => 100]);
        $yeast = Ingredient::create(['name' => 'Yeast', 'unit' => 'kg', 'current_stock' => 2, 'reorder_level' => 1, 'cost_per_unit' => 800]);
        $sugar = Ingredient::create(['name' => 'Sugar', 'unit' => 'kg', 'current_stock' => 30, 'reorder_level' => 10, 'cost_per_unit' => 150]);
        $cocoa = Ingredient::create(['name' => 'Cocoa Powder', 'unit' => 'kg', 'current_stock' => 5, 'reorder_level' => 3, 'cost_per_unit' => 600]);

        // Recipe Items (the BOM!) — White Bread recipe
        RecipeItem::create(['product_id' => $bread->id, 'ingredient_id' => $flour->id, 'quantity_needed' => 0.5]);
        RecipeItem::create(['product_id' => $bread->id, 'ingredient_id' => $yeast->id, 'quantity_needed' => 0.01]);
        RecipeItem::create(['product_id' => $bread->id, 'ingredient_id' => $sugar->id, 'quantity_needed' => 0.02]);

        // Chocolate Cake recipe
        RecipeItem::create(['product_id' => $cake->id, 'ingredient_id' => $flour->id, 'quantity_needed' => 0.3]);
        RecipeItem::create(['product_id' => $cake->id, 'ingredient_id' => $cocoa->id, 'quantity_needed' => 0.2]);
        RecipeItem::create(['product_id' => $cake->id, 'ingredient_id' => $sugar->id, 'quantity_needed' => 0.1]);
    }
}
