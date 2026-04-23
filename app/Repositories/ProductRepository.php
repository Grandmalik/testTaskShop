<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly Product $model
    ) {}

    public function getPaginated(int $perPage = 10, ?int $categoryId = null): LengthAwarePaginator
    {
        return $this->model
            ->with('category')
            ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return $this->model->with('category')->find($id);
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->load('category');
    }

    public function delete(Product $product): void
    {
        $product->delete(); // SoftDelete
    }
}
