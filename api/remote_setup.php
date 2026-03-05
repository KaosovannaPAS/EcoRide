<?php
// api/remote_setup.php
header('Content-Type: text/plain');

require_once __DIR__ . '/../noyau_backend/configuration/db.php';

echo "--- EcoRide Remote Setup ---\n";

try {
    // 1. Check current tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Existing tables: " . implode(", ", $tables) . "\n";

    // 2. Create tables if missing (Simplification of schema.sql)
    echo "Initializing schema...\n";

    $queries = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            last_name VARCHAR(50) NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            pseudo VARCHAR(50) UNIQUE NOT NULL,
            phone VARCHAR(20),
            address TEXT,
            birth_date DATE,
            role ENUM('admin', 'driver', 'passenger') DEFAULT 'passenger',
            credits INT DEFAULT 20,
            profile_picture VARCHAR(255)
        )",
        "CREATE TABLE IF NOT EXISTS vehicles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            brand VARCHAR(50) NOT NULL,
            model VARCHAR(50) NOT NULL,
            color VARCHAR(30),
            registration_number VARCHAR(20) UNIQUE NOT NULL,
            first_registration_date DATE,
            nb_seats INT NOT NULL,
            is_electric BOOLEAN DEFAULT FALSE,
            driver_id INT,
            FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS trips (
            id INT AUTO_INCREMENT PRIMARY KEY,
            departure_city VARCHAR(100) NOT NULL,
            destination_city VARCHAR(100) NOT NULL,
            departure_date DATE NOT NULL,
            departure_time TIME NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            seats_available INT NOT NULL,
            status ENUM('planned', 'completed', 'cancelled') DEFAULT 'planned',
            driver_id INT,
            vehicle_id INT,
            is_eco BOOLEAN DEFAULT FALSE,
            FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
        )"
    ];

    foreach ($queries as $q) {
        $pdo->exec($q);
        echo "Query executed successfully.\n";
    }

    // 3. Seed some data if users table is empty
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count == 0) {
        echo "Seeding initial data...\n";

        // Insert a driver
        $pdo->exec("INSERT INTO users (last_name, first_name, email, password, pseudo, role, credits) 
                   VALUES ('Eco', 'Driver', 'driver@ecoride.fr', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'EcoDriver', 'driver', 100)");
        $driver_id = $pdo->lastInsertId();

        // Insert a vehicle
        $pdo->exec("INSERT INTO vehicles (brand, model, color, registration_number, nb_seats, is_electric, driver_id) 
                   VALUES ('Tesla', 'Model 3', 'Blanc', 'ECO-2024-FR', 4, 1, $driver_id)");
        $vehicle_id = $pdo->lastInsertId();

        // Insert some trips
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $pdo->exec("INSERT INTO trips (departure_city, destination_city, departure_date, departure_time, price, seats_available, driver_id, vehicle_id, is_eco) 
                   VALUES ('Paris', 'Lyon', '$tomorrow', '08:00:00', 15.00, 3, $driver_id, $vehicle_id, 1)");

        $pdo->exec("INSERT INTO trips (departure_city, destination_city, departure_date, departure_time, price, seats_available, driver_id, vehicle_id, is_eco) 
                   VALUES ('Marseille', 'Nice', '$tomorrow', '14:30:00', 10.00, 2, $driver_id, $vehicle_id, 1)");

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
