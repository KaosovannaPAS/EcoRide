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
        $query = "SELECT id, pseudo, password_hash, role, credits FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && password_verify($password, $row['password_hash'])) {
            $this->id = $row['id'];
            $this->pseudo = $row['pseudo'];
            $this->role = $row['role'];
            $this->credits = $row['credits'];
            return true;
        }
        return false;
    }
}
