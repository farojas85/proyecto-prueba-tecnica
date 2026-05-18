<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Category extends Model
{
    use Auditable;

    protected $fillable = ['name', 'description', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
