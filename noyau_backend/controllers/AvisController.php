<?php
// noyau_backend/controllers/AvisController.php
require_once __DIR__ . '/../models/Avis.php';

class AvisController
{
    private $db;
    private $avis;

    public function __construct($db)
    {
        $this->db = $db;
        $this->avis = new Avis($db);
    }

    public function create($data)
    {
        if (empty($data->trajet_id) || empty($data->auteur_id) || empty($data->cible_id) || empty($data->commentaire) || empty($data->note)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        if ($this->avis->create($data->trajet_id, $data->auteur_id, $data->cible_id, $data->note, $data->commentaire)) {
            return ["status" => 201, "message" => "Avis envoyé pour modération."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de l'envoi."];
        }
    }

    public function listPending()
    {
        $reviews = $this->avis->getPending();
        return ["status" => 200, "avis" => $reviews];
    }

    public function moderate($data)
    {
        if (empty($data->review_id) || empty($data->status)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        if ($this->avis->updateStatus($data->review_id, $data->status)) {
            return ["status" => 200, "message" => "Avis mis à jour."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de la modération."];
        }
    }
}
