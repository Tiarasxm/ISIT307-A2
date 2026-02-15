<?php
/**
 * Motorbike class - Handles motorbike operations
 */
class Motorbike {
    private $conn;
    private $code;
    private $rentingLocation;
    private $description;
    private $costPerHour;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
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
    
    public function create() {
        $stmt = $this->conn->prepare("SELECT code FROM motorbikes WHERE code = ?");
        $stmt->bind_param("s", $this->code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return "Motorbike code already exists";
        }
        
        $stmt = $this->conn->prepare("INSERT INTO motorbikes (code, rentingLocation, description, costPerHour) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $this->code, $this->rentingLocation, $this->description, $this->costPerHour);
        
        if ($stmt->execute()) {
            return true;
        }
        return "Failed to create motorbike: " . $stmt->error;
    }
    
    public function update() {
        $stmt = $this->conn->prepare("UPDATE motorbikes SET rentingLocation = ?, description = ?, costPerHour = ? WHERE code = ?");
        $stmt->bind_param("ssds", $this->rentingLocation, $this->description, $this->costPerHour, $this->code);
        
        if ($stmt->execute()) {
            return true;
        }
        return "Failed to update motorbike: " . $stmt->error;
    }
    
    public function getByCode($code) {
        $stmt = $this->conn->prepare("SELECT * FROM motorbikes WHERE code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $this->code = $data['code'];
            $this->rentingLocation = $data['rentingLocation'];
            $this->description = $data['description'];
            $this->costPerHour = $data['costPerHour'];
            return $this;
        }
        return null;
    }
    
    public function getAllMotorbikes() {
        $sql = "SELECT * FROM motorbikes ORDER BY code";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAvailableMotorbikes() {
        $sql = "SELECT * FROM motorbikes WHERE code NOT IN (SELECT motorbikeCode FROM rentals WHERE status = 'ACTIVE') ORDER BY code";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getRentedMotorbikes() {
        $sql = "SELECT DISTINCT m.* FROM motorbikes m INNER JOIN rentals r ON m.code = r.motorbikeCode WHERE r.status = 'ACTIVE' ORDER BY m.code";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function searchMotorbikes($code = '', $location = '', $description = '') {
        $sql = "SELECT * FROM motorbikes WHERE 1=1";
        $params = [];
        $types = '';
        
        if (!empty($code)) {
            $sql .= " AND code LIKE ?";
            $params[] = "%$code%";
            $types .= 's';
        }
        if (!empty($location)) {
            $sql .= " AND rentingLocation LIKE ?";
            $params[] = "%$location%";
            $types .= 's';
        }
        if (!empty($description)) {
            $sql .= " AND description LIKE ?";
            $params[] = "%$description%";
            $types .= 's';
        }
        
        $sql .= " ORDER BY code";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function isAvailable($code) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM rentals WHERE motorbikeCode = ? AND status = 'ACTIVE'");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] == 0;
    }
}
?>
