<?php
require_once 'Controller.php';
require_once 'app/models/User.php';

class AuthController extends Controller {
    public function __construct($db = null) {
        parent::__construct($db);
        echo ('echo connection');
    }
    
public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $this->sanitize($_POST['email']);
        $password = $this->sanitize($_POST['password']);
        
        // Get user by email
        $user = $this->models['user']->login($email);
        // In AuthController.php login method:
if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                if ($user['role'] === 'pending') {
                    $this->view('auth/login', ['error' => 'Your account is pending approval']);
                    return;
                }
                // Set session variables
                $_SESSION['id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];
                
                header("Location: index.php?page=dashboard");
            }
        }
        $this->view('auth/login', ['error' => 'Invalid email or password']);
    } else {
        $this->view('auth/login');
    }
}    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $this->sanitize($_POST['name']),
                'email' => $this->sanitize($_POST['email']),
                'password' => $this->sanitize($_POST['password']),
                'role' => 'pending'
            ];

            // Validate inputs
            if (empty($data['name']) || empty($data['email'])) {
                $this->view('auth/registration', ['error' => 'Name and email are required']);
                return;
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->view('auth/registration', ['error' => 'Invalid email format']);
                return;
            }
            if (strlen($_POST['password']) < 8) {
                $this->view('auth/registration', ['error' => 'Password must be at least 8 characters long']);
                return;
            }

            try {
                $this->models['user']->name = $data['name'];
                $this->models['user']->email = $data['email'];
                $this->models['user']->password = $data['password'];
                $result = $this->models['user']->register();
                if ($result === true) {
                    $this->redirect('index.php?page=login', 'Registration successful. Please wait for approval.');
                } else {
                    $this->view('auth/registration', ['error' => is_string($result) ? $result : 'Registration failed']);
                }
            } catch (Exception $e) {
                $this->view('auth/registration', ['error' => 'Registration error: ' . $e->getMessage()]);
            }
        } else {
            $this->view('auth/registration');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('index.php?page=login');
    }
}