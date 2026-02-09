<?php
/**
 * Motorbike class - Handles motorbike data and operations
 * Fields: code, rentingLocation, description, costPerHour
 */
class Motorbike {
    private $code;
    private $rentingLocation;
    private $description;
    private $costPerHour;
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Getters
    public function getCode() { return $this->code; }
    public function getRentingLocation() { return $this->rentingLocation; }
    public function getDescription() { return $this->description; }
    public function getCostPerHour() { return $this->costPerHour; }
    
    // Setters
    public function setCode($code) { $this->code = $code; }
    public function setRentingLocation($location) { $this->rentingLocation = $location; }
    public function setDescription($description) { $this->description = $description; }
    public function setCostPerHour($cost) { $this->costPerHour = $cost; }
    
    /**
     * Insert a new motorbike
     * @return bool|string True on success, error message on failure
     */
    public function insert() {
        try {
            // Check if code already exists
            $stmt = $this->db->prepare("SELECT code FROM motorbikes WHERE code = ?");
            $stmt->execute([$this->code]);
            if ($stmt->fetch()) {
                return "Motorbike code already exists";
            }
            
            $stmt = $this->db->prepare(
                "INSERT INTO motorbikes (code, rentingLocation, description, costPerHour) 
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $this->code,
                $this->rentingLocation,
                $this->description,
                $this->costPerHour
            ]);
            
            return true;
        } catch (PDOException $e) {
            return "Insert failed: " . $e->getMessage();
        }
    }
    
    /**
     * Update an existing motorbike
     * @param string $oldCode Original code (in case code is being changed)
     * @return bool|string True on success, error message on failure
     */
    public function update($oldCode) {
        try {
            // If code is being changed, check if new code already exists
            if ($this->code !== $oldCode) {
                $stmt = $this->db->prepare("SELECT code FROM motorbikes WHERE code = ?");
                $stmt->execute([$this->code]);
                if ($stmt->fetch()) {
                    return "New motorbike code already exists";
                }
            }
            
            $stmt = $this->db->prepare(
                "UPDATE motorbikes 
                 SET code = ?, rentingLocation = ?, description = ?, costPerHour = ? 
                 WHERE code = ?"
            );
            $stmt->execute([
                $this->code,
                $this->rentingLocation,
                $this->description,
                $this->costPerHour,
                $oldCode
            ]);
            
            return true;
        } catch (PDOException $e) {
            return "Update failed: " . $e->getMessage();
        }
    }
    
    /**
     * Get motorbike by code
     * @param string $code
     * @return Motorbike|null
     */
    public function getMotorbikeByCode($code) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM motorbikes WHERE code = ?");
            $stmt->execute([$code]);
            $data = $stmt->fetch();
            
            if ($data) {
                $this->code = $data['code'];
                $this->rentingLocation = $data['rentingLocation'];
                $this->description = $data['description'];
                $this->costPerHour = $data['costPerHour'];
                return $this;
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Get all motorbikes
     * @return array
     */
    public function getAllMotorbikes() {
        try {
            $stmt = $this->db->query("SELECT * FROM motorbikes ORDER BY code");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get available motorbikes (not currently rented)
     * @return array
     */
    public function getAvailableMotorbikes() {
        try {
            $stmt = $this->db->query(
                "SELECT m.* FROM motorbikes m
                 WHERE m.code NOT IN (
                     SELECT motorbikeCode FROM rentals WHERE status = 'ACTIVE'
                 )
                 ORDER BY m.code"
            );
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get currently rented motorbikes
     * @return array
     */
    public function getRentedMotorbikes() {
        try {
            $stmt = $this->db->query(
                "SELECT DISTINCT m.* FROM motorbikes m
                 INNER JOIN rentals r ON m.code = r.motorbikeCode
                 WHERE r.status = 'ACTIVE'
                 ORDER BY m.code"
            );
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Check if a motorbike is available (not currently rented)
     * @param string $code
     * @return bool
     */
    public function isAvailable($code) {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM rentals 
                 WHERE motorbikeCode = ? AND status = 'ACTIVE'"
            );
            $stmt->execute([$code]);
            $result = $stmt->fetch();
            return $result['count'] == 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Search motorbikes by code, location, or description (partial match)
     * @param string $code
     * @param string $location
     * @param string $description
     * @return array
     */
    public function searchMotorbikes($code = '', $location = '', $description = '') {
        try {
            $sql = "SELECT * FROM motorbikes WHERE 1=1";
            $params = [];
            
            if (!empty($code)) {
                $sql .= " AND code LIKE ?";
                $params[] = "%$code%";
            }
            if (!empty($location)) {
                $sql .= " AND rentingLocation LIKE ?";
                $params[] = "%$location%";
            }
            if (!empty($description)) {
                $sql .= " AND description LIKE ?";
                $params[] = "%$description%";
            }
            
            $sql .= " ORDER BY code";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Search available motorbikes by code, location, or description (partial match)
     * @param string $code
     * @param string $location
     * @param string $description
     * @return array
     */
    public function searchAvailableMotorbikes($code = '', $location = '', $description = '') {
        try {
            $sql = "SELECT m.* FROM motorbikes m
                    WHERE m.code NOT IN (
                        SELECT motorbikeCode FROM rentals WHERE status = 'ACTIVE'
                    )";
            $params = [];
            
            if (!empty($code)) {
                $sql .= " AND m.code LIKE ?";
                $params[] = "%$code%";
            }
            if (!empty($location)) {
                $sql .= " AND m.rentingLocation LIKE ?";
                $params[] = "%$location%";
            }
            if (!empty($description)) {
                $sql .= " AND m.description LIKE ?";
                $params[] = "%$description%";
            }
            
            $sql .= " ORDER BY m.code";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
