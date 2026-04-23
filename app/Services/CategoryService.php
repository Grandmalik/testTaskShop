<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->categoryRepository->getAll();
    }
}
