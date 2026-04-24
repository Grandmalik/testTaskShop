<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function getPaginated(int $perPage = 10, ?int $categoryId = null, ?string $search = null): LengthAwarePaginator
    {
        return $this->productRepository->getPaginated($perPage, $categoryId, $search);
    }

    public function findOrFail(int $id): Product
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            abort(404, 'Товар не найден');
        }

        return $product;
    }

    public function create(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->findOrFail($id);
        return $this->productRepository->update($product, $data);
    }

    public function delete(int $id): void
    {
        $product = $this->findOrFail($id);
        $this->productRepository->delete($product);
    }
}
