<?php

// Konfigurasi CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../vendor/autoload.php';

use App\Config\Env;
use App\Database\Connection;
use App\Core\Container;
use App\Core\Router;
use App\Core\Request;
use App\Controllers\BookController;
use App\Contracts\BookRepositoryInterface;
use App\Repositories\MySQLBookRepository;
use App\Controllers\CategoryController;
use App\Contracts\CategoryRepositoryInterface;
use App\Repositories\MySQLCategoryRepository;
use App\Services\CategoryService;

// Memuat konfigurasi
Env::load(__DIR__ . '/../.env');

// Membuat Dependency Injection Container
$container = new Container();

// Binding repository
$container->bind(
    BookRepositoryInterface::class,
    function ($container) {
        return new MySQLBookRepository(Connection::get());
    }
);

// Membuat controller
$container->bind(
    BookController::class,
    function ($container) {
        return new BookController(
            $container->make(BookRepositoryInterface::class)
        );
    }
);

// Binding repository kategori
$container->bind(
    CategoryRepositoryInterface::class,
    function ($container) {
        return new MySQLCategoryRepository(Connection::get());
    }
);

// Binding service kategori
$container->bind(
    CategoryService::class,
    function ($container) {
        return new CategoryService(
            $container->make(CategoryRepositoryInterface::class)
        );
    }
);

// Binding controller kategori
$container->bind(
    CategoryController::class,
    function ($container) {
        return new CategoryController(
            $container->make(CategoryService::class)
        );
    }
);

// Membuat router
$router = new Router();

// Route daftar buku
$router->get('/api/books', function () use ($container) {
    $container->make(BookController::class)->index();
});

// Route detail buku
$router->get('/api/books/{id}', function ($id) use ($container) {
    $container->make(BookController::class)->show((int) $id);
});

// Route tambah buku
$router->post('/api/books', function () use ($container) {
    $container->make(BookController::class)->store(new Request());
});

// Route ubah buku
$router->put('/api/books/{id}', function ($id) use ($container) {
    $container->make(BookController::class)->update((int) $id, new Request());
});

// Route hapus buku
$router->delete('/api/books/{id}', function ($id) use ($container) {
    $container->make(BookController::class)->destroy((int) $id);
});

// Route daftar kategori
$router->get('/api/categories', function () use ($container) {
    $container->make(CategoryController::class)->index();
});

// Route detail kategori
$router->get('/api/categories/{id}', function ($id) use ($container) {
    $container->make(CategoryController::class)->show((int) $id);
});

// Route tambah kategori
$router->post('/api/categories', function () use ($container) {
    $container->make(CategoryController::class)->store(new Request());
});

// Route ubah kategori
$router->put('/api/categories/{id}', function ($id) use ($container) {
    $container->make(CategoryController::class)->update((int) $id, new Request());
});

// Route hapus kategori
$router->delete('/api/categories/{id}', function ($id) use ($container) {
    $container->make(CategoryController::class)->destroy((int) $id);
});
// Menjalankan router
$router->dispatch(new Request());