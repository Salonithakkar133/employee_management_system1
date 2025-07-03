<?php
require_once 'Controller.php';
require_once 'app/models/User.php';

class AuthController extends Controller {
    public function __construct($db = null) {
        parent::__construct($db);
    }

    public function login() {
        // Check if already logged in
        if (isset($_SESSION['id'])) {
            $this->redirect('index.php?page=dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitize($_POST['email']);
            $password = $this->sanitize($_POST['password']);
            
            // Get user by email
            $user = $this->models['user']->login($email);

            if ($user) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    if ($user['role'] === 'pending') {
                        $this->view('auth/login', ['error' => 'Your account is pending approval']);
                        return;
                    }
                    
                    // Regenerate session ID to prevent fixation
                    session_regenerate_id(true);
                    
                    // Set secure session variables
                    $_SESSION = [
                        'id' => $user['id'],
                        'role' => $user['role'],
                        'name' => $user['name'],
                        'ip' => $_SERVER['REMOTE_ADDR'],
                        'user_agent' => $_SERVER['HTTP_USER_AGENT']
                    ];
                    
                    $this->redirect('index.php?page=dashboard');
                    return;
                }
            }
            
            // Failed login attempt
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
                'password' => password_hash($this->sanitize($_POST['password']), PASSWORD_DEFAULT),
                'role' => 'pending'
            ];
            $errors = [];
            if (empty($data['name'])) {
                $errors[] = 'Name is required';
            }
            if (empty($data['email'])) {
                $errors[] = 'Email is required';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }
            if (strlen($_POST['password']) < 8) {
                $errors[] = 'Password must be at least 8 characters long';
            }

            if (!empty($errors)) {
                $this->view('auth/registration', ['error' => implode('<br>', $errors)]);
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
                    $error = is_string($result) ? $result : 'Registration failed';
                    $this->view('auth/registration', ['error' => $error]);
                }
            } catch (Exception $e) {
                $this->view('auth/registration', ['error' => 'Registration error: ' . $e->getMessage()]);
            }
        } else {
            $this->view('auth/registration');
        }
    }

    public function logout() {
        // Clear all session variables
        $_SESSION = [];

        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }

        // Destroy the session
        session_destroy();

        // Redirect to login
        $this->redirect('index.php?page=login');
    }
}