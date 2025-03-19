<?php
header("Content-Type: application/json");

// Sample competitors data
$competitors = [
    ["id" => "1a", "refId" => 2, "place" => 1, "name" => "Ján Šedivý", "points" => 0],
    ["id" => "2b", "refId" => 2, "place" => 2, "name" => "František Biely", "points" => 0],
    ["id" => "3c", "refId" => 2, "place" => 3, "name" => "Karol Fiala", "points" => 0],
    ["id" => "4d", "refId" => 2, "place" => 4, "name" => "Joe Smith", "points" => 0],
    ["id" => "5e", "refId" => 2, "place" => 5, "name" => "Andrew Johnson", "points" => 0],
    ["id" => "6d", "refId" => 2, "place" => 6, "name" => "Elwis Davis", "points" => 0],
    ["id" => "7f", "refId" => 2, "place" => 7, "name" => "Lucas Martin", "points" => 0],
    ["id" => "8g", "refId" => 2, "place" => 8, "name" => "Christian Taylor", "points" => 0]
];

// Get the request URI and split it into parts
$requestUri = $_SERVER['REQUEST_URI'];
$segments = explode('/', trim($requestUri, '/'));

// Check if the URL follows the expected pattern: referees/{referee_id}/competitors

    echo json_encode(array_values($competitors));
