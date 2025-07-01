<?php
class User {
    private $conn;
    private $table_name = "users";
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function clean($value) {
        return htmlspecialchars(strip_tags($value));
    }

    private function bindCommonFields($stmt, $includePassword = true) {
        $this->name = $this->clean($this->name);
        $this->email = $this->clean($this->email);
        $this->role = $this->clean($this->role);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":role", $this->role);

        if ($includePassword) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
            $stmt->bindParam(":password", $this->password);
        }
    }
public function register() {
        $query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);
        $this->role = 'pending';

        try {
            $this->bindCommonFields($stmt);
            $result = $stmt->execute();
            return $result;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return "Email is already registered";
            }
            return "Database error: " . $e->getMessage();
        } catch (Exception $e) {
            return "Validation error: " . $e->getMessage();
        }
    }

    public function login($email) {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);

        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
    public function getAllUsers() {
        $query = "SELECT * FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
 public function getUserById($id) {
    try {
        $query = "SELECT id, name, email, role, profile_image FROM users WHERE id = :id AND is_deleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: false;
    } catch (PDOException $e) {
        return false;
    }
}
    public function add() {
        $query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);
        $this->bindCommonFields($stmt);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return $e->getCode() == 23000 ? "Email is already registered" : false;
        }
    }
    public function update($data): mixed {
    // Validate input data
    if (!isset($data['id']) || !is_numeric($data['id']) || $data['id'] <= 0) {
        return "Invalid user ID";
    }

    // Initialize query and parameters
    $query = "UPDATE users SET name = :name, email = :email";
    $params = [
        ':name' => $this->clean($data['name'] ?? ''),
        ':email' => $this->clean($data['email'] ?? ''),
        ':id' => (int) $data['id']
    ];

    // Handle optional fields
    if (!empty($data['password'])) {
        if (strlen($data['password']) < 8) {
            return "Password must be at least 8 characters long";
        }
        $query .= ", password = :password";
        $params[':password'] = password_hash($this->clean($data['password']), PASSWORD_DEFAULT);
    }

    if (!empty($data['profile_image'])) {
        $query .= ", profile_image = :profile_image";
        $params[':profile_image'] = $this->clean($data['profile_image']);
    }

    if (!empty($data['role'])) {
        $validRoles = ['employee', 'team_leader', 'admin', 'pending'];
        if (!in_array($data['role'], $validRoles)) {
            return "Invalid role provided";
        }
        $query .= ", role = :role";
        $params[':role'] = $this->clean($data['role']);
    }

    $query .= " WHERE id = :id";

    try {


        // Verify user exists before update
        $checkStmt = $this->conn->prepare("SELECT id FROM users WHERE id = :id");
        $checkStmt->bindParam(':id', $params[':id'], PDO::PARAM_INT);
        $checkStmt->execute();
        if ($checkStmt->rowCount() === 0) {
            return "User not found";
        }

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => &$value) {
            if ($key === ':id') {
                $stmt->bindParam($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindParam($key, $value);
            }
        }

        // Execute with transaction for reliability
        $this->conn->beginTransaction();
        $stmt->execute();
        $rowCount = $stmt->rowCount();
       

        if ($rowCount === 0) {
            
            $this->conn->rollBack();
            return "No changes applied";
        }

        $this->conn->commit();
        return true;
    } catch (PDOException $e) {
        $this->conn->rollBack();
        
        return $e->getCode() == 23000 ? "Email is already registered" : "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $this->conn->rollBack();
    
        return "Error: " . $e->getMessage();
    }
}
    public function getAllActiveUsers() {
        $query = "SELECT * FROM users WHERE is_deleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function updateRole($id, $role) {
        $query = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
    public function restoreUser($id) {
        $query = "UPDATE users SET is_deleted = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function getNonAdminUsers() {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE role != 'admin'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
