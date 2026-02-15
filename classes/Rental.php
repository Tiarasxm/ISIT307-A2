<?php
/**
 * Rental class - Handles rental operations
 */
class Rental {
    private $conn;
    private $rentalId;
    private $userId;
    private $motorbikeCode;
    private $startDateTime;
    private $endDateTime;
    private $costPerHourAtStart;
    private $status;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    // Getters
    public function getRentalId() { return $this->rentalId; }
    public function getUserId() { return $this->userId; }
    public function getMotorbikeCode() { return $this->motorbikeCode; }
    public function getStartDateTime() { return $this->startDateTime; }
    public function getEndDateTime() { return $this->endDateTime; }
    public function getCostPerHourAtStart() { return $this->costPerHourAtStart; }
    public function getStatus() { return $this->status; }
    
    // Setters
    public function setUserId($userId) { $this->userId = $userId; }
    public function setMotorbikeCode($code) { $this->motorbikeCode = $code; }
    public function setStartDateTime($dateTime) { $this->startDateTime = $dateTime; }
    public function setCostPerHourAtStart($cost) { $this->costPerHourAtStart = $cost; }
    
    public function createRental() {
        // Check if motorbike is available
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM rentals WHERE motorbikeCode = ? AND status = 'ACTIVE'");
        $stmt->bind_param("s", $this->motorbikeCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            return "Motorbike is not available";
        }
        
        // Create rental
        $stmt = $this->conn->prepare("INSERT INTO rentals (userId, motorbikeCode, startDateTime, costPerHourAtStart, status) VALUES (?, ?, ?, ?, 'ACTIVE')");
        $stmt->bind_param("issd", $this->userId, $this->motorbikeCode, $this->startDateTime, $this->costPerHourAtStart);
        
        if ($stmt->execute()) {
            $this->rentalId = $stmt->insert_id;
            return true;
        }
        return "Failed to create rental: " . $stmt->error;
    }
    
    public function returnRental($rentalId) {
        $endDateTime = date('Y-m-d H:i:s');
        
        $stmt = $this->conn->prepare("UPDATE rentals SET endDateTime = ?, status = 'COMPLETED' WHERE rentalId = ? AND status = 'ACTIVE'");
        $stmt->bind_param("si", $endDateTime, $rentalId);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $this->endDateTime = $endDateTime;
            return true;
        }
        return "Failed to return rental";
    }
    
    public function getRentalById($rentalId) {
        $stmt = $this->conn->prepare("SELECT * FROM rentals WHERE rentalId = ?");
        $stmt->bind_param("i", $rentalId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $this->rentalId = $data['rentalId'];
            $this->userId = $data['userId'];
            $this->motorbikeCode = $data['motorbikeCode'];
            $this->startDateTime = $data['startDateTime'];
            $this->endDateTime = $data['endDateTime'];
            $this->costPerHourAtStart = $data['costPerHourAtStart'];
            $this->status = $data['status'];
            return $this;
        }
        return null;
    }
    
    public function getActiveRentalsByUser($userId) {
        $stmt = $this->conn->prepare("SELECT r.*, m.description, m.rentingLocation 
                                       FROM rentals r 
                                       INNER JOIN motorbikes m ON r.motorbikeCode = m.code 
                                       WHERE r.userId = ? AND r.status = 'ACTIVE' 
                                       ORDER BY r.startDateTime DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getCompletedRentalsByUser($userId) {
        $stmt = $this->conn->prepare("SELECT r.*, m.description, m.rentingLocation 
                                       FROM rentals r 
                                       INNER JOIN motorbikes m ON r.motorbikeCode = m.code 
                                       WHERE r.userId = ? AND r.status = 'COMPLETED' 
                                       ORDER BY r.endDateTime DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAllActiveRentals() {
        $sql = "SELECT r.*, m.description, m.rentingLocation, u.name, u.surname, u.email 
                FROM rentals r 
                INNER JOIN motorbikes m ON r.motorbikeCode = m.code 
                INNER JOIN users u ON r.userId = u.id 
                WHERE r.status = 'ACTIVE' 
                ORDER BY r.startDateTime DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function calculateTotalCost($startDateTime, $endDateTime, $costPerHour) {
        $start = new DateTime($startDateTime);
        $end = new DateTime($endDateTime);
        $interval = $start->diff($end);
        
        // Calculate total hours including minutes
        $hours = $interval->h + ($interval->days * 24) + ($interval->i / 60);
        
        // Minimum charge: 1 hour (even if rented for less time)
        if ($hours < 1) {
            $hours = 1;
        }
        
        $totalCost = $hours * $costPerHour;
        
        return round($totalCost, 2);
    }
}
?>
