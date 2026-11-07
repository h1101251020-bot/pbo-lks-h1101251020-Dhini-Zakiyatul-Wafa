<?php

namespace App\Services;

use App\Models\Category;
use App\Contracts\CategoryRepositoryInterface;

class CategoryService
{
    private CategoryRepositoryInterface $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function list(): array
    {
        $categories = $this->repository->findAll();

        return array_map(
            fn(Category $category) => $category->toArray(),
            $categories
        );
    }

    public function find(int $id): ?array
    {
        $category = $this->repository->findById($id);

        return $category?->toArray();
    }

    public function create(array $data): array
    {
        if (empty(trim($data['nama'] ?? ''))) {
            throw new \Exception('Nama kategori wajib diisi');
        }

        $category = $this->repository->create($data);

        return $category->toArray();
    }

    public function update(int $id, array $data): ?array
    {
        if (empty(trim($data['nama'] ?? ''))) {
            throw new \Exception('Nama kategori wajib diisi');
        }

        $category = $this->repository->update($id, $data);

        return $category?->toArray();
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}