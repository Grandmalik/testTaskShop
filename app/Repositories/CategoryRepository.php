<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private readonly Category $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    public function findById(int $id): ?Category
    {
        return $this->model->find($id);
    }
}
