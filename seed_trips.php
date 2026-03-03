<?php
// seed_trips.php
require_once __DIR__ . '/noyau_backend/configuration/db.php';

try {
    // 1. Create Users
    $users = [
        ['pseudo' => 'JeanP', 'email' => 'jean@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT), 'role' => 'chauffeur', 'credits' => 100],
        ['pseudo' => 'MarieD', 'email' => 'marie@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT), 'role' => 'chauffeur', 'credits' => 50],
        ['pseudo' => 'AlexL', 'email' => 'alex@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT), 'role' => 'chauffeur', 'credits' => 80]
    ];

    $stmtUser = $pdo->prepare("INSERT IGNORE INTO users (pseudo, email, password_hash, role, credits) VALUES (:pseudo, :email, :password, :role, :credits)");
    foreach ($users as $u) {
        $stmtUser->execute($u);
    }
    echo "Users seeded.\n";

    // 2. Create Vehicles
    $vehicles = [
        ['user_id' => 1, 'registration' => 'AB-123-CD', 'model' => 'Tesla Model 3', 'color' => 'Blanc', 'is_electric' => 1],
        ['user_id' => 2, 'registration' => 'EF-456-GH', 'model' => 'Renault Zoe', 'color' => 'Bleu', 'is_electric' => 1],
        ['user_id' => 3, 'registration' => 'IJ-789-KL', 'model' => 'Peugeot 308', 'color' => 'Gris', 'is_electric' => 0]
    ];

    $stmtVeh = $pdo->prepare("INSERT IGNORE INTO vehicles (user_id, registration, model, color, is_electric) VALUES (:user_id, :registration, :model, :color, :is_electric)");
    foreach ($vehicles as $v) {
        $stmtVeh->execute($v);
    }
    echo "Vehicles seeded.\n";

    // 3. Create Trips
    $trips = [
        [
            'driver_id' => 1,
            'vehicle_id' => 1,
            'departure_city' => 'Paris',
            'destination_city' => 'Lyon',
            'departure_date' => '2026-03-10',
            'departure_time' => '08:00:00',
            'price' => 25,
            'max_duration' => 270,
            'max_seats' => 3
        ],
        [
            'driver_id' => 2,
            'vehicle_id' => 2,
            'departure_city' => 'Bordeaux',
            'destination_city' => 'Toulouse',
            'departure_date' => '2026-03-12',
            'departure_time' => '10:30:00',
            'price' => 15,
            'max_duration' => 150,
            'max_seats' => 4
        ],
        [
            'driver_id' => 3,
            'vehicle_id' => 3,
            'departure_city' => 'Lille',
            'destination_city' => 'Paris',
            'departure_date' => '2026-03-16',
            'departure_time' => '07:00:00',
            'price' => 20,
            'max_duration' => 180,
            'max_seats' => 3
        ],
        [
            'driver_id' => 1,
            'vehicle_id' => 1,
            'departure_city' => 'Nantes',
            'destination_city' => 'Angers',
            'departure_date' => '2026-03-20',
            'departure_time' => '09:00:00',
            'price' => 8,
            'max_duration' => 60,
            'max_seats' => 2
        ],
        [
            'driver_id' => 2,
            'vehicle_id' => 2,
            'departure_city' => 'Marseille',
            'destination_city' => 'Nice',
            'departure_date' => '2026-03-21',
            'departure_time' => '14:00:00',
            'price' => 12,
            'max_duration' => 160,
            'max_seats' => 3
        ],
        [
            'driver_id' => 1,
            'vehicle_id' => 1,
            'departure_city' => 'Lyon',
            'destination_city' => 'Grenoble',
            'departure_date' => '2026-03-22',
            'departure_time' => '08:30:00',
            'price' => 10,
            'max_duration' => 80,
            'max_seats' => 3
        ],
        [
            'driver_id' => 3,
            'vehicle_id' => 3,
            'departure_city' => 'Strasbourg',
            'destination_city' => 'Colmar',
            'departure_date' => '2026-03-25',
            'departure_time' => '10:00:00',
            'price' => 7,
            'max_duration' => 50,
            'max_seats' => 2
        ],
        [
            'driver_id' => 2,
            'vehicle_id' => 2,
            'departure_city' => 'Rennes',
            'destination_city' => 'Saint-Malo',
            'departure_date' => '2026-03-28',
            'departure_time' => '11:00:00',
            'price' => 9,
            'max_duration' => 55,
            'max_seats' => 3
        ],
        [
            'driver_id' => 1,
            'vehicle_id' => 1,
            'departure_city' => 'Toulouse',
            'destination_city' => 'Montpellier',
            'departure_date' => '2026-03-30',
            'departure_time' => '07:45:00',
            'price' => 18,
            'max_duration' => 140,
            'max_seats' => 4
        ],
        [
            'driver_id' => 3,
            'vehicle_id' => 3,
            'departure_city' => 'Dijon',
            'destination_city' => 'Besançon',
            'departure_date' => '2026-04-02',
            'departure_time' => '17:00:00',
            'price' => 11,
            'max_duration' => 75,
            'max_seats' => 3
        ],
        [
            'driver_id' => 2,
            'vehicle_id' => 2,
            'departure_city' => 'Tours',
            'destination_city' => 'Orléans',
            'departure_date' => '2026-04-05',
            'departure_time' => '08:15:00',
            'price' => 13,
            'max_duration' => 90,
            'max_seats' => 2
        ],
        [
            'driver_id' => 1,
            'vehicle_id' => 1,
            'departure_city' => 'Paris',
            'destination_city' => 'Lille',
            'departure_date' => '2026-04-08',
            'departure_time' => '10:00:00',
            'price' => 12,
            'max_duration' => 130,
            'max_seats' => 3
        ],
        [
            'driver_id' => 2,
            'vehicle_id' => 2,
            'departure_city' => 'Bordeaux',
            'destination_city' => 'La Rochelle',
            'departure_date' => '2026-04-10',
            'departure_time' => '15:30:00',
            'price' => 14,
            'max_duration' => 110,
            'max_seats' => 3
        ]
    ];

    foreach ($trips as $t) {
        // Use Trip model to benefit from credit logic
        require_once __DIR__ . '/noyau_backend/models/Trip.php';
        $tripModel = new Trip($pdo);
        $tripModel->driver_id = $t['driver_id'];
        $tripModel->vehicle_id = $t['vehicle_id'];
        $tripModel->departure_city = $t['departure_city'];
        $tripModel->destination_city = $t['destination_city'];
        $tripModel->departure_date = $t['departure_date'];
        $tripModel->departure_time = $t['departure_time'];
        $tripModel->price = $t['price'];
        $tripModel->max_duration = $t['max_duration'];
        $tripModel->max_seats = $t['max_seats'];

        if ($tripModel->create()) {
            echo "Trip " . $t['departure_city'] . " -> " . $t['destination_city'] . " created.\n";
        }
        else {
            echo "Failed to create trip " . $t['departure_city'] . " -> " . $t['destination_city'] . "\n";
        }
    }

}
catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
