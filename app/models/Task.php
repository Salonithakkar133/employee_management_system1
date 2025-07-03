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
        $query = "SELECT t.*, u.name AS user_assigned_name, c.name AS user_created_name, t.assigned_to AS assigned_to_id
        FROM " . $this->table_name . " t 
        LEFT JOIN users u ON t.assigned_to = u.id 
        LEFT JOIN users c ON t.created_by = c.id 
        WHERE t.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($task) {
            $task['assigned_to'] = $task['assigned_to'] ?? 'Unassigned';
            $task['created_by'] = $task['created_by'] ?? 'Unknown';
        }
        
        return $task;
}
public function getTasksByAccess($user_id, $role, $user_search = null, $title_search = null,$start_date = null, $end_date = null) {
        $query = "SELECT 
        t.*, 
        a.name AS assigned_to, 
        c.name AS created_by,
        t.assigned_to AS assigned_to_id
        FROM tasks t 
        LEFT JOIN users a ON t.assigned_to = a.id 
        LEFT JOIN users c ON t.created_by = c.id";
        $params = [];
        $conditions = [];
        
        if ($role === 'employee') {
            $conditions[] = "t.assigned_to = :user_id";
            $conditions[] = "t.is_deleted = 0";
            $params[':user_id'] = $user_id;
        } elseif ($role === 'admin') {
        } else {
            $conditions[] = "t.is_deleted = 0";
        }
        if (in_array($role, ['admin', 'team_leader'])) {
            if ($user_search !== null && $user_search !== '') {
                $conditions[] = "(t.assigned_to = :user_search OR t.created_by = :user_search)";
                $params[':user_search'] = $user_search;
            }
            if ($title_search !== null && $title_search !== '') {
                $conditions[] = "(t.title LIKE :title_search OR t.description LIKE :title_search)";
                $params[':title_search'] = '%' . $title_search . '%';
            }
            if ($start_date !== null && $start_date !== '') {
                $conditions[] = "t.start_date >= :start_date";
                $params[':start_date'] = $start_date;
            }
            if ($end_date !== null && $end_date !== '') {
                $conditions[] = "t.end_date <= :end_date";
                $params[':end_date'] = $end_date;
            }
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tasks as &$task) {
            $task['assigned_to'] = $task['assigned_to'] ?? 'Unassigned';
            $task['created_by'] = $task['created_by'] ?? 'Unknown';
        }
        unset($task);
        $stmt->execute();
        return $tasks;
    
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
