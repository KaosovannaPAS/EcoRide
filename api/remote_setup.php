<?php
// api/remote_setup.php
header('Content-Type: text/plain');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . '/../noyau_backend/configuration/db.php';

echo "--- EcoRide Remote Setup ---\n";

try {
    // 1. Check current tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Existing tables: " . implode(", ", $tables) . "\n";

    // 2. Create tables if missing
    echo "Cleining up old schema...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS trips, vehicles, users, reservations, incidents, dishes, menus, order_items");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Old tables dropped.\n";

    echo "Initializing new schema...\n";

    $queries = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pseudo VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('passager', 'chauffeur', 'employe', 'admin', 'suspended') DEFAULT 'passager',
            credits INT DEFAULT 20,
            photo VARCHAR(255) DEFAULT NULL,
            bio TEXT DEFAULT NULL,
            pref_smoking BOOLEAN DEFAULT FALSE,
            pref_animals BOOLEAN DEFAULT FALSE,
            pref_music BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS vehicles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            registration VARCHAR(20) NOT NULL UNIQUE,
            model VARCHAR(100) NOT NULL,
            color VARCHAR(30) NOT NULL,
            is_electric BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS trips (
            id INT AUTO_INCREMENT PRIMARY KEY,
            driver_id INT NOT NULL,
            vehicle_id INT NOT NULL,
            departure_city VARCHAR(100) NOT NULL,
            destination_city VARCHAR(100) NOT NULL,
            departure_date DATE NOT NULL,
            departure_time TIME NOT NULL,
            price INT NOT NULL,
            max_duration INT NOT NULL,
            max_seats INT NOT NULL,
            status ENUM('planned', 'started', 'finished', 'cancelled') DEFAULT 'planned',
            is_eco BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
        )"
    ];

    foreach ($queries as $q) {
        $pdo->exec($q);
        echo "Successfully checked/created table.\n";
    }

    // 3. Seed some data if users table is empty
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count == 0) {
        echo "Seeding initial data...\n";

        // Insert a driver
        $pdo->exec("INSERT INTO users (pseudo, email, password_hash, role, credits) 
                   VALUES ('EcoDriver', 'driver@ecoride.fr', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'chauffeur', 100)");
        $driver_id = $pdo->lastInsertId();

        // Insert a vehicle
        $pdo->exec("INSERT INTO vehicles (user_id, registration, model, color, is_electric) 
                   VALUES ($driver_id, 'ECO-2024-FR', 'Tesla Model 3', 'Blanc', 1)");
        $vehicle_id = $pdo->lastInsertId();

        // Insert some trips
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $dayAfter = date('Y-m-d', strtotime('+2 days'));

        $pdo->exec("INSERT INTO trips (driver_id, vehicle_id, departure_city, destination_city, departure_date, departure_time, price, max_duration, max_seats, status, is_eco) 
                   VALUES ($driver_id, $vehicle_id, 'Paris', 'Lyon', '$tomorrow', '08:00:00', 15, 240, 3, 'planned', 1)");

        $pdo->exec("INSERT INTO trips (driver_id, vehicle_id, departure_city, destination_city, departure_date, departure_time, price, max_duration, max_seats, status, is_eco) 
                   VALUES ($driver_id, $vehicle_id, 'Marseille', 'Nice', '$dayAfter', '14:30:00', 10, 120, 2, 'planned', 1)");

        echo "Data seeded successfully.\n";
    }
    else {
        echo "Database already has data. Skipping seed.\n";
    }

    echo "\nSetup Complete! You can now check the trips page.";

}
catch (Exception $e) {
    echo "\nError: " . $e->getMessage();
}
