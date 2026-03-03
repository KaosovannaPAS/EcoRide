<?php
// noyau_backend/controllers/UserController.php
require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $db;
    private $user;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function login($data)
    {
        if (empty($data->email) || empty($data->password)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        if ($this->user->login($data->email, $data->password)) {
            return ["status" => 200, "user" => [
                    "id" => $this->user->id,
                    "pseudo" => $this->user->pseudo,
                    "role" => $this->user->role,
                    "credits" => $this->user->credits,
                    "photo" => $this->user->photo,
                    "bio" => $this->user->bio,
                    "pref_smoking" => $this->user->pref_smoking,
                    "pref_animals" => $this->user->pref_animals,
                    "pref_music" => $this->user->pref_music
                ]];
        }
        else {
            return ["status" => 401, "message" => "Identifiants incorrects."];
        }
    }

    public function register($data)
    {
        if (empty($data->pseudo) || empty($data->email) || empty($data->password)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        $this->user->pseudo = $data->pseudo;
        $this->user->email = $data->email;
        $this->user->password = $data->password;
        if (isset($data->role))
            $this->user->role = $data->role;

        if ($this->user->register()) {
            return ["status" => 201, "message" => "Utilisateur créé."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de la création."];
        }
    }

    public function getProfile($user_id)
    {
        if ($this->user->getById($user_id)) {
            return ["status" => 200, "profile" => [
                    "id" => $this->user->id,
                    "pseudo" => $this->user->pseudo,
                    "email" => $this->user->email,
                    "role" => $this->user->role,
                    "credits" => $this->user->credits,
                    "photo" => $this->user->photo,
                    "bio" => $this->user->bio,
                    "pref_smoking" => $this->user->pref_smoking,
                    "pref_animals" => $this->user->pref_animals,
                    "pref_music" => $this->user->pref_music
                ]];
        }
        else {
            return ["status" => 404, "message" => "Utilisateur non trouvé."];
        }
    }

    public function updateProfile($data)
    {
        if (empty($data->user_id)) {
            return ["status" => 400, "message" => "ID utilisateur manquant."];
        }

        if ($this->user->updateProfile($data->user_id, $data)) {
            return ["status" => 200, "message" => "Profil mis à jour."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors de la mise à jour."];
        }
    }

    public function listAll()
    {
        $users = $this->user->getAllUsers();
        return ["status" => 200, "users" => $users];
    }

    public function updateRole($data)
    {
        if (empty($data->user_id) || empty($data->role)) {
            return ["status" => 400, "message" => "Données incomplètes."];
        }

        if ($this->user->updateRole($data->user_id, $data->role)) {
            return ["status" => 200, "message" => "Rôle mis à jour."];
        }
        else {
            return ["status" => 500, "message" => "Erreur lors du changement de rôle."];
        }
    }
}
