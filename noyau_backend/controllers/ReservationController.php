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
        if (empty($data->trip_id) || empty($data->passenger_id) || empty($data->price)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        $this->reservation->trip_id = $data->trip_id;
        $this->reservation->passenger_id = $data->passenger_id;

        if ($this->reservation->create($data->price)) {
            return ["status" => 201, "message" => "Réservation effectuée."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de la réservation."];
        }
    }

    public function listByPassenger($passenger_id)
    {
        $stmt = $this->reservation->getByPassenger($passenger_id);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "reservations" => $reservations];
    }
}
