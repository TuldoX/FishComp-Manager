<?php
header("Content-Type: application/json");

// Simulating a database response
$users = [
    1 => ["id" => 1, "name" => "Alice"],
    2 => ["id" => 2, "name" => "Bob"]
];

$id = $_GET['id'] ?? null;

if ($id && isset($users[$id])) {
    echo json_encode($users[$id]);
} else {
    echo json_encode(["error" => "User not found"]);
}
?>
