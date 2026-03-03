<?php
// noyau_backend/controllers/ReviewController.php
require_once __DIR__ . '/../models/Review.php';

class ReviewController
{
    private $db;
    private $review;

    public function __construct($db)
    {
        $this->db = $db;
        $this->review = new Review($db);
    }

    public function create($data)
    {
        if (empty($data->trip_id) || empty($data->passenger_id) || empty($data->reviewee_id) || empty($data->comment) || empty($data->rating)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        if ($this->review->create($data->trip_id, $data->passenger_id, $data->reviewee_id, $data->rating, $data->comment)) {
            return ["status" => 201, "message" => "Avis envoyé pour modération."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de l'envoi."];
        }
    }

    public function listPending()
    {
        $reviews = $this->review->getPending();
        return ["status" => 200, "reviews" => $reviews];
    }

    public function moderate($data)
    {
        if (empty($data->review_id) || empty($data->status)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        if ($this->review->updateStatus($data->review_id, $data->status)) {
            return ["status" => 200, "message" => "Avis mis à jour."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de la modération."];
        }
    }
}
