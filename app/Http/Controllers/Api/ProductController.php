<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    public function index(Request $request): ProductCollection
    {
        $products = $this->productService->getPaginated(
            perPage: 10,
            categoryId: $request->integer('category_id') ?: null
        );

        return new ProductCollection($products);
    }

    public function show(int $id): ProductResource
    {
        $product = $this->productService->findOrFail($id);

        return new ProductResource($product);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return (new ProductResource($product->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateProductRequest $request, int $id): ProductResource
    {
        $product = $this->productService->update($id, $request->validated());

        return new ProductResource($product);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);

        return response()->json(['message' => 'Товар удалён'], 200);
    }
}
