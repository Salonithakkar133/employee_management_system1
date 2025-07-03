<?php
// Start session at the VERY TOP (before any output)
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'app/controllers/AuthController.php';
require_once 'app/controllers/UserController.php';
require 'app/controllers/TaskController.php';

// Handle empty page parameter
if (!isset($_GET['page']) || empty(trim($_GET['page']))) {
    if (isset($_SESSION['id'])) {
        // User is logged in but accessed index.php? - redirect to dashboard
        header("Location: index.php?page=dashboard");
    } else {
        // Not logged in - go to login
        header("Location: index.php?page=login");
    }
    exit;
}

$page = trim($_GET['page']);
$authController = new AuthController();
$userController = new UserController();
$taskController = new TaskController();

switch ($page) {
    case 'login':
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['id'])) {
            header("Location: index.php?page=dashboard");
            exit;
        }
        $authController->login();
        break;
        
    case 'register':
        $authController->register();
        break;
        
    case 'logout':
        $authController->logout();
        break;
        
    case 'dashboard':
        if (!isset($_SESSION['id'])) {
            header("Location: index.php?page=login");
            exit;
        }
        if ($_SESSION['role'] === 'pending') {
            header("Location: index.php?page=login");
            exit;
        }
        $role = $_SESSION['role'];
        include_once "app/views/dashboard/$role.php";
        break;
        
    case 'users':
        if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['admin', 'team_leader','employee'])) {
            header("Location: index.php?page=login");
            exit;
        }
        $userController->list();
        break;
        
    case 'add_user':
    case 'edit_user':
    case 'delete_user':
        if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?page=login");
            exit;
        }
        if ($page === 'add_user') $userController->add();
        elseif ($page === 'edit_user') $userController->edit();
        elseif ($page === 'delete_user') $userController->delete();
        break;
        
    case 'update_user':
        if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['admin', 'team_leader','employee'])) {
            header("Location: index.php?page=login");
            exit;
        }
        $userController->update();
        break;
        
    case 'tasks':
    case 'add_task':
    case 'edit_task':
    case 'delete_task':
        if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['admin', 'team_leader', 'employee'])) {
            header("Location: index.php?page=login");
            exit;
        }
        if ($page === 'tasks') $taskController->list();
        elseif ($page === 'add_task') $taskController->add();
        elseif ($page === 'edit_task') $taskController->edit();
        elseif ($page === 'delete_task') $taskController->delete();
        break;
        
    case 'profile':
        if (!isset($_SESSION['id'])) {
            header("Location: index.php?page=login");
            exit;
        }
        $user_id = $_SESSION['id'];
        $user = $userController->profile($user_id);
        include_once 'app/views/users/profile.php'; 
        break;
        
    case 'update_profile':
        if (!isset($_SESSION['id'])) {
            header("Location: index.php?page=login");
            exit;
        }
        $userController->Profile();
        break;
        
    case 'restore_user':
        if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?page=login");
            exit;
        }
        $userController->restore();
        break;
        
    case 'restore_task':
        if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?page=login");
            exit;
        }
        $taskController->restore();
        break; 
        
    case 'view_task':
        $taskController->view_task();
        break;
        
    default:
        // Invalid page requested - redirect based on login status
        if (isset($_SESSION['id'])) {
            header("Location: index.php?page=dashboard");
        } else {
            header("Location: index.php?page=login");
        }
        exit;
}