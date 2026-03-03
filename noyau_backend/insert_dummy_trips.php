<?php
require_once __DIR__ . '/configuration/db.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Vehicle.php';
require_once __DIR__ . '/models/Trip.php';

try {
    // Check if user exists, else create one
    $user = new User($pdo);
    $user->email = "test.driver@ecoride.fr";
    $userQuery = "SELECT id FROM users WHERE email = :email";
    $stmt = $pdo->prepare($userQuery);
    $stmt->execute([':email' => $user->email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $user->pseudo = "EcoPilot";
        $user->password = password_hash("password123", PASSWORD_BCRYPT);
        $user->create();
        $stmt->execute([':email' => $user->email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    $driver_id = $row['id'];

    // Add vehicle to driver
    $vehicleQuery = "SELECT id FROM vehicles WHERE user_id = :user_id LIMIT 1";
    $stmt2 = $pdo->prepare($vehicleQuery);
    $stmt2->execute([':user_id' => $driver_id]);
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

    if (!$row2) {
        $vehQuery = "INSERT INTO vehicles (user_id, registration, model, color, is_electric) VALUES (?, 'AB-123-CD', 'Tesla Model 3', 'Blanc', 1)";
        $pdo->prepare($vehQuery)->execute([$driver_id]);
        $vehicle_id = $pdo->lastInsertId();
    }
    else {
        $vehicle_id = $row2['id'];
    }

    // Ensure driver has credits
    $pdo->prepare("UPDATE users SET credits = 100 WHERE id = ?")->execute([$driver_id]);

    // Create 3 trips, next week
    $trips = [
        ['Paris', 'Lyon', date('Y-m-d', strtotime('+3 days')), '08:00', 35, 120, 3],
        ['Bordeaux', 'Toulouse', date('Y-m-d', strtotime('+4 days')), '10:30', 15, 60, 2],
        ['Marseille', 'Nice', date('Y-m-d', strtotime('+5 days')), '14:00', 20, 90, 4]
    ];

    $tripModel = new Trip($pdo);
    foreach ($trips as $t) {
        $tripModel->driver_id = $driver_id;
        $tripModel->vehicle_id = $vehicle_id;
        $tripModel->departure_city = $t[0];
        $tripModel->destination_city = $t[1];
        $tripModel->departure_date = $t[2];
        $tripModel->departure_time = $t[3];
        $tripModel->price = $t[4];
        $tripModel->max_duration = $t[5];
        $tripModel->max_seats = $t[6];
        $tripModel->create();
    }

    echo "Trips created successfully!";
}
catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
