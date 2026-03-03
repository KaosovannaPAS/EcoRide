<?php
// noyau_backend/api/v1/reviews.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../../configuration/mongo.php';
require_once '../../models/Review.php';

$action = $_GET['action'] ?? '';
$review = isset($mongoDb) ? new Review($mongoDb) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if ($action === 'create') {
        if (!empty($data->trip_id) && !empty($data->reviewer_id) && isset($data->reviewee_id) && isset($data->rating) && !empty($data->comment)) {
            if ($review && $review->create($data->trip_id, $data->reviewer_id, $data->reviewee_id, $data->rating, $data->comment)) {
                http_response_code(201);
                echo json_encode(["message" => "Avis soumis et en attente de modération."]);
            }
            else {
                http_response_code(503);
                echo json_encode(["message" => "Impossible de soumettre l'avis."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["message" => "Données incomplètes."]);
        }
    }
}
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'approved') {
        $reviewee_id = $_GET['user_id'] ?? null;
        if ($reviewee_id && $review) {
            $reviews = $review->getApprovedByReviewee($reviewee_id);
            http_response_code(200);
            echo json_encode($reviews);
        }
        else {
            http_response_code(400);
            echo json_encode(["message" => "user_id manquant ou erreur serveur."]);
        }
    }
}
