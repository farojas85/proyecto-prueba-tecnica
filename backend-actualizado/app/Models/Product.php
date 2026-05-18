<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Product extends Model
{
    use Auditable;

    protected $fillable = ['name', 'description', 'price', 'stock', 'category_id', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
