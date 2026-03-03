<?php
// noyau_backend/models/Reservation.php
class Reservation
{
    private $conn;
    private $table_name = "reservations";

    public $id;
    public $trip_id;
    public $passenger_id;
    public $status;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($price)
    {
        try {
            $this->conn->beginTransaction();

            $checkCreditsQuery = "SELECT credits FROM users WHERE id = ? FOR UPDATE";
            $stmtCred = $this->conn->prepare($checkCreditsQuery);
            $stmtCred->execute([$this->passenger_id]);
            $userRow = $stmtCred->fetch(PDO::FETCH_ASSOC);

            if (!$userRow || $userRow['credits'] < $price) {
                $this->conn->rollBack();
                return false;
            }

            // Deduct credits from passenger
            $deductQuery = "UPDATE users SET credits = credits - ? WHERE id = ?";
            $stmtDeduct = $this->conn->prepare($deductQuery);
            $stmtDeduct->execute([$price, $this->passenger_id]);

            // Add credits to driver
            $driverQuery = "SELECT driver_id FROM trips WHERE id = ?";
            $stmtDriver = $this->conn->prepare($driverQuery);
            $stmtDriver->execute([$this->trip_id]);
            $tripRow = $stmtDriver->fetch(PDO::FETCH_ASSOC);
            if ($tripRow) {
                $addQuery = "UPDATE users SET credits = credits + ? WHERE id = ?";
                $stmtAdd = $this->conn->prepare($addQuery);
                $stmtAdd->execute([$price, $tripRow['driver_id']]);
            }

            // Insert reservation
            $query = "INSERT INTO " . $this->table_name . " 
                      SET trip_id=:trip_id, passenger_id=:passenger_id, status='pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":trip_id", $this->trip_id);
            $stmt->bindParam(":passenger_id", $this->passenger_id);
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

    public function updateStatus($id, $status)
    {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute() && $stmt->rowCount() > 0;
    }
}
