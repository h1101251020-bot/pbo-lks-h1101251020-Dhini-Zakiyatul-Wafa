<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Contracts\BookRepositoryInterface;

class BookController
{
    private BookRepositoryInterface $repository;

    public function __construct(BookRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    // Menampilkan semua buku
    public function index(): void
    {
        $books = $this->repository->findAll();

        $data = array_map(
            fn($book) => $book->toArray(),
            $books
        );

        Response::json([
            'success' => true,
            'message' => 'Daftar buku',
            'data' => $data
        ]);
    }

    // Menampilkan satu buku
    public function show(int $id): void
    {
        $book = $this->repository->findById($id);

        if (!$book) {
            Response::json([
                'success' => false,
                'message' => "Buku dengan ID $id tidak ditemukan"
            ], 404);

            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Detail buku',
            'data' => $book->toArray()
        ]);
    }

    // Menambahkan buku
    public function store(Request $request): void
    {
        $data = $request->input();

        if (empty(trim($data['judul'] ?? ''))) {
            Response::json([
                'success' => false,
                'message' => 'Judul wajib diisi'
            ], 422);

            return;
        }

        $book = $this->repository->create($data);

        Response::json([
            'success' => true,
            'message' => 'Buku berhasil dibuat',
            'data' => $book->toArray()
        ], 201);
    }

    // Memperbarui buku
    public function update(int $id, Request $request): void
    {
        $data = $request->input();

        if (!$this->repository->findById($id)) {
            Response::json([
                'success' => false,
                'message' => "Buku dengan ID $id tidak ditemukan"
            ], 404);

            return;
        }

        $book = $this->repository->update($id, $data);

        Response::json([
            'success' => true,
            'message' => 'Buku berhasil diperbarui',
            'data' => $book->toArray()
        ]);
    }

    // Menghapus buku
    public function destroy(int $id): void
    {
        if (!$this->repository->delete($id)) {
            Response::json([
                'success' => false,
                'message' => "Buku dengan ID $id tidak ditemukan"
            ], 404);

            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Buku berhasil dihapus'
        ]);
    }
}