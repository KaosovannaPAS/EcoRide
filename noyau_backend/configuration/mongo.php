<?php
// noyau_backend/configuration/mongo.php

require_once __DIR__ . '/../../vendor/autoload.php'; // If using Composer for MongoDB driver

// Fallback logic for when MongoDB native driver is used instead of the library
$mongoHost = getenv('MONGO_HOST') ?: 'mongo';
$mongoPort = getenv('MONGO_PORT') ?: '27017';

try {
    if (class_exists('MongoDB\Client')) {
        $mongoClient = new MongoDB\Client("mongodb://$mongoHost:$mongoPort");
        $mongoDb = $mongoClient->selectDatabase('ecoride');
    }
    else if (class_exists('MongoDB\Driver\Manager')) {
        $mongoManager = new MongoDB\Driver\Manager("mongodb://$mongoHost:$mongoPort");
    // Using standard driver manager directly
    }
    else {
        throw new Exception("No suitable MongoDB driver found.");
    }
}
catch (Exception $e) {
    error_log("MongoDB Connection Error: " . $e->getMessage());
    header('Content-Type: application/json', true, 500);
    echo json_encode(["status" => "error", "message" => "NoSQL Database connection failed"]);
    exit;
}
