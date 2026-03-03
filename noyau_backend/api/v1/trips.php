<?php
// noyau_backend/api/v1/trips.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../../configuration/db.php';
require_once '../../models/Trip.php';

$action = $_GET['action'] ?? '';
$trip = new Trip($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if ($action === 'create') {
        if (!empty($data->driver_id) && !empty($data->vehicle_id) &&
        !empty($data->departure_city) && !empty($data->destination_city) &&
        !empty($data->departure_date) && !empty($data->departure_time) &&
        isset($data->price) && !empty($data->max_duration) && !empty($data->max_seats)) {

            $trip->driver_id = $data->driver_id;
            $trip->vehicle_id = $data->vehicle_id;
            $trip->departure_city = $data->departure_city;
            $trip->destination_city = $data->destination_city;
            $trip->departure_date = $data->departure_date;
            $trip->departure_time = $data->departure_time;
            $trip->price = $data->price;
            $trip->max_duration = $data->max_duration;
            $trip->max_seats = $data->max_seats;

            if ($trip->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Trajet publié avec succès. 2 crédits prélevés."]);
            }
            else {
                http_response_code(503);
                echo json_encode(["message" => "Impossible de publier le trajet. Vérifiez votre solde de crédits."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["message" => "Données incomplètes."]);
        }
    }
}
else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));
    if ($action === 'update_status') {
        if (!empty($data->trip_id) && !empty($data->driver_id) && !empty($data->status)) {
            if ($trip->updateStatus($data->trip_id, $data->driver_id, $data->status)) {
                http_response_code(200);
                echo json_encode(["message" => "Statut mis à jour."]);
            }
            else {
                http_response_code(503);
                echo json_encode(["message" => "Impossible de mettre à jour le statut."]);
            }
        }
    }
}
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'search') {
        $filters = [
            'departure_city' => $_GET['departure_city'] ?? null,
            'destination_city' => $_GET['destination_city'] ?? null,
            'departure_date' => $_GET['departure_date'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'max_duration' => $_GET['max_duration'] ?? null,
            'is_electric' => $_GET['is_electric'] ?? null
        ];

        $stmt = $trip->search($filters);
        $trips_arr = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $trips_arr[] = $row;
        }

        http_response_code(200);
        echo json_encode($trips_arr);
    }
}
