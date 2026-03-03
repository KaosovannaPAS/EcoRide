<?php
// noyau_backend/api/v1/vehicles.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../../configuration/db.php';
require_once '../../models/Vehicle.php';

$action = $_GET['action'] ?? '';
$vehicle = new Vehicle($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if ($action === 'create') {
        if (!empty($data->user_id) && !empty($data->registration) && !empty($data->model) && !empty($data->color)) {
            $vehicle->user_id = $data->user_id;
            $vehicle->registration = $data->registration;
            $vehicle->model = $data->model;
            $vehicle->color = $data->color;
            $vehicle->is_electric = isset($data->is_electric) ? $data->is_electric : false;

            if ($vehicle->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Véhicule ajouté."]);
            }
            else {
                http_response_code(503);
                echo json_encode(["message" => "Impossible d'ajouter le véhicule."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["message" => "Données incomplètes."]);
        }
    }
}
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'list') {
        $user_id = $_GET['user_id'] ?? null;
        if ($user_id) {
            $stmt = $vehicle->readByUserId($user_id);
            $vehicles_arr = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $vehicles_arr[] = $row;
            }
            http_response_code(200);
            echo json_encode($vehicles_arr);
        }
        else {
            http_response_code(400);
            echo json_encode(["message" => "user_id manquant."]);
        }
    }
}
