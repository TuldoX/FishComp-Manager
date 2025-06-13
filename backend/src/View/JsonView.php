<?php
namespace App\View;

class JsonView
{
    public function render(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);

        header("Access-Control-Allow-Origin: http://localhost");
        header('Content-Type: application/json');

        echo json_encode($data, JSON_PRETTY_PRINT);
    }
}