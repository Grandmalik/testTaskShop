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

    // 200 OK — список
    public function index(Request $request): ProductCollection
    {
        $perPage = $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 12, 15]) ? $perPage : 10;

        $products = $this->productService->getPaginated(
            perPage:    $perPage,
            categoryId: $request->integer('category_id') ?: null,
            search:     $request->string('search')->trim()->value() ?: null,
        );

        return new ProductCollection($products);
    }

    // 200 OK — один товар
    public function show(int $id): ProductResource
    {
        $product = $this->productService->findOrFail($id);

        return new ProductResource($product);
    }

    // 201 Created — создание
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return (new ProductResource($product->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    // 200 OK — обновление
    public function update(UpdateProductRequest $request, int $id): ProductResource
    {
        $product = $this->productService->update($id, $request->validated());

        return new ProductResource($product);
    }

    // 204 — удаление
    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);

        return response()->json(null, 204);
    }
}
