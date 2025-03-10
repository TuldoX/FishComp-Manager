<?php
require __DIR__ . '/../vendor/autoload.php';

use App\api\Referee;

header("Content-Type: application/json");

$request_uri = $_SERVER['REQUEST_URI'];

// Log received request
error_log("Received request: " . $request_uri);

// Remove "/api/" prefix
$endpoint = str_replace("/api/", "", $request_uri);
error_log("Processed endpoint: " . $endpoint);

// Simple routing
switch ($endpoint) {
    case "referee/competitors":
        require_once __DIR__ . '/../src/api/Referee.php';
        $referee = new Referee();
        echo json_encode(["status" => "Route found", "data" => $referee->getCompetitors()]);
        break;
    default:
        error_log("Route not found: " . $endpoint);
        http_response_code(404);
        echo json_encode(["error" => "Not Found"]);
}
