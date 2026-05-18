<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StockMovementResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        
        // Eloquent Paginator is automatically resolved by Laravel, but we can map it via Resource Collection
        $products = $this->productService->getPaginatedProducts([
            'search' => $request->get('q') ?? $request->get('search'), // Fallback for old 'q'
            'category_id' => $request->get('category_id'),
            'status' => $request->get('status'),
        ], $perPage);

        return response()->json(ProductResource::collection($products)->response()->getData(true));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());
        Log::info('Product created', ['product_id' => $product->id, 'payload' => $request->all()]);

        return response()->json(['ok' => true, 'product' => new ProductResource($product)], 201);
    }

    public function show($id): JsonResponse
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json(['data' => new ProductResource($product)]);
    }

    public function update(UpdateProductRequest $request, $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['msg' => 'No existe'], 404);
        }

        $updatedProduct = $this->productService->updateProduct($product, $request->validated());
        Log::info('Product updated', ['product_id' => $updatedProduct->id, 'payload' => $request->all()]);

        return response()->json(['success' => true, 'data' => new ProductResource($updatedProduct)]);
    }

    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $this->productService->deleteProduct($product);
        Log::info('Product deleted', ['product_id' => $id]);

        return response()->json(['deleted' => true]);
    }

    public function stockMovements(Request $request, $id)
    {
        $perPage = (int) $request->get('per_page', 10);
        $movements = $this->productService->getStockMovements($id, $perPage);
        return response()->json(StockMovementResource::collection($movements)->response()->getData(true));
    }

    public function storeStockMovement(StoreStockMovementRequest $request, $id): JsonResponse
    {
        try {
            $result = $this->productService->registerStockMovement(
                $id, 
                $request->validated(), 
                $request->auth_user_id
            );

            Log::info('Stock movement registered', ['movement_id' => $result['movement']->id]);

            return response()->json([
                'message' => 'Stock updated',
                'product' => new ProductResource($result['product']),
                'movement' => new StockMovementResource($result['movement']),
            ]);

        } catch (\Exception $e) {
            $statusCode = $e->getCode() == 404 ? 404 : 422;
            return response()->json(['message' => $e->getMessage()], $statusCode);
        }
    }
}
