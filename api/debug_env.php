<?php
header('Content-Type: application/json');

$vars = [
    'TIDB_HOST' => getenv('TIDB_HOST') ? 'SET' : 'MISSING',
    'TIDB_PORT' => getenv('TIDB_PORT') ? 'SET' : 'MISSING',
    'TIDB_USER' => getenv('TIDB_USER') ? 'SET' : 'MISSING',
    'TIDB_DATABASE' => getenv('TIDB_DATABASE') ? 'SET' : 'MISSING',
    'TIDB_PASSWORD' => getenv('TIDB_PASSWORD') ? 'SET (Hidden)' : 'MISSING',
    'MYSQL_HOST' => getenv('MYSQL_HOST') ?: 'not set',
    'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
];

echo json_encode([
    "status" => "diagnostic",
    "environment" => $vars,
    "php_version" => PHP_VERSION,
    "message" => "Si les variables TIDB sont 'MISSING', vous devez les ajouter dans le tableau de bord Vercel (Settings -> Environment Variables) et REDÉPLOYER."
]);
