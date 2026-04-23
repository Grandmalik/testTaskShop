<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Category;
}
