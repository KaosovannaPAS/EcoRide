<?php
// noyau_backend/api/v1/reservations.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, PUT, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../../configuration/db.php';
require_once '../../models/Reservation.php';

$action = $_GET['action'] ?? '';
$reservation = new Reservation($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if ($action === 'create') {
        if (!empty($data->trip_id) && !empty($data->passenger_id) && isset($data->price)) {
            $reservation->trip_id = $data->trip_id;
            $reservation->passenger_id = $data->passenger_id;

            if ($reservation->create($data->price)) {
                http_response_code(201);
                echo json_encode(["message" => "Réservation effectuée. Crédits débités."]);
            }
            else {
                http_response_code(503);
                echo json_encode(["message" => "Impossible de réserver. Crédits insuffisants ou erreur."]);
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
        if (!empty($data->reservation_id) && !empty($data->status)) {
            // Double confirmation usually means changing status to validated or refused
            if ($reservation->updateStatus($data->reservation_id, $data->status)) {
                http_response_code(200);
                echo json_encode(["message" => "Statut de réservation mis à jour."]);
            }
            else {
                http_response_code(503);
                echo json_encode(["message" => "Impossible de mettre à jour le statut."]);
            }
        }
    }
}
