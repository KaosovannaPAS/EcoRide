<?php
// noyau_backend/controllers/TripController.php
require_once __DIR__ . '/../models/Trip.php';

class TripController
{
    private $db;
    private $trip;

    public function __construct($db)
    {
        $this->db = $db;
        $this->trip = new Trip($db);
    }

    public function create($data)
    {
        if (empty($data->driver_id) || empty($data->vehicle_id) || empty($data->departure_city) ||
        empty($data->destination_city) || empty($data->departure_date) || empty($data->price)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        $this->trip->driver_id = $data->driver_id;
        $this->trip->vehicle_id = $data->vehicle_id;
        $this->trip->departure_city = $data->departure_city;
        $this->trip->destination_city = $data->destination_city;
        $this->trip->departure_date = $data->departure_date;
        $this->trip->departure_time = $data->departure_time;
        $this->trip->price = $data->price;
        $this->trip->max_duration = $data->max_duration;
        $this->trip->max_seats = $data->max_seats;

        if ($this->trip->create()) {
            return ["status" => 201, "message" => "Trajet créé (2 crédits prélevés)."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de la création ou crédits insuffisants."];
        }
    }

    public function search($filters)
    {
        $stmt = $this->trip->search($filters);
        $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "trips" => $trips];
    }

    public function getByDriver($driver_id)
    {
        $stmt = $this->trip->getByDriver($driver_id);
        $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "trips" => $trips];
    }

    public function updateStatus($data)
    {
        if (empty($data->trip_id) || empty($data->driver_id) || empty($data->status)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        if ($this->trip->updateStatus($data->trip_id, $data->driver_id, $data->status)) {
            return ["status" => 200, "message" => "Statut mis à jour."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de la mise à jour."];
        }
    }
}
