<?php
// super_diag.php
header('Content-Type: text/plain; charset=UTF-8');
require_once 'noyau_backend/configuration/db.php';

echo "=== ECO-RIDE SUPER DIAGNOSTICS ===\n\n";

echo "1. DATABASE CONNECTION\n";
try {
    echo "DSN: $dsn\n";
    echo "User: $user\n";
    echo "Connection: SUCCESS\n\n";
}
catch (Exception $e) {
    echo "Connection: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit;
}

echo "2. TABLE COUNTS\n";
try {
    $counts = [
        'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'vehicles' => $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn(),
        'trips' => $pdo->query("SELECT COUNT(*) FROM trips")->fetchColumn(),
        'planned_trips' => $pdo->query("SELECT COUNT(*) FROM trips WHERE status='planned'")->fetchColumn(),
        'reservations' => $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn()
    ];
    foreach ($counts as $table => $count) {
        echo "- $table: $count\n";
    }
    echo "\n";
}
catch (Exception $e) {
    echo "Count Error: " . $e->getMessage() . "\n\n";
}

echo "3. TRIP DATA INTEGRITY (LIMIT 5)\n";
try {
    $stmt = $pdo->query("SELECT id, driver_id, vehicle_id, status, departure_city, destination_city FROM trips LIMIT 5");
    $trips = $stmt->fetchAll();
    if (empty($trips)) {
        echo "No trips found in table.\n";
    }
    else {
        foreach ($trips as $t) {
            echo "Trip #{$t['id']}: {$t['departure_city']} -> {$t['destination_city']} (Status: {$t['status']}, Driver: {$t['driver_id']}, Vehicle: {$t['vehicle_id']})\n";

            // Check if driver exists
            $dExist = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
            $dExist->execute([$t['driver_id']]);
            echo "  - Driver exists: " . ($dExist->fetchColumn() ? "YES" : "NO !!!") . "\n";

            // Check if vehicle exists
            $vExist = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE id = ?");
            $vExist->execute([$t['vehicle_id']]);
            echo "  - Vehicle exists: " . ($vExist->fetchColumn() ? "YES" : "NO !!!") . "\n";
        }
    }
    echo "\n";
}
catch (Exception $e) {
    echo "Integrity Error: " . $e->getMessage() . "\n\n";
}

echo "4. SEARCH QUERY SIMULATION\n";
try {
    $query = "
        SELECT t.id, t.departure_city, t.destination_city, u.pseudo, v.model
        FROM trips t
        JOIN users u ON t.driver_id = u.id
        JOIN vehicles v ON t.vehicle_id = v.id
        WHERE t.status = 'planned'
    ";
    $stmt = $pdo->query($query);
    $results = $stmt->fetchAll();
    echo "Search results for 'all planned': " . count($results) . "\n";
    if (count($results) == 0 && $counts['planned_trips'] > 0) {
        echo "WARNING: JOINS ARE FILTERING OUT ALL TRIPS! Check if driver_id or vehicle_id match records in users/vehicles.\n";
    }
    echo "\n";
}
catch (Exception $e) {
    echo "Search Simulation Error: " . $e->getMessage() . "\n\n";
}

echo "5. SERVER INFO\n";
echo "PHP version: " . PHP_VERSION . "\n";
echo "PDO extensions: " . implode(', ', PDO::getAvailableDrivers()) . "\n";
echo "=== END OF DIAGNOSTICS ===\n";
?>
