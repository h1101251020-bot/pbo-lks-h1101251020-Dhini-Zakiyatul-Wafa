<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Services\CategoryService;

class CategoryController
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    // Menampilkan semua kategori
    public function index(): void
    {
        $categories = $this->service->list();

        Response::json([
            'success' => true,
            'message' => 'Daftar kategori',
            'data' => $categories
        ]);
    }

    // Menampilkan satu kategori
    public function show(int $id): void
    {
        $category = $this->service->find($id);

        if (!$category) {
            Response::json([
                'success' => false,
                'message' => "Kategori dengan ID $id tidak ditemukan"
            ], 404);

            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Detail kategori',
            'data' => $category
        ]);
    }

    // Menambahkan kategori
    public function store(Request $request): void
    {
        $data = $request->input();

        if (empty(trim($data['nama'] ?? ''))) {
            Response::json([
                'success' => false,
                'message' => 'Nama kategori wajib diisi'
            ], 422);

            return;
        }

        $category = $this->service->create($data);

        Response::json([
            'success' => true,
            'message' => 'Kategori berhasil dibuat',
            'data' => $category
        ], 201);
    }

    // Memperbarui kategori
    public function update(int $id, Request $request): void
    {
        $data = $request->input();

        if (!$this->service->find($id)) {
            Response::json([
                'success' => false,
                'message' => "Kategori dengan ID $id tidak ditemukan"
            ], 404);

            return;
        }

        if (empty(trim($data['nama'] ?? ''))) {
            Response::json([
                'success' => false,
                'message' => 'Nama kategori wajib diisi'
            ], 422);

            return;
        }

        $category = $this->service->update($id, $data);

        Response::json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui',
            'data' => $category
        ]);
    }

    // Menghapus kategori
    public function destroy(int $id): void
    {
        if (!$this->service->delete($id)) {
            Response::json([
                'success' => false,
                'message' => "Kategori dengan ID $id tidak ditemukan"
            ], 404);

            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}