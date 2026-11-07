<?php

namespace App\Services;

use App\Models\Book;
use App\Contracts\BookRepositoryInterface;

class BookService
{
    private BookRepositoryInterface $repository;

    public function __construct(BookRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function list(): array
    {
        $books = $this->repository->findAll();

        return array_map(
            fn(Book $book) => $book->toArray(),
            $books
        );
    }

    public function create(array $data): array
    {
        if (empty(trim($data['judul'] ?? ''))) {
            throw new \Exception('Judul wajib diisi');
        }

        if (
            empty(trim($data['penulis'] ?? '')) ||
            !isset($data['tahun']) ||
            !isset($data['stok'])
        ) {
            throw new \Exception('Data buku belum lengkap');
        }

        $book = $this->repository->create($data);

        return $book->toArray();
    }
}