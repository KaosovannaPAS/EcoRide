<?php
// noyau_backend/models/User.php

class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $pseudo;
    public $email;
    public $password; // Used for incoming password
    public $role;
    public $credits;
    public $photo;
    public $bio;
    public $pref_smoking;
    public $pref_animals;
    public $pref_music;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET pseudo=:pseudo, email=:email, password_hash=:password_hash, role=:role, credits=:credits";
        $stmt = $this->conn->prepare($query);

        $this->pseudo = htmlspecialchars(strip_tags($this->pseudo));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $this->role = $this->role ?? 'passager';
        $this->credits = 20; // 20 credits offered on signup

        $stmt->bindParam(":pseudo", $this->pseudo);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password_hash", $password_hash);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":credits", $this->credits);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function login($email, $password)
    {
        $query = "SELECT id, pseudo, password_hash, role, credits, photo, bio, pref_smoking, pref_animals, pref_music 
                  FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Check if role is suspended
        if ($row && $row['role'] === 'suspended') {
            return false;
        }

        if ($row && password_verify($password, $row['password_hash'])) {
            $this->id = $row['id'];
            $this->pseudo = $row['pseudo'];
            $this->role = $row['role'];
            $this->credits = $row['credits'];
            $this->photo = $row['photo'];
            $this->bio = $row['bio'];
            $this->pref_smoking = $row['pref_smoking'];
            $this->pref_animals = $row['pref_animals'];
            $this->pref_music = $row['pref_music'];
            return true;
        }
        return false;
    }

    public function getById($id)
    {
        $query = "SELECT id, pseudo, email, role, credits, photo, bio, pref_smoking, pref_animals, pref_music 
                  FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->pseudo = $row['pseudo'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->credits = $row['credits'];
            $this->photo = $row['photo'];
            $this->bio = $row['bio'];
            $this->pref_smoking = $row['pref_smoking'];
            $this->pref_animals = $row['pref_animals'];
            $this->pref_music = $row['pref_music'];
            return true;
        }
        return false;
    }

    public function getAllUsers()
    {
        $query = "SELECT id, pseudo, email, role, credits FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateRole($id, $new_role)
    {
        $query = "UPDATE " . $this->table_name . " SET role = :role WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $new_role);
        $stmt->bindParam(':id', $id);
        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function updateProfile($id, $data)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET bio = :bio, photo = :photo, 
                      pref_smoking = :smoking, pref_animals = :animals, pref_music = :music 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $bio = htmlspecialchars(strip_tags($data->bio ?? ''));
        $photo = htmlspecialchars(strip_tags($data->photo ?? ''));
        $smoking = (int)($data->pref_smoking ?? 0);
        $animals = (int)($data->pref_animals ?? 0);
        $music = (int)($data->pref_music ?? 0);

        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':photo', $photo);
        $stmt->bindParam(':smoking', $smoking);
        $stmt->bindParam(':animals', $animals);
        $stmt->bindParam(':music', $music);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute() && $stmt->rowCount() > 0;
    }
}
