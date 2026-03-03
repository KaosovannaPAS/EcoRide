<?php
// noyau_backend/controllers/AdminController.php

class AdminController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getDashboardStats()
    {
        try {
            // Daily volume
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM trips WHERE DATE(created_at) = CURDATE()");
            $tripsToday = $stmt->fetch()['total'] ?? 0;

            // Total credits generated (2 per trip published)
            $stmtC = $this->db->query("SELECT COUNT(*) * 2 as total FROM trips");
            $totalCredits = $stmtC->fetch()['total'] ?? 0;

            return ["status" => 200, "stats" => [
                    "credits_generated" => $totalCredits,
                    "trips_today" => $tripsToday
                ]];
        }
        catch (Exception $e) {
            return ["status" => 500, "message" => "Erreur statistiques."];
        }
    }

    public function listIncidents()
    {
        try {
            $stmt = $this->db->query("
                SELECT i.*, t.departure_city, t.destination_city, u.pseudo as reporter 
                FROM incidents i 
                JOIN trips t ON i.trip_id = t.id 
                JOIN users u ON i.user_id = u.id 
                ORDER BY i.created_at DESC
            ");
            return ["status" => 200, "incidents" => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        }
        catch (Exception $e) {
            return ["status" => 500, "message" => "Erreur incidents."];
        }
    }

    public function reportIncident($data)
    {
        if (empty($data->trip_id) || empty($data->user_id) || empty($data->description)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        $stmt = $this->db->prepare("INSERT INTO incidents (trip_id, user_id, description) VALUES (?, ?, ?)");
        if ($stmt->execute([$data->trip_id, $data->user_id, $data->description])) {
            return ["status" => 201, "message" => "Incident signalé."];
        }
        else {
            return ["status" => 500, "message" => "Erreur signalement."];
        }
    }

    public function resolveIncident($incident_id)
    {
        $stmt = $this->db->prepare("UPDATE incidents SET status = 'resolved' WHERE id = ?");
        if ($stmt->execute([$incident_id])) {
            return ["status" => 200, "message" => "Incident résolu."];
        }
        else {
            return ["status" => 500, "message" => "Erreur résolution."];
        }
    }
}
