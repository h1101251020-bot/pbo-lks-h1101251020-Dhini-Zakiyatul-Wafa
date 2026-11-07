<?php

namespace App\Core;

class Request
{
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return parse_url($uri, PHP_URL_PATH) ?: '/';
    }

    public function input(): array
    {
        $content = file_get_contents('php://input');

        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }
}