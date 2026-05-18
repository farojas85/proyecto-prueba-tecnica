<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getPaginatedCategories(array $filters, int $perPage = 15)
    {
        $query = Category::query();
        
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'LIKE', '%' . $filters['search'] . '%');
            });
        }
        
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getAllCategories()
    {
        return Category::orderBy('created_at', 'desc')->get();
    }

    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    public function updateCategory(Category $category, array $data)
    {
        $category->update($data);
        return $category;
    }

    public function deleteCategory(Category $category)
    {
        if ($category->products()->exists()) {
            throw new \Exception('No se puede eliminar la categoría porque tiene productos asociados', 400);
        }
        return $category->delete();
    }
}
