<?php
class Task {
    private $conn;
    private $table_name = "tasks";
    public $id;
    public $title;
    public $description;
    public $status;
    public $assigned_to;
    public $created_by;
    public $start_date;
    public $end_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function clean($value) {
        return htmlspecialchars(strip_tags($value));
    }

    private function bindCommonFields($stmt) {
        $this->title = $this->clean($this->title);
        $this->description = $this->clean($this->description);
        $this->status = $this->clean($this->status);
        $this->assigned_to = $this->assigned_to ? $this->clean($this->assigned_to) : null;
        $this->start_date = $this->clean($this->start_date);
        $this->end_date = $this->clean($this->end_date);

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":assigned_to", $this->assigned_to, PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
    }

    public function create(array $taskData): mixed {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$taskData['created_by']]);
        
        if (!$stmt->fetch()) {
            throw new Exception("Invalid user ID: User doesn't exist");
        }

        $query = "INSERT INTO tasks (title, description, status, assigned_to, created_by, start_date, end_date)
        VALUES (:title, :description, :status, :assigned_to, :created_by, :start_date, :end_date)";
        $stmt = $this->conn->prepare($query);

        // Set class properties from taskData 
        $this->title = $taskData['title'];
        $this->description = $taskData['description'] ?? '';
        $this->status = $taskData['status'];
        $this->assigned_to = $taskData['assigned_to'] ?: null;
        $this->created_by = $taskData['created_by'];
        $this->start_date = $taskData['start_date'];
        $this->end_date = $taskData['end_date'];

        $this->bindCommonFields($stmt);
        $stmt->bindParam(":created_by", $this->created_by, PDO::PARAM_INT);
        // print_r($taskData); // Uncomment for debugging
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE tasks SET title = :title, description = :description, status = :status, assigned_to = :assigned_to,
        start_date = :start_date, end_date = :end_date WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = $this->clean($this->id);
        $this->bindCommonFields($stmt);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ... (other methods unchanged)

    public function updateStatusOnly() {
        $query = "UPDATE tasks SET status = :status WHERE id = :id AND assigned_to = :assigned_to";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":assigned_to", $_SESSION['id']);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
    public function getTaskById($id) {
        $query = "SELECT t.*, u.name AS assigned_to, c.name AS created_by, t.assigned_to AS assigned_to
        FROM " . $this->table_name . " t 
        LEFT JOIN users u ON t.assigned_to = u.id 
        LEFT JOIN users c ON t.created_by = c.id 
        WHERE t.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        return $task;
    }

    // Combined method: replaces readAll() and readByUser()
public function getTasksByAccess($user_id, $role) {
        $query = "SELECT 
            t.*, 
            a.name AS assigned_to, 
            c.name AS created_by,
            t.assigned_to AS assigned_to
        FROM tasks t 
        LEFT JOIN users a ON t.assigned_to = a.id 
        LEFT JOIN users c ON t.created_by = c.id";
        
        if ($role === 'employee') {
            $query .= " WHERE t.assigned_to = :user_id AND t.is_deleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        } elseif ($role === 'admin') {
            $query .= " WHERE 1=1";
            $stmt = $this->conn->prepare($query);
        } else {
            $query .= " WHERE t.is_deleted = 0";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->execute(); // Reset cursor for view
        return $stmt;
}

    public function getAllTasks() {
        $query = "SELECT * FROM tasks WHERE is_deleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getDeletedTasks() {
        $query = "SELECT t.*, u.name as assigned_to, c.name as created_by 
        FROM tasks t 
        LEFT JOIN users u ON t.assigned_to = u.id 
        LEFT JOIN users c ON t.created_by = c.id 
        WHERE t.is_deleted = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function softDelete($id) {
        $query = "UPDATE tasks SET is_deleted = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function restore($task_id) {
        $query = "UPDATE tasks SET is_deleted = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $task_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
