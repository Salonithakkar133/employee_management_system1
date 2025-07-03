<?php
require_once 'config/database.php';
class Controller {
    protected $db;
    protected $models = [];
    protected $viewData = [];
    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        } else {
            $database = new Database();
            $this->db = $database->getConnection();
        }
        $this->initializeSession();
        $this->loadCoreModels();
    }
    protected function initializeSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    protected function loadCoreModels() {
        require_once 'app/models/User.php';
        $this->models['user'] = new User($this->db);
    }
    protected function view($view, $data = []) {
        $data = array_merge($this->viewData, $data);
        extract($data);
        require_once "app/views/$view.php";
    }
    protected function redirect($url, $message = null, $isError = false) {
        if ($message) {
            $_SESSION[$isError ? 'error' : 'success'] = $message;
        }
        header("Location: $url");
        exit;
    }
    protected function requireAuth() {
        if (!isset($_SESSION['id'])) {
            $this->redirect('login', 'Please login first', true);
        }
    }
    protected function requireRole($role) {
        $this->requireAuth();
        if ($_SESSION['role'] !== $role) {
            $this->redirect('dashboard', 'Unauthorized access', true);
        }
    }
    protected function requireRoles($roles) {
        $this->requireAuth();
        if (!in_array($_SESSION['role'], $roles)) {
            $this->redirect('dashboard', 'Unauthorized access', true);
        }
    }
    protected function sanitize($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
protected function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    protected function ajaxResponse($success, $message = '', $data = []) {
        header('Content-Type: application/json');
        http_response_code($success ? 200 : 400);
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

}