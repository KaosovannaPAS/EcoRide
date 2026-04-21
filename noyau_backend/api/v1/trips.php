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

require_once '../../configuration/db.php';
require_once '../../controllers/TrajetController.php';

$controller = new TrajetController($pdo);
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
                'ville_depart' => $_GET['ville_depart'] ?? null,
                'ville_destination' => $_GET['ville_destination'] ?? null,
                'date_depart' => $_GET['date_depart'] ?? null,
                'prix_max' => $_GET['prix_max'] ?? null,
                'duree_max' => $_GET['duree_max'] ?? null,
                'est_electrique' => $_GET['est_electrique'] ?? null
            ];
            $res = $controller->search($filters);
            // Compatibility
            http_response_code(200);
            echo json_encode($res['trajets'] ?? []);
            exit;
        }
        else if ($action === 'list_by_driver') {
            $res = $controller->getByDriver($_GET['conducteur_id'] ?? null);
            http_response_code($res['status']);
            echo json_encode($res['trajets'] ?? []);
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
