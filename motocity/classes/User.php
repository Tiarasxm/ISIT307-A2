<?php
/**
 * User class - Handles user data and operations
 * Fields: id, name, surname, phone, email, type, password
 */
class User {
    private $id;
    private $name;
    private $surname;
    private $phone;
    private $email;
    private $type;
    private $password;
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getSurname() { return $this->surname; }
    public function getPhone() { return $this->phone; }
    public function getEmail() { return $this->email; }
    public function getType() { return $this->type; }
    
    // Setters
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setSurname($surname) { $this->surname = $surname; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setEmail($email) { $this->email = $email; }
    public function setType($type) { $this->type = $type; }
    public function setPassword($password) { $this->password = $password; }
    
    /**
     * Hash password based on configured method
     * @param string $password Plain text password
     * @return string Hashed password
     */
    public function hashPassword($password) {
        if (HASH_METHOD === 'md5') {
            // For lab compatibility - use md5
            return md5($password);
        } else {
            // Recommended - use password_hash
            return password_hash($password, PASSWORD_DEFAULT);
        }
    }
    
    /**
     * Verify password based on configured method
     * @param string $password Plain text password
     * @param string $hash Stored hash
     * @return bool
     */
    public function verifyPassword($password, $hash) {
        if (HASH_METHOD === 'md5') {
            return md5($password) === $hash;
        } else {
            return password_verify($password, $hash);
        }
    }
    
    /**
     * Register a new user
     * @return bool|string True on success, error message on failure
     */
    public function register() {
        try {
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$this->email]);
            if ($stmt->fetch()) {
                return "Email already registered";
            }
            
            // Insert new user
            $stmt = $this->db->prepare(
                "INSERT INTO users (name, surname, phone, email, type, password) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $hashedPassword = $this->hashPassword($this->password);
            $stmt->execute([
                $this->name,
                $this->surname,
                $this->phone,
                $this->email,
                $this->type,
                $hashedPassword
            ]);
            
            $this->id = $this->db->lastInsertId();
            return true;
        } catch (PDOException $e) {
            return "Registration failed: " . $e->getMessage();
        }
    }
    
    /**
     * Login user by email and password
     * @param string $email
     * @param string $password
     * @return User|string User object on success, error message on failure
     */
    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $userData = $stmt->fetch();
            
            if (!$userData) {
                return "Invalid email or password";
            }
            
            if (!$this->verifyPassword($password, $userData['password'])) {
                return "Invalid email or password";
            }
            
            // Set user properties
            $this->id = $userData['id'];
            $this->name = $userData['name'];
            $this->surname = $userData['surname'];
            $this->phone = $userData['phone'];
            $this->email = $userData['email'];
            $this->type = $userData['type'];
            
            return $this;
        } catch (PDOException $e) {
            return "Login failed: " . $e->getMessage();
        }
    }
    
    /**
     * Get user by ID
     * @param int $id
     * @return User|null
     */
    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $userData = $stmt->fetch();
            
            if ($userData) {
                $this->id = $userData['id'];
                $this->name = $userData['name'];
                $this->surname = $userData['surname'];
                $this->phone = $userData['phone'];
                $this->email = $userData['email'];
                $this->type = $userData['type'];
                return $this;
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Get all users
     * @return array
     */
    public function getAllUsers() {
        try {
            $stmt = $this->db->query("SELECT id, name, surname, phone, email, type FROM users ORDER BY name");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get users currently renting motorbikes
     * @return array
     */
    public function getUsersCurrentlyRenting() {
        try {
            $stmt = $this->db->query(
                "SELECT DISTINCT u.id, u.name, u.surname, u.phone, u.email, u.type 
                 FROM users u
                 INNER JOIN rentals r ON u.id = r.userId
                 WHERE r.status = 'ACTIVE'
                 ORDER BY u.name"
            );
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Search users by name, surname, phone, or email (partial match)
     * @param string $name
     * @param string $surname
     * @param string $phone
     * @param string $email
     * @return array
     */
    public function searchUsers($name = '', $surname = '', $phone = '', $email = '') {
        try {
            $sql = "SELECT id, name, surname, phone, email, type FROM users WHERE 1=1";
            $params = [];
            
            if (!empty($name)) {
                $sql .= " AND name LIKE ?";
                $params[] = "%$name%";
            }
            if (!empty($surname)) {
                $sql .= " AND surname LIKE ?";
                $params[] = "%$surname%";
            }
            if (!empty($phone)) {
                $sql .= " AND phone LIKE ?";
                $params[] = "%$phone%";
            }
            if (!empty($email)) {
                $sql .= " AND email LIKE ?";
                $params[] = "%$email%";
            }
            
            $sql .= " ORDER BY name";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
