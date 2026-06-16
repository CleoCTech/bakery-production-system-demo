<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // ---------------------------------------------------------------
    // MODEL: Category — maps to the CATEGORIES table
    //
    // A lookup table. Products belong to a category.
    // Keeping this as a table (not ENUM) means admins can
    // add new categories without changing code or migrations.
    // ---------------------------------------------------------------

    // protected $fillable = ['name', 'description'];
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
