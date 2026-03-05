<?php
// noyau_backend/api/v1/trips.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../noyau_backend/configuration/db.php';
require_once __DIR__ . '/../noyau_backend/controllers/TripController.php';

$controller = new TripController($pdo);
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if ($action === 'create') {
            $res = $controller->create($data);
        }
        else {
            $res = ["status" => 400, "message" => "Action inconnue."];
        }
        break;

    case 'GET':
        if ($action === 'search') {
            $filters = [
                'departure_city' => $_GET['departure_city'] ?? null,
                'destination_city' => $_GET['destination_city'] ?? null,
                'departure_date' => $_GET['departure_date'] ?? null,
                'max_price' => $_GET['max_price'] ?? null,
                'max_duration' => $_GET['max_duration'] ?? null,
                'is_electric' => $_GET['is_electric'] ?? null
            ];
            $res = $controller->search($filters);
            // Compatibility
            http_response_code(200);
            echo json_encode($res['trips'] ?? []);
            exit;
        }
        else if ($action === 'list_by_driver') {
            $res = $controller->getByDriver($_GET['driver_id'] ?? null);
            http_response_code($res['status']);
            echo json_encode($res['trips'] ?? []);
            exit;
        }
        else {
            $res = ["status" => 400, "message" => "Action inconnue."];
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if ($action === 'update_status') {
            $res = $controller->updateStatus($data);
        }
        else {
            $res = ["status" => 400, "message" => "Action inconnue."];
        }
        break;

    default:
        $res = ["status" => 405, "message" => "Méthode non autorisée."];
        break;
}

http_response_code($res['status']);
echo json_encode($res);
