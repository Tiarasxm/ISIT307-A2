<?php
/**
 * Rental class - Handles rental transactions and operations
 * Fields: rentalId, userId, motorbikeCode, startDateTime, endDateTime, costPerHourAtStart, status
 */
class Rental {
    private $rentalId;
    private $userId;
    private $motorbikeCode;
    private $startDateTime;
    private $endDateTime;
    private $costPerHourAtStart;
    private $status;
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
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
    public function setRentalId($id) { $this->rentalId = $id; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setMotorbikeCode($code) { $this->motorbikeCode = $code; }
    public function setStartDateTime($dateTime) { $this->startDateTime = $dateTime; }
    public function setEndDateTime($dateTime) { $this->endDateTime = $dateTime; }
    public function setCostPerHourAtStart($cost) { $this->costPerHourAtStart = $cost; }
    public function setStatus($status) { $this->status = $status; }
    
    /**
     * Create a new rental (rent a motorbike)
     * @return bool|string True on success, error message on failure
     */
    public function createRental() {
        try {
            // Check if motorbike is available
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM rentals 
                 WHERE motorbikeCode = ? AND status = 'ACTIVE'"
            );
            $stmt->execute([$this->motorbikeCode]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                return "Motorbike is already rented";
            }
            
            // Insert rental
            $stmt = $this->db->prepare(
                "INSERT INTO rentals (userId, motorbikeCode, startDateTime, costPerHourAtStart, status) 
                 VALUES (?, ?, ?, ?, 'ACTIVE')"
            );
            $stmt->execute([
                $this->userId,
                $this->motorbikeCode,
                $this->startDateTime,
                $this->costPerHourAtStart
            ]);
            
            $this->rentalId = $this->db->lastInsertId();
            $this->status = 'ACTIVE';
            return true;
        } catch (PDOException $e) {
            return "Rental creation failed: " . $e->getMessage();
        }
    }
    
    /**
     * Complete a rental (return a motorbike)
     * @param int $rentalId
     * @param string $endDateTime
     * @return bool|string True on success, error message on failure
     */
    public function completeRental($rentalId, $endDateTime) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE rentals 
                 SET endDateTime = ?, status = 'COMPLETED' 
                 WHERE rentalId = ? AND status = 'ACTIVE'"
            );
            $stmt->execute([$endDateTime, $rentalId]);
            
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return "Rental not found or already completed";
            }
        } catch (PDOException $e) {
            return "Return failed: " . $e->getMessage();
        }
    }
    
    /**
     * Get rental by ID
     * @param int $rentalId
     * @return array|null
     */
    public function getRentalById($rentalId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT r.*, u.name, u.surname, u.email, m.rentingLocation, m.description
                 FROM rentals r
                 INNER JOIN users u ON r.userId = u.id
                 INNER JOIN motorbikes m ON r.motorbikeCode = m.code
                 WHERE r.rentalId = ?"
            );
            $stmt->execute([$rentalId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Get active rentals for a user
     * @param int $userId
     * @return array
     */
    public function getActiveRentalsByUser($userId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT r.*, m.rentingLocation, m.description
                 FROM rentals r
                 INNER JOIN motorbikes m ON r.motorbikeCode = m.code
                 WHERE r.userId = ? AND r.status = 'ACTIVE'
                 ORDER BY r.startDateTime DESC"
            );
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get all active rentals (for admin)
     * @return array
     */
    public function getAllActiveRentals() {
        try {
            $stmt = $this->db->query(
                "SELECT r.*, u.name, u.surname, u.email, m.rentingLocation, m.description
                 FROM rentals r
                 INNER JOIN users u ON r.userId = u.id
                 INNER JOIN motorbikes m ON r.motorbikeCode = m.code
                 WHERE r.status = 'ACTIVE'
                 ORDER BY r.startDateTime DESC"
            );
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get completed rentals for a user (rental history)
     * @param int $userId
     * @return array
     */
    public function getCompletedRentalsByUser($userId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT r.*, m.rentingLocation, m.description
                 FROM rentals r
                 INNER JOIN motorbikes m ON r.motorbikeCode = m.code
                 WHERE r.userId = ? AND r.status = 'COMPLETED'
                 ORDER BY r.endDateTime DESC"
            );
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get all completed rentals (for admin)
     * @return array
     */
    public function getAllCompletedRentals() {
        try {
            $stmt = $this->db->query(
                "SELECT r.*, u.name, u.surname, u.email, m.rentingLocation, m.description
                 FROM rentals r
                 INNER JOIN users u ON r.userId = u.id
                 INNER JOIN motorbikes m ON r.motorbikeCode = m.code
                 WHERE r.status = 'COMPLETED'
                 ORDER BY r.endDateTime DESC"
            );
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Calculate total cost for a rental
     * @param string $startDateTime
     * @param string $endDateTime
     * @param float $costPerHour
     * @return float Total cost (rounded to 2 decimals)
     */
    public function calculateTotalCost($startDateTime, $endDateTime, $costPerHour) {
        $start = new DateTime($startDateTime);
        $end = new DateTime($endDateTime);
        $interval = $start->diff($end);
        
        // Calculate total hours (including fractional hours)
        $totalSeconds = ($interval->days * 24 * 3600) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
        $totalHours = $totalSeconds / 3600;
        
        // Calculate cost
        $totalCost = $totalHours * $costPerHour;
        
        // Round to 2 decimal places
        return round($totalCost, 2);
    }
    
    /**
     * Get active rental for a specific motorbike
     * @param string $motorbikeCode
     * @return array|null
     */
    public function getActiveRentalByMotorbike($motorbikeCode) {
        try {
            $stmt = $this->db->prepare(
                "SELECT r.*, u.name, u.surname, u.email
                 FROM rentals r
                 INNER JOIN users u ON r.userId = u.id
                 WHERE r.motorbikeCode = ? AND r.status = 'ACTIVE'"
            );
            $stmt->execute([$motorbikeCode]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
}
?>
