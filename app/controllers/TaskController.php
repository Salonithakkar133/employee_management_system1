<?php
require_once 'Controller.php';
require_once 'app/models/Task.php';
class TaskController extends Controller {
    public function __construct($db = null) {
        parent::__construct($db);
        require_once 'app/models/Task.php';
        $this->models['task'] = new Task($this->db);
    }
    public function add() {
        $this->requireRoles(['admin', 'team_leader']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskData = [
                'title' => $this->sanitize($_POST['title']),
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'status' => $this->sanitize($_POST['status']),
                'assigned_to' => $this->sanitize($_POST['assigned_to'] ?: null),
                'created_by' => $_SESSION['id'],
                'start_date' => $this->sanitize($_POST['start_date']),
                'end_date' => $this->sanitize($_POST['end_date'])
            ];

            if (!$this->validateDates($taskData['start_date'], $taskData['end_date'])) {
                $this->view('tasks/add', [
                    'users' => $this->models['user']->getAllUsers(),
                    'message' => 'End date must be after start date'
                ]);
                return;
            }

            try {
                if ($this->models['task']->create($taskData)) {
                    $this->redirect('index.php?page=tasks', 'Task added successfully');
                } else {
                    $this->view('tasks/add', [
                        'users' => $this->models['user']->getAllUsers(),
                        'message' => 'Failed to add task'
                    ]);
                }
            } catch (Exception $e) {
                $this->view('tasks/add', [
                    'users' => $this->models['user']->getAllUsers(),
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            $this->view('tasks/add', [
                'users' => $this->models['user']->getAllUsers()
            ]);
        }
    }
    public function edit() {
        $this->requireRoles(['admin', 'team_leader', 'employee']);
        $task_id = $_GET['id'] ?? null;
        $task = $this->models['task']->getTaskById($task_id);
        $message = '';

        if (!$task) {
            $this->redirect('index.php?page=tasks', 'Task not found', true);
        }

        if ($_SESSION['role'] === 'employee' && $task['assigned_to'] != $_SESSION['id']) {
            $this->redirect('index.php?page=tasks', 'Unauthorized access', true);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_SESSION['role'] === 'employee') {
                $this->models['task']->id = $task_id;
                $this->models['task']->status = $this->sanitize($_POST['status']);
                $success = $this->models['task']->updateStatusOnly();
                $message = $success ? "Status updated successfully" : "Failed to update status";
            } else {
                $taskData = [
                    'id' => $task_id,
                    'title' => $this->sanitize($_POST['title']),
                    'description' => $this->sanitize($_POST['description'] ?? ''),
                    'status' => $this->sanitize($_POST['status']),
                    'assigned_to' => $this->sanitize($_POST['assigned_to'] ?: null),
                    'start_date' => $this->sanitize($_POST['start_date']),
                    'end_date' => $this->sanitize($_POST['end_date'])
                ];

                if (!$this->validateDates($taskData['start_date'], $taskData['end_date'])) {
                    $message = "End date must be after start date";
                } else {
                    $this->models['task']->id = $taskData['id'];
                    $this->models['task']->title = $taskData['title'];
                    $this->models['task']->description = $taskData['description'];
                    $this->models['task']->status = $taskData['status'];
                    $this->models['task']->assigned_to = $taskData['assigned_to'];
                    $this->models['task']->start_date = $taskData['start_date'];
                    $this->models['task']->end_date = $taskData['end_date'];
                    $success = $this->models['task']->update($taskData);
                    $message = $success ? "Task updated successfully" : "Failed to update task";
                }
            }
            if ($success) {
                $this->redirect('index.php?page=tasks', $message);
            }
        }

        $this->view('tasks/edit', [
            'task' => $task,
            'users' => $this->models['user']->getAllUsers(),
            'message' => $message
        ]);
    }

    protected function validateDates($start, $end) {
        if (empty($start) || empty($end)) {
            return false;
        }
        return strtotime($end) >= strtotime($start);
    }
    public function list() {
        $this->requireRoles(['admin', 'team_leader', 'employee']);
        
        $tasks = $this->models['task']->getTasksByAccess(
            $_SESSION['id'],
            $_SESSION['role']
        );
        
        $this->view('tasks/list', ['tasks' => $tasks]);
    }
    public function delete() {
        $this->requireRoles(['admin', 'team_leader']);
        
        $task_id = $_GET['id'] ?? null;
        if ($this->models['task']->softDelete($task_id)) {
            header("Location: index.php?page=tasks&message=Task+deleted+successfully");
            } else {
            header("Location: index.php?page=tasks&message=Failed+to+delete+task");
    }
    }
    public function restore() {
        $this->requireRole('admin');
        $task_id = $_GET['id'] ?? null;
        if ($this->models['task']->restore($task_id)) {
        header("Location: index.php?page=tasks&message=Task+restored+successfully");
        } else {
        header("Location: index.php?page=tasks&message=Failed+to+restore+task");
        }
    }




public function view_task() {
    $this->requireRoles(['admin', 'team_leader', 'employee']);
    $task_id = $_GET['id'] ?? null;
    $task = $this->models['task']->getTaskById($task_id);

    if (!$task) {
        $this->redirect('index.php?page=tasks', 'Task not found', true);
    }

    if ($_SESSION['role'] === 'employee' && $task['assigned_to'] != $_SESSION['id']) {
        $this->redirect('index.php?page=tasks', 'Unauthorized access', true);
    }

    $this->view('tasks/view', ['task' => $task]);
}
}