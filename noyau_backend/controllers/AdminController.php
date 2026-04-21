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
            // Volume quotidien
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM trajets WHERE DATE(date_creation) = CURDATE()");
            $tripsToday = $stmt->fetch()['total'] ?? 0;

            // Total crédits générés (2 par trajet publié)
            $stmtC = $this->db->query("SELECT COUNT(*) * 2 as total FROM trajets");
            $totalCredits = $stmtC->fetch()['total'] ?? 0;

            return ["status" => 200, "stats" => [
                    "credits_generes" => $totalCredits,
                    "trajets_aujourdhui" => $tripsToday
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
                SELECT i.*, t.ville_depart, t.ville_arrivee, u.pseudo as signaleur 
                FROM incidents i 
                JOIN trajets t ON i.trajet_id = t.id 
                JOIN utilisateurs u ON i.utilisateur_id = u.id 
                ORDER BY i.date_creation DESC
            ");
            return ["status" => 200, "incidents" => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        }
        catch (Exception $e) {
            return ["status" => 500, "message" => "Erreur incidents."];
        }
    }

    public function reportIncident($data)
    {
        if (empty($data->trajet_id) || empty($data->utilisateur_id) || empty($data->description)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        $stmt = $this->db->prepare("INSERT INTO incidents (trajet_id, utilisateur_id, description) VALUES (?, ?, ?)");
        if ($stmt->execute([$data->trajet_id, $data->utilisateur_id, $data->description])) {
            return ["status" => 201, "message" => "Incident signalé."];
        }
        else {
            return ["status" => 500, "message" => "Erreur signalement."];
        }
    }

    public function resolveIncident($incident_id)
    {
        $stmt = $this->db->prepare("UPDATE incidents SET statut = 'resolu' WHERE id = ?");
        if ($stmt->execute([$incident_id])) {
            return ["status" => 200, "message" => "Incident résolu."];
        }
        else {
            return ["status" => 500, "message" => "Erreur résolution."];
        }
    }
}

