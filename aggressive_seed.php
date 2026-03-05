<?php
// aggressive_seed.php
$ports = ['3306', '3307'];
$logFile = 'seed_debug.log';

function logMsg($msg)
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $msg\n", FILE_APPEND);
    echo $msg . "\n";
}

logMsg("Starting aggressive seed...");

foreach ($ports as $port) {
    logMsg("Trying port $port...");
    try {
        $host = '127.0.0.1';
        $db = 'ecoride';
        $user = 'user'; // Try Docker user first
        $pass = 'password';
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        logMsg("SUCCESS: Connected to port $port with user 'user'");
    }
    catch (Exception $e1) {
        logMsg("FAILED: Port $port with user 'user': " . $e1->getMessage());
        try {
            $user = 'root';
            $pass = '';
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            logMsg("SUCCESS: Connected to port $port with user 'root'");
        }
        catch (Exception $e2) {
            logMsg("FAILED: Port $port with user 'root': " . $e2->getMessage());
            continue;
        }
    }

    // If we are here, we have a connection
    try {
        logMsg("Seeding data on port $port...");
        // Ensure schema exists (just in case)
        $schema = file_get_contents('noyau_backend/configuration/schema.sql');
        // Remove 'CREATE DATABASE' lines to avoid errors if already in DB
        $schema = preg_replace('/CREATE DATABASE.*?;/is', '', $schema);
        $schema = preg_replace('/USE.*?;/is', '', $schema);
        $pdo->exec($schema);
        logMsg("Schema verified.");

        // Clean up
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("TRUNCATE TABLE reservations;");
        $pdo->exec("TRUNCATE TABLE incidents;");
        $pdo->exec("TRUNCATE TABLE trips;");
        $pdo->exec("TRUNCATE TABLE vehicles;");
        $pdo->exec("TRUNCATE TABLE users;");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // Seed
        $pdo->exec("INSERT INTO users (id, pseudo, email, password_hash, role, credits) VALUES 
            (1, 'JeanP', 'jean@example.com', 'hash', 'chauffeur', 100),
            (2, 'MarieD', 'marie@example.com', 'hash', 'chauffeur', 50)");

        $pdo->exec("INSERT INTO vehicles (id, user_id, registration, model, color, is_electric) VALUES 
            (1, 1, 'AA-001-BB', 'Tesla Model 3', 'Blanc', 1),
            (2, 2, 'CC-002-DD', 'Renault Zoe', 'Bleu', 1)");

        $pdo->exec("INSERT INTO trips (driver_id, vehicle_id, departure_city, destination_city, departure_date, departure_time, price, max_duration, max_seats, status) VALUES 
            (1, 1, 'Paris', 'Lyon', '2026-03-10', '08:00:00', 25, 270, 3, 'planned'),
            (2, 2, 'Bordeaux', 'Toulouse', '2026-03-12', '10:30:00', 15, 150, 4, 'planned'),
            (1, 1, 'Lille', 'Paris', '2026-03-16', '07:00:00', 20, 180, 3, 'planned')");

        logMsg("Seeding COMPLETED on port $port");
        break; // Stop after first success
    }
    catch (Exception $e3) {
        logMsg("ERROR during seeding on port $port: " . $e3->getMessage());
    }
}

logMsg("Aggressive seed finished.");
?>
