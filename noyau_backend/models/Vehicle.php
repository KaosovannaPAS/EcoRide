<?php
// noyau_backend/models/Vehicle.php

class Vehicle
{
    private $conn;
    private $table_name = "vehicles";

    public $id;
    public $user_id;
    public $registration;
    public $model;
    public $color;
    public $is_electric;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, registration=:registration, model=:model, color=:color, is_electric=:is_electric";
        $stmt = $this->conn->prepare($query);

        $this->registration = htmlspecialchars(strip_tags($this->registration));
        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->color = htmlspecialchars(strip_tags($this->color));
        $this->is_electric = $this->is_electric ? 1 : 0;

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":registration", $this->registration);
        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":color", $this->color);
        $stmt->bindParam(":is_electric", $this->is_electric);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getByUser($user_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET registration=:registration, model=:model, color=:color, is_electric=:is_electric 
                  WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":registration", $this->registration);
        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":color", $this->color);
        $stmt->bindParam(":is_electric", $this->is_electric);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        return $stmt->execute();
    }

    public function delete($id, $user_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }
}
