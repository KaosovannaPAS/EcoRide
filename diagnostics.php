<?php
// diagnostics.php
require_once 'noyau_backend/configuration/db.php';
require_once 'noyau_backend/controllers/TripController.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $controller = new TripController($pdo);
    echo "Controller initialized.\n";

    $filters = [
        'departure_city' => null,
        'destination_city' => null,
        'departure_date' => null
    ];

    $res = $controller->search($filters);
    echo "Search successful. Count: " . count($res['trips']) . "\n";
    print_r($res['trips']);
}
catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
