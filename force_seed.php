<?php
// force_seed.php
header('Content-Type: text/plain; charset=UTF-8');
require_once 'noyau_backend/configuration/db.php';

echo "Starting Force Seed...\n";

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE reservations;");
    $pdo->exec("TRUNCATE TABLE incidents;");
    $pdo->exec("TRUNCATE TABLE trips;");
    $pdo->exec("TRUNCATE TABLE vehicles;");
    $pdo->exec("TRUNCATE TABLE users;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "Tables truncated.\n";

    // Insert Users
    $stmt = $pdo->prepare("INSERT INTO users (pseudo, email, password_hash, role, credits) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['EcoPilot', 'test.driver@ecoride.fr', password_hash('password123', PASSWORD_BCRYPT), 'chauffeur', 100]);
    $driver1_id = $pdo->lastInsertId();

    $stmt->execute(['MarieEco', 'marie@ecoride.fr', password_hash('password123', PASSWORD_BCRYPT), 'chauffeur', 100]);
    $driver2_id = $pdo->lastInsertId();
    echo "Users created (IDs: $driver1_id, $driver2_id).\n";

    // Insert Vehicles
    $stmt = $pdo->prepare("INSERT INTO vehicles (user_id, registration, model, color, is_electric) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$driver1_id, 'AB-123-CD', 'Tesla Model 3', 'Blanc', 1]);
    $v1_id = $pdo->lastInsertId();

    $stmt->execute([$driver2_id, 'EF-456-GH', 'Renault Zoe', 'Bleu', 1]);
    $v2_id = $pdo->lastInsertId();
    echo "Vehicles created (IDs: $v1_id, $v2_id).\n";

    // Insert Trips
    $stmt = $pdo->prepare("INSERT INTO trips (driver_id, vehicle_id, departure_city, destination_city, departure_date, departure_time, price, max_duration, max_seats, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'planned')");

    $trips = [
        [$driver1_id, $v1_id, 'Paris', 'Lyon', date('Y-m-d', strtotime('+3 days')), '08:00', 25, 270, 3],
        [$driver2_id, $v2_id, 'Bordeaux', 'Toulouse', date('Y-m-d', strtotime('+4 days')), '10:30', 15, 150, 4],
        [$driver1_id, $v1_id, 'Lille', 'Paris', date('Y-m-d', strtotime('+5 days')), '07:00', 20, 180, 3],
        [$driver1_id, $v1_id, 'Nantes', 'Angers', date('Y-m-d', strtotime('+6 days')), '09:00', 8, 60, 2]
    ];

    foreach ($trips as $t) {
        $stmt->execute($t);
    }
    echo "Trips created (" . count($trips) . " trips).\n";

    echo "\nSUCCESS: Database seeded and ready.\n";

}
catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
}
?>
