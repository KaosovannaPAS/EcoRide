<?php
// noyau_backend/controllers/UtilisateurController.php
require_once __DIR__ . '/../models/Utilisateur.php';

class UtilisateurController
{
    private $db;
    private $utilisateur;

    public function __construct($db)
    {
        $this->db = $db;
        $this->utilisateur = new Utilisateur($db);
    }

    public function login($data)
    {
        if (empty($data->email) || empty($data->mot_de_passe)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        if ($this->utilisateur->login($data->email, $data->mot_de_passe)) {
            return ["status" => 200, "utilisateur" => [
                    "id" => $this->utilisateur->id,
                    "pseudo" => $this->utilisateur->pseudo,
                    "role" => $this->utilisateur->role,
                    "credits" => $this->utilisateur->credits,
                    "photo" => $this->utilisateur->photo,
                    "biographie" => $this->utilisateur->biographie,
                    "pref_fumeur" => $this->utilisateur->pref_fumeur,
                    "pref_animaux" => $this->utilisateur->pref_animaux,
                    "pref_musique" => $this->utilisateur->pref_musique
                ]];
        }
        else {
            return ["status" => 401, "message" => "Identifiants incorrects."];
        }
    }

    public function register($data)
    {
        if (empty($data->pseudo) || empty($data->email) || empty($data->mot_de_passe)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        $this->utilisateur->pseudo = $data->pseudo;
        $this->utilisateur->email = $data->email;
        $this->utilisateur->mot_de_passe = $data->mot_de_passe;
        if (isset($data->role))
            $this->utilisateur->role = $data->role;

        if ($this->utilisateur->create()) {
            return ["status" => 201, "message" => "Utilisateur créé."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de la création."];
        }
    }

    public function getProfile($utilisateur_id)
    {
        if ($this->utilisateur->getById($utilisateur_id)) {
            return ["status" => 200, "profil" => [
                    "id" => $this->utilisateur->id,
                    "pseudo" => $this->utilisateur->pseudo,
                    "email" => $this->utilisateur->email,
                    "role" => $this->utilisateur->role,
                    "credits" => $this->utilisateur->credits,
                    "photo" => $this->utilisateur->photo,
                    "biographie" => $this->utilisateur->biographie,
                    "pref_fumeur" => $this->utilisateur->pref_fumeur,
                    "pref_animaux" => $this->utilisateur->pref_animaux,
                    "pref_musique" => $this->utilisateur->pref_musique
                ]];
        }
        else {
            return ["status" => 404, "message" => "Utilisateur non trouvé."];
        }
    }

    public function updateProfile($data)
    {
        if (empty($data->utilisateur_id)) {
            return ["status" => 400, "message" => "ID utilisateur manquant."];
        }

        if ($this->utilisateur->updateProfile($data->utilisateur_id, $data)) {
            return ["status" => 200, "message" => "Profil mis à jour."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de la mise à jour."];
        }
    }

    public function listAll()
    {
        $users = $this->utilisateur->getAllUsers();
        return ["status" => 200, "utilisateurs" => $users];
    }

    public function updateRole($data)
    {
        if (empty($data->utilisateur_id) || empty($data->role)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        if ($this->utilisateur->updateRole($data->utilisateur_id, $data->role)) {
            return ["status" => 200, "message" => "Rôle mis à jour."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors du changement de rôle."];
        }
    }
}
