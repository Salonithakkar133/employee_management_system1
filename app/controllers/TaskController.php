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
        // Start output buffering to prevent unintended output
        ob_start();
        
        $this->requireRoles(['admin', 'team_leader']);
        $message = '';
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskData = [
                'title' => $this->sanitize($_POST['title'] ?? ''),
                'description' => $this->sanitize($_POST['description'] ?? ''),
                'status' => $this->sanitize($_POST['status'] ?? ''),
                'assigned_to' => $this->sanitize($_POST['assigned_to'] ?: null),
                'created_by' => $_SESSION['id'],
                'start_date' => $this->sanitize($_POST['start_date'] ?? ''),
                'end_date' => $this->sanitize($_POST['end_date'] ?? '')
            ];

            try {
                // Call create and expect it to return the inserted ID or throw an exception
                $result = $this->models['task']->create($taskData);
                if ($result === false) {
                    $success = false;
                    $message = 'Failed to add task to database';
                } else {
                    $success = true;
                    $taskId = is_numeric($result) ? $result : null;
                    $message = 'Task added successfully';
                }

                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    ob_end_clean();
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'task_id' => $taskId
                    ]);
                    exit;
                }
            } catch (Exception $e) {
                $message = 'Error adding task: ' . $e->getMessage();
                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    ob_end_clean();
                    echo json_encode(['success' => false, 'message' => $message]);
                    exit;
                }
            }

            if ($success && !$this->isAjaxRequest()) {
                $this->redirect('index.php?page=tasks', $message);
            }
        }

        ob_end_clean();
        $this->view('tasks/add', [
            'users' => $this->models['user']->getAllUsers(),
            'message' => $message
        ]);
    }
    public function edit() {
        $this->requireRoles(['admin', 'team_leader', 'employee']);
        $task_id = $_GET['id'] ?? null;
        $task = $this->models['task']->getTaskById($task_id);
        $message = '';
        $success = false;

        if (!$task) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Task not found']);
                exit;
            }
            $this->handleTaskNotFound();
        }

        if ($_SESSION['role'] === 'employee' && $task['assigned_to'] != $_SESSION['id']) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
                exit;
            }
            $this->handleUnauthorizedAccess();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? '';

            if ($_SESSION['role'] === 'employee') {
                $this->models['task']->id = $task_id;
                $this->models['task']->status = $this->sanitize($status);
                $success = $this->models['task']->updateStatusOnly();
                $message = $success ? "Status updated successfully" : "Failed to update status";

                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => $success,
                        'message' => $message
                    ]);
                    exit;
                }
            } else {
                $taskData = [
                    'id' => $task_id,
                    'title' => $this->sanitize($_POST['title'] ?? ''),
                    'description' => $this->sanitize($_POST['description'] ?? ''),
                    'status' => $this->sanitize($status),
                    'assigned_to' => $this->sanitize($_POST['assigned_to'] ?? null),
                    'start_date' => $this->sanitize($_POST['start_date'] ?? ''),
                    'end_date' => $this->sanitize($_POST['end_date'] ?? '')
                ];

                if (!$this->validateDates($taskData['start_date'], $taskData['end_date'])) {
                    $message = "End date must be after start date";
                    if ($this->isAjaxRequest()) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => $message]);
                        exit;
                    }
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

                    if ($this->isAjaxRequest()) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => $success,
                            'message' => $message,
                            'task' => $this->models['task']->getTaskById($task_id)
                        ]);
                        exit;
                    }
                }
            }

            if ($success && !$this->isAjaxRequest()) {
                $this->redirect('index.php?page=tasks', $message);
            }
        }

        $this->view('tasks/edit', [
            'task' => $task,
            'users' => $this->models['user']->getAllUsers(),
            'message' => $message
        ]);
    }

public function list() {
    // Restrict access to authorized roles
    $this->requireRoles(['admin', 'team_leader', 'employee']);
    
    // Initialize search parameters
    $user_search = null;
    $title_search = null;
    $start_date = null;
    $end_date = null;
    $message = '';

    if (in_array($_SESSION['role'], ['admin', 'team_leader'])) {
        $user_search = isset($_GET['user_search']) && $_GET['user_search'] !== '' ? $this->sanitize($_GET['user_search']) : null;
        $title_search = isset($_GET['title_search']) && $_GET['title_search'] !== '' ? $this->sanitize($_GET['title_search']) : null;
        $start_date = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $this->sanitize($_GET['start_date']) : null;
        $end_date = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $this->sanitize($_GET['end_date']) : null;

        if ($start_date && $end_date && !$this->validateDates($start_date, $end_date)) {
            $message = 'End date must be after start date';
            $end_date = null;

            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $message
                ]);
                exit;
            }
        }
    }
    
    try {
        // Fetch tasks based on search criteria
        $tasks = $this->models['task']->getTasksByAccess(
            $_SESSION['id'],
            $_SESSION['role'],
            $user_search,
            $title_search,
            $start_date,
            $end_date
        );

        // Handle AJAX request
        if ($this->isAjaxRequest()) {
        ob_start();
        if (!empty($tasks)) {
            include 'app/views/tasks/list.php'; // Your table partial
        } else {
            echo '<p class="no-results">No tasks found matching your criteria.</p>';
        }
        $html = ob_get_clean();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => empty($tasks) ? 'No tasks found' : count($tasks) . ' tasks found',
            'html' => $html,
            'count' => count($tasks),
            'hasResults' => !empty($tasks) // Add this flag
        ]);
        exit;
    }


        // Normal request handling
        $this->view('tasks/list', [
            'tasks' => $tasks,
            'users' => $this->models['user']->getAllUsers(),
            'user_search' => $user_search,
            'title_search' => $title_search,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'message' => $message
        ]);
        
    } catch (Exception $e) {
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
            exit;
        }
        
        // Handle normal request error
        $_SESSION['error'] = 'Error loading tasks: ' . $e->getMessage();
        header('Location: index.php?page=dashboard');
        exit;
    }
}

    public function delete() {
        $this->requireRoles(['admin', 'team_leader']);
        $task_id = $_GET['id'] ?? $_POST['id'] ?? null;

        if (!$task_id || !is_numeric($task_id)) {
            $message = 'Invalid task ID';
            if ($this->isAjaxRequest()) {
                $this->ajaxResponse(false, $message);
            }
            $this->redirect('index.php?page=tasks', $message, true);
        }

        $success = $this->models['task']->softDelete($task_id);
        $message = $success ? 'Task deleted successfully' : 'Failed to delete task';

        if ($this->isAjaxRequest()) {
            $this->ajaxResponse($success, $message);
        }

        $this->redirect('index.php?page=tasks', $message, !$success);
    }

    public function restore() {
        $this->requireRole('admin');
        $task_id = $_GET['id'] ?? $_POST['id'] ?? null;

        if (!$task_id || !is_numeric($task_id)) {
            $message = 'Invalid task ID';
            if ($this->isAjaxRequest()) {
                $this->ajaxResponse(false, $message);
            }
            $this->redirect('index.php?page=tasks', $message, true);
        }

        $success = $this->models['task']->restore($task_id);
        $message = $success ? 'Task restored successfully' : 'Failed to restore task';

        if ($this->isAjaxRequest()) {
            $this->ajaxResponse($success, $message);
        }

        $this->redirect('index.php?page=tasks', $message, !$success);
    }

    public function view_task() {
        
        $this->requireRoles(['admin', 'team_leader', 'employee']);
        $task_id = $_GET['id'] ?? null;
        $task = $this->models['task']->getTaskById($task_id);

        if (!$task) {
            $this->handleTaskNotFound();
        }

        if ($_SESSION['role'] === 'employee' && $task['assigned_to'] != $_SESSION['id']) {
            $this->handleUnauthorizedAccess();
        }

        if ($this->isAjaxRequest()) {
            $this->ajaxResponse(true, '', ['task' => $task]);
        }

        $this->view('tasks/view', ['task' => $task]);
    }
    protected function validateDates($start, $end) {
        if (empty($start) || empty($end)) {
            return false;
        }
        return strtotime($end) >= strtotime($start);
    }

    protected function handleTaskNotFound() {
        $message = 'Task not found';
        if ($this->isAjaxRequest()) {
            $this->ajaxResponse(false, $message);
        }
        $this->redirect('index.php?page=tasks', $message, true);
    }

    protected function handleUnauthorizedAccess() {
        $message = 'Unauthorized access';
        if ($this->isAjaxRequest()) {
            $this->ajaxResponse(false, $message);
        }
        $this->redirect('index.php?page=tasks', $message, true);
    }
}