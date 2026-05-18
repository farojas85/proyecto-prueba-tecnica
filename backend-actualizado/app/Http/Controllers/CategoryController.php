<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 10);
        $filters = [
            'search' => $request->get('search')
        ];
        
        $categories = $this->categoryService->getPaginatedCategories($filters, $perPage);
        return response()->json(CategoryResource::collection($categories)->response()->getData(true));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        Log::info('Category created', ['category_id' => $category->id]);

        return response()->json(new CategoryResource($category), 201);
    }

    public function show($id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'No category'], 404);
        }

        $updatedCategory = $this->categoryService->updateCategory($category, $request->validated());
        Log::info('Category updated', ['category_id' => $updatedCategory->id]);

        return response()->json(['updated' => true, 'category' => new CategoryResource($updatedCategory)]);
    }

    public function destroy($id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'not found'], 404);
        }

        try {
            $this->categoryService->deleteCategory($category);
            Log::info('Category deleted', ['category_id' => $id]);
            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
