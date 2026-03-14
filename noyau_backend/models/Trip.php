<?php
// noyau_backend/models/Trip.php
class Trip
{
    private $conn;
    private $table_name = "trips";

    public $id;
    public $driver_id;
    public $vehicle_id;
    public $departure_city;
    public $destination_city;
    public $departure_date;
    public $departure_time;
    public $price;
    public $max_duration;
    public $max_seats;
    public $status; // 'planned', 'started', 'finished', 'cancelled'

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        try {
            $this->conn->beginTransaction();

            // Check driver credits
            $checkCreditsQuery = "SELECT credits FROM users WHERE id = ? FOR UPDATE";
            $stmtCred = $this->conn->prepare($checkCreditsQuery);
            $stmtCred->execute([$this->driver_id]);
            $userRow = $stmtCred->fetch(PDO::FETCH_ASSOC);

            if (!$userRow || $userRow['credits'] < 2) {
                // Not enough credits for commission
                $this->conn->rollBack();
                return false;
            }

            // Deduct 2 credits
            $deductQuery = "UPDATE users SET credits = credits - 2 WHERE id = ?";
            $stmtDeduct = $this->conn->prepare($deductQuery);
            $stmtDeduct->execute([$this->driver_id]);

            // Insert trip
            $query = "INSERT INTO " . $this->table_name . " 
                      SET driver_id=:driver_id, vehicle_id=:vehicle_id, departure_city=:departure_city, 
                          destination_city=:destination_city, departure_date=:departure_date, departure_time=:departure_time, 
                          price=:price, max_duration=:max_duration, max_seats=:max_seats, status='planned'";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":driver_id", $this->driver_id);
            $stmt->bindParam(":vehicle_id", $this->vehicle_id);
            $stmt->bindValue(":departure_city", htmlspecialchars(strip_tags($this->departure_city)));
            $stmt->bindValue(":destination_city", htmlspecialchars(strip_tags($this->destination_city)));
            $stmt->bindParam(":departure_date", $this->departure_date);
            $stmt->bindParam(":departure_time", $this->departure_time);
            $stmt->bindParam(":price", $this->price);
            $stmt->bindParam(":max_duration", $this->max_duration);
            $stmt->bindParam(":max_seats", $this->max_seats);

            $stmt->execute();

            $this->conn->commit();
            return true;
        }
        catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function updateStatus($trip_id, $driver_id, $new_status)
    {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id AND driver_id = :driver_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':id', $trip_id);
        $stmt->bindParam(':driver_id', $driver_id);
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function search($filters)
    {
        // Build base query
        $query = "
            SELECT t.id, t.departure_city, t.destination_city, t.departure_date, t.departure_time, 
                   t.price, t.max_duration, t.max_seats, t.status, 
                   u.pseudo as driver_pseudo, u.photo as driver_photo, v.model as vehicle_model, v.is_electric
            FROM " . $this->table_name . " t
            JOIN users u ON t.driver_id = u.id
            JOIN vehicles v ON t.vehicle_id = v.id
            WHERE t.status = 'planned'
        ";

        $params = [];
        if (!empty($filters['departure_city'])) {
            $query .= " AND t.departure_city LIKE :departure_city";
            $params[':departure_city'] = "%" . $filters['departure_city'] . "%";
        }
        if (!empty($filters['destination_city'])) {
            $query .= " AND t.destination_city LIKE :destination_city";
            $params[':destination_city'] = "%" . $filters['destination_city'] . "%";
        }
        if (!empty($filters['departure_date'])) {
            $query .= " AND t.departure_date = :departure_date";
            $params[':departure_date'] = $filters['departure_date'];
        }
        if (!empty($filters['max_price'])) {
            $query .= " AND t.price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        if (!empty($filters['max_duration'])) {
            $query .= " AND t.max_duration <= :max_duration";
            $params[':max_duration'] = $filters['max_duration'];
        }
        if (!empty($filters['is_electric']) && $filters['is_electric'] == 'true') {
            $query .= " AND v.is_electric = 1";
        }

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt;
    }

    public function getByDriver($driver_id)
    {
        $query = "
            SELECT t.id, t.departure_city, t.destination_city, t.departure_date, t.departure_time, 
                   t.price, t.max_duration, t.max_seats, t.status, 
                   v.model as vehicle_model, v.registration
            FROM " . $this->table_name . " t
            JOIN vehicles v ON t.vehicle_id = v.id
            WHERE t.driver_id = :driver_id
            ORDER BY t.id DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':driver_id', $driver_id);
        $stmt->execute();
        return $stmt;
    }
    public function delete($id, $driver_id)
    {
        // Only allow deleting planned trips
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND driver_id = :driver_id AND status = 'planned'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':driver_id', $driver_id);
        return $stmt->execute() && $stmt->rowCount() > 0;
    }
}
