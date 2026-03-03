<?php
// noyau_backend/api/v1/admin.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, PUT, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../../configuration/db.php';
require_once '../../configuration/mongo.php';
require_once '../../models/Review.php';

$action = $_GET['action'] ?? '';
$review = isset($mongoDb) ? new Review($mongoDb) : null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'pending_reviews') {
        if ($review) {
            $reviews = $review->getPending();
            http_response_code(200);
            echo json_encode($reviews);
        }
        else {
            http_response_code(500);
            echo json_encode(["message" => "MongoDB non configuré."]);
        }
    }
    else if ($action === 'dashboard_stats') {
        // Pseudo logic for dashboard stats
        // 1. Chart of carpools per day
        $queryTrips = "SELECT DATE(created_at) as date, COUNT(*) as count FROM trips GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 30";
        $stmt = $pdo->prepare($queryTrips);
        $stmt->execute();
        $trips_per_day = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Credits earned by platform
        // Each trip earns 2 credits
        $queryCredits = "SELECT COUNT(*) * 2 as total_credits FROM trips";
        $stmtC = $pdo->prepare($queryCredits);
        $stmtC->execute();
        $credits_row = $stmtC->fetch(PDO::FETCH_ASSOC);
        $total_credits = $credits_row['total_credits'] ?? 0;

        http_response_code(200);
        echo json_encode([
            "trips_per_day" => $trips_per_day,
            "total_credits_earned" => $total_credits
        ]);
    }
}
else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));
    if ($action === 'moderate_review') {
        if (!empty($data->review_id) && !empty($data->status) && $review) {
            if ($review->updateStatus($data->review_id, $data->status)) {
                http_response_code(200);
                echo json_encode(["message" => "Avis modéré avec succès."]);
            }
            else {
                http_response_code(503);
                echo json_encode(["message" => "Impossible de modérer l'avis."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["message" => "Données incomplètes."]);
        }
    }
}
