<?php
// noyau_backend/controllers/ReservationController.php
require_once __DIR__ . '/../models/Reservation.php';

class ReservationController
{
    private $db;
    private $reservation;

    public function __construct($db)
    {
        $this->db = $db;
        $this->reservation = new Reservation($db);
    }

    public function create($data)
    {
        if (empty($data->trajet_id) || empty($data->passager_id) || empty($data->prix)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        $this->reservation->trajet_id = $data->trajet_id;
        $this->reservation->passager_id = $data->passager_id;

        if ($this->reservation->create($data->prix)) {
            return ["status" => 201, "message" => "Réservation effectuée."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de la réservation."];
        }
    }

    public function listByPassenger($passager_id)
    {
        $stmt = $this->reservation->getByPassenger($passager_id);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "reservations" => $reservations];
    }
}

