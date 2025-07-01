<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'app/controllers/AuthController.php';
require_once 'app/controllers/UserController.php';
require 'app/controllers/TaskController.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'login';
$authController = new AuthController();
$userController = new UserController();
$taskController = new TaskController();

switch ($page) {
    case 'login':
        $authController->login();
        break;
    case 'register':
        $authController->register();
        break;
    case 'logout':
        $authController->logout();
        break;
    case 'dashboard':
        //session_start();
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
        //session_start();
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
        session_start();
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
        require_once 'app/controllers/TaskController.php';
        $task = new TaskController();
        $task->restore();
        break; 
    case 'view_task':
        require_once 'app/controllers/TaskController.php';
        $task = new TaskController();
        $task->view_task();
        break;
    default:
        $authController->login();
}
?>