<?php

namespace App\Repositories;

use PDO;
use App\Models\Category;
use App\Contracts\CategoryRepositoryInterface;

class MySQLCategoryRepository implements CategoryRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM categories");

        $categories = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category(
                (int) $row['id'],
                $row['nama'],
                $row['deskripsi']
            );
        }

        return $categories;
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categories WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Category(
            (int) $row['id'],
            $row['nama'],
            $row['deskripsi']
        );
    }

    public function create(array $data): Category
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (nama, deskripsi)
             VALUES (:nama, :deskripsi)"
        );

        $stmt->execute([
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null
        ]);

        return $this->findById((int) $this->pdo->lastInsertId());
    }

    public function update(int $id, array $data): ?Category
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categories
             SET nama = :nama,
                 deskripsi = :deskripsi
             WHERE id = :id"
        );

        $stmt->execute([
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'id' => $id
        ]);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM categories WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}