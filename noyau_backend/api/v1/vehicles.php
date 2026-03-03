<?php
// noyau_backend/api/v1/vehicles.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../../configuration/db.php';
require_once '../../controllers/VehicleController.php';

$controller = new VehicleController($pdo);
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
        if ($action === 'list') {
            $res = $controller->listByUser($_GET['user_id'] ?? 0);
            http_response_code($res['status']);
            echo json_encode($res['vehicles'] ?? []);
            exit;
        }
        else {
            $res = ["status" => 400, "message" => "Action inconnue."];
        }
        break;

    case 'DELETE':
        $res = $controller->delete($_GET['id'] ?? 0, $_GET['user_id'] ?? 0);
        break;

    default:
        $res = ["status" => 405, "message" => "Méthode non autorisée."];
        break;
}

http_response_code($res['status']);
echo json_encode($res);
