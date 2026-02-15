<?php
/**
 * User class - Handles user operations
 */
class User {
    private $conn;
    private $id;
    private $name;
    private $surname;
    private $phone;
    private $email;
    private $type;
    private $password;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getSurname() { return $this->surname; }
    public function getPhone() { return $this->phone; }
    public function getEmail() { return $this->email; }
    public function getType() { return $this->type; }
    
    // Setters
    public function setName($name) { $this->name = $name; }
    public function setSurname($surname) { $this->surname = $surname; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setEmail($email) { $this->email = $email; }
    public function setType($type) { $this->type = $type; }
    public function setPassword($password) { $this->password = $password; }
    
    public function register() {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return "Email already registered";
        }
        
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (name, surname, phone, email, type, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $this->name, $this->surname, $this->phone, $this->email, $this->type, $hashedPassword);
        
        if ($stmt->execute()) {
            return true;
        }
        return "Registration failed: " . $stmt->error;
    }
    
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return "Invalid email or password";
        }
        
        $userData = $result->fetch_assoc();
        
        if (!password_verify($password, $userData['password'])) {
            return "Invalid email or password";
        }
        
        $this->id = $userData['id'];
        $this->name = $userData['name'];
        $this->surname = $userData['surname'];
        $this->phone = $userData['phone'];
        $this->email = $userData['email'];
        $this->type = $userData['type'];
        
        return $this;
    }
    
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
            $this->id = $userData['id'];
            $this->name = $userData['name'];
            $this->surname = $userData['surname'];
            $this->phone = $userData['phone'];
            $this->email = $userData['email'];
            $this->type = $userData['type'];
            return $this;
        }
        return null;
    }
    
    public function getAllUsers() {
        $sql = "SELECT id, name, surname, phone, email, type FROM users ORDER BY name";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function searchUsers($name = '', $surname = '', $phone = '', $email = '') {
        $sql = "SELECT id, name, surname, phone, email, type FROM users WHERE 1=1";
        $params = [];
        $types = '';
        
        if (!empty($name)) {
            $sql .= " AND name LIKE ?";
            $params[] = "%$name%";
            $types .= 's';
        }
        if (!empty($surname)) {
            $sql .= " AND surname LIKE ?";
            $params[] = "%$surname%";
            $types .= 's';
        }
        if (!empty($phone)) {
            $sql .= " AND phone LIKE ?";
            $params[] = "%$phone%";
            $types .= 's';
        }
        if (!empty($email)) {
            $sql .= " AND email LIKE ?";
            $params[] = "%$email%";
            $types .= 's';
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getUsersCurrentlyRenting() {
        $sql = "SELECT DISTINCT u.id, u.name, u.surname, u.phone, u.email, u.type 
                FROM users u 
                INNER JOIN rentals r ON u.id = r.userId 
                WHERE r.status = 'ACTIVE'
                ORDER BY u.name";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
