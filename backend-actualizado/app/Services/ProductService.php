<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductService
{
    public function getPaginatedProducts(array $filters, int $perPage = 15)
    {
        $query = Product::with('category')->withCount('stockMovements');

        if (!empty($filters['search'])) {
            $query->where('name', 'LIKE', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createProduct(array $data)
    {
        return Product::create($data);
    }

    public function updateProduct(Product $product, array $data)
    {
        $product->update($data);
        return $product;
    }

    public function deleteProduct(Product $product)
    {
        return $product->delete();
    }

    public function registerStockMovement(int $productId, array $data, int $userId)
    {
        return DB::transaction(function () use ($productId, $data, $userId) {
            $product = Product::where('id', $productId)->lockForUpdate()->first();

            if (!$product) {
                throw new Exception('Product not found', 404);
            }

            if ($data['type'] == 'salida') {
                if ($product->stock < $data['quantity']) {
                    throw new Exception('Stock insuficiente', 422);
                }
                $product->decrement('stock', $data['quantity']);
            } else {
                $product->increment('stock', $data['quantity']);
            }

            $movement = StockMovement::create([
                'product_id' => $product->id,
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'reason' => $data['reason'],
                'user_id' => $userId,
            ]);

            return [
                'product' => $product->fresh(),
                'movement' => $movement,
            ];
        });
    }

    public function getStockMovements(int $productId, int $perPage = 20)
    {
        return StockMovement::where('product_id', $productId)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }
}
