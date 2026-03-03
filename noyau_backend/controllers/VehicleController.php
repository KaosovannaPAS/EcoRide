<?php
// noyau_backend/controllers/VehicleController.php
require_once __DIR__ . '/../models/Vehicle.php';

class VehicleController
{
    private $db;
    private $vehicle;

    public function __construct($db)
    {
        $this->db = $db;
        $this->vehicle = new Vehicle($db);
    }

    public function create($data)
    {
        if (empty($data->user_id) || empty($data->registration) || empty($data->model) || empty($data->color)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        $this->vehicle->user_id = $data->user_id;
        $this->vehicle->registration = $data->registration;
        $this->vehicle->model = $data->model;
        $this->vehicle->color = $data->color;
        $this->vehicle->is_electric = $data->is_electric ?? 0;

        if ($this->vehicle->create()) {
            return ["status" => 201, "message" => "Véhicule ajouté."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de l'ajout."];
        }
    }

    public function listByUser($user_id)
    {
        $stmt = $this->vehicle->getByUser($user_id);
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => 200, "vehicles" => $vehicles];
    }

    public function delete($vehicle_id, $user_id)
    {
        if ($this->vehicle->delete($vehicle_id, $user_id)) {
            return ["status" => 200, "message" => "Véhicule supprimé."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de la suppression."];
        }
    }
}
