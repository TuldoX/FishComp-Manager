<?php

namespace App\api;

class Referee {
    // Example attributes
    public $name = "John Doe";
    public $id = 1;

    // Simulate a list of competitors
    public function getCompetitors(){
        // Example array of competitors (could come from a database, etc.)
        $competitors = [
            ["id" => 1, "name" => "Alice", "age" => 30],
            ["id" => 2, "name" => "Bob", "age" => 25],
            ["id" => 3, "name" => "Charlie", "age" => 28],
        ];
        
        return $competitors; // Return an array of competitors
    }
}