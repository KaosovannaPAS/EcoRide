<?php
// noyau_backend/api/v1/users.php
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
require_once '../../models/User.php';

$action = $_GET['action'] ?? '';
$user = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if ($action === 'register') {
        if (!empty($data->pseudo) && !empty($data->email) && !empty($data->password)) {
            $user->pseudo = $data->pseudo;
            $user->email = $data->email;
            $user->password = $data->password;

            if ($user->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Utilisateur créé avec 20 crédits offerts."]);
            }
            else {
                http_response_code(503);
                echo json_encode(["message" => "Impossible de créer l'utilisateur. Pseudo ou email peut-être déjà pris."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["message" => "Données incomplètes."]);
        }
    }
    else if ($action === 'login') {
        if (!empty($data->email) && !empty($data->password)) {
            if ($user->login($data->email, $data->password)) {
                http_response_code(200);
                // In a real app we would use JWT, let's return user details for simplicity
                echo json_encode([
                    "message" => "Connexion réussie.",
                    "user" => [
                        "id" => $user->id,
                        "pseudo" => $user->pseudo,
                        "role" => $user->role,
                        "credits" => $user->credits
                    ]
                ]);
            }
            else {
                http_response_code(401);
                echo json_encode(["message" => "Email ou mot de passe incorrect."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["message" => "Données incomplètes."]);
        }
    }
    else {
        http_response_code(404);
        echo json_encode(["message" => "Action non supportée."]);
    }
}
