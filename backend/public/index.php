<?php
header("Content-Type: application/json");

// Helper function to send JSON response
function sendResponse($status, $data = null, $message = "") {
    http_response_code($status);
    echo json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

// Parse the request URI
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Debugging: Log the request URI and method
error_log("Request URI: $requestUri");
error_log("Request Method: $method");

// Handle API routes
switch (true) {
    // Handle referee login
    case preg_match('/^\/auth\/referee$/', $requestUri) && $method === 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $code = $input['code'] ?? '';

        // Dummy validation
        if ($code === "validCode123") {
            sendResponse(200, [
                "id" => 1,
                "code" => "validCode123"
            ]);
        } else {
            sendResponse(404, null, "Neplatný kód.");
        }
        break;

    // Handle fetching competitors for a referee
    case preg_match('/^\/referees\/(\d+)\/competitors$/', $requestUri, $matches) && $method === 'GET':
        $refereeId = $matches[1];

        // Debugging: Log the referee ID
        error_log("Referee ID: $refereeId");

        // Dummy data for competitors
        $competitors = [
            [
                "id" => 1,
                "name" => "John Doe",
                "place" => 1,
                "points" => 100
            ],
            [
                "id" => 2,
                "name" => "Jane Smith",
                "place" => 2,
                "points" => 90
            ]
        ];

        sendResponse(200, $competitors);
        break;

    // Handle fetching catches for a competitor
    case preg_match('/^\/competitors\/(\d+)\/catches$/', $requestUri, $matches) && $method === 'GET':
        $competitorId = $matches[1];

        // Debugging: Log the competitor ID
        error_log("Competitor ID: $competitorId");

        // Dummy data for catches
        $catches = [
            [
                "id" => 1,
                "species" => "Trout",
                "points" => 50,
                "competitorId" => $competitorId
            ],
            [
                "id" => 2,
                "species" => "Salmon",
                "points" => 70,
                "competitorId" => $competitorId
            ],
            [
                "id" => 3,
                "species" => "Trout",
                "points" => 90,
                "competitorId" => $competitorId
            ],
            [
                "id" => 4,
                "species" => "Šťuka",
                "points" => 85,
                "competitorId" => $competitorId
            ]
        ];

        sendResponse(200, $catches);
        break;

    // Handle deleting a catch
    case preg_match('/^\/catches\/(\d+)$/', $requestUri, $matches) && $method === 'DELETE':
        $catchId = $matches[1];

        // Debugging: Log the catch ID
        error_log("Catch ID: $catchId");

        // Dummy success response
        sendResponse(200, ["id" => $catchId], "Catch deleted successfully.");
        break;

    // Handle fetching species list
    case preg_match('/^\/species$/', $requestUri) && $method === 'GET':
        // Dummy data for species
        $species = [
            [
                "id" => 1,
                "name" => "Trout"
            ],
            [
                "id" => 2,
                "name" => "Salmon"
            ],
            [
                "id" => 3,
                "name" => "Pike"
            ],
            [
                "id" => 4,
                "name" => "Carp"
            ]
        ];

        sendResponse(200, $species);
        break;

    // Handle posting a catch
    case preg_match('/^\/catches$/', $requestUri) && $method === 'POST':
        $input = json_decode(file_get_contents('php://input'), true);

        // Dummy validation
        if (isset($input['competitorId']) && isset($input['druh']) && isset($input['points'])) {
            // Simulate successful creation of a catch
            $newCatch = [
                "id" => rand(100, 999), // Random ID for the new catch
                "species" => $input['druh'],
                "points" => $input['points'],
                "competitorId" => $input['competitorId']
            ];

            sendResponse(200, $newCatch, "Catch added successfully.");
        } else {
            sendResponse(400, null, "Invalid input data.");
        }
        break;

    // Handle unknown routes
    default:
        sendResponse(404, null, "Endpoint not found.");
        break;
}