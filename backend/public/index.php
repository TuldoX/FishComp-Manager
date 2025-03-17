<?php

$code = null;

// Accessing POST data (for form or JSON)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
        // Reading raw JSON from the request body
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $code = $data['code'] ?? null;
    } else {
        // Form data sent via POST
        $code = $_POST['code'] ?? null;
    }
}

if ($code === "123ab") {
    $response = [
        'id' => '1',
        'code' => '123ab'
    ];
    echo json_encode($response);
}

else {
    $response = [];
    http_response_code(404);
    echo json_encode($response);
}