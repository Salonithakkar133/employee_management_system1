<?php
require_once 'Controller.php';
require_once 'app/models/user.php' ;
class UserController extends Controller {
    public function __construct($db = null) {
        parent::__construct($db);
    }
    public function list() {
        $this->requireRoles(['admin', 'team_leader', 'employee']);
        
        $role = $_SESSION['role'];
        if ($role === 'team_leader') {
            $users = $this->models['user']->getNonAdminUsers();
        } elseif ($role === 'admin') {
            $users = $this->models['user']->getAllUsers();
        } else {
            $users = $this->models['user']->getAllActiveUsers();
        }
        
        $this->view('users/list', ['users' => $users]);
    }
    public function add() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $this->sanitize($_POST['name']),
                'email' => $this->sanitize($_POST['email']),
                'password' => $this->sanitize($_POST['password']),
                'role' => $this->sanitize($_POST['role'])
            ];
            
            $result = $this->models['user']->add($data);
            $message = match ($result) {
                true => "User added successfully.",
                "Email is already registered" => "Email is already registered.",
                default => "Failed to add user."
            };
            
            $this->view('users/add', ['message' => $message]);
        } else {
            $this->view('users/add');
        }
    }
    public function edit() {
    $this->requireRoles(['admin', 'team_leader']);
    
    $user_id = $_GET['id'] ?? null;
    $user = $this->models['user']->getUserById($user_id);
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_SESSION['id'] == $_POST['user_id']) {
            $message = "You cannot change your own role.";
        } elseif ($_SESSION['role'] === 'team_leader' && $_POST['role'] === 'admin') {
            $message = "Team Leader cannot assign Admin role.";
        } else {
            $success = $this->models['user']->updateRole(
                $this->sanitize($_POST['user_id']),
                $this->sanitize($_POST['role'])
            );
            $message = $success ? "Role updated successfully." : "Failed to update role.";
            $user = $this->models['user']->getUserById($user_id); // Refresh user data
        }
    }
    $this->view('users/edit', [
        'user' => $user,
        'message' => $message
    ]);
}   public function Profile() {
    $this->requireAuth();
    $user_id = $_SESSION['id'];
    $user = $this->models['user']->getUserById($user_id);
    if (!$user) {
        $message = "User not found. Please try logging in again.";
    } else {
        $message = '';
        
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'id' => $user_id,
            'name' => $this->sanitize($_POST['name']),
            'email' => $this->sanitize($_POST['email']),
            'role' => $user['role'] ?? 'pending' // Fallback role if $user is null
        ];
        if (!empty($_POST['password'])) {
            if (strlen($_POST['password']) < 8) {
                $message = "Password must be at least 8 characters long.";
            } else {
                $data['password'] = $this->sanitize($_POST['password']);
            }
        }
        if (!empty($_FILES['profile_image']['name'])) {
            $target_dir = "Uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            $image_name = time() . '_' . basename($_FILES["profile_image"]["name"]);
            $target_file = $target_dir . $image_name;
            
            // Validate file type and size
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            if (!in_array($_FILES['profile_image']['type'], $allowed_types) || $_FILES['profile_image']['size'] > $max_size) {
                $message = "Invalid image type or size (max 2MB, allowed: JPEG, PNG, GIF).";
            } elseif (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                chmod($target_file, 0644);
                if (!empty($user['profile_image']) && file_exists($target_dir . $user['profile_image']) && $user['profile_image'] !== $image_name) {
                    unlink($target_dir . $user['profile_image']);
                }
                $data['profile_image'] = $image_name;
            } else {
                $message = "Failed to upload profile image.";
            }
        }

        if (empty($message)) {
            
            $result = $this->models['user']->update($data);
    
            $message = match (true) {
                $result === true => "Profile updated successfully.",
                is_string($result) => $result,
                default => "Failed to update profile."
            };
            $user = $this->models['user']->getUserById($user_id);
            if (!$user) {
               
                $message = "Error: User data not found after update.";
            } else {
             
            }
        }
    }

    $this->view('users/profile', [
        'user' => $user,
        'message' => $message
    ]);
}    public function update() {
    $this->requireRoles(['admin', 'team_leader', 'employee']);
    
    $user_id = $_GET['id'] ?? null;
    $user = $this->models['user']->getUserById($user_id);
    $message = '';

    if ($_SESSION['role'] === 'team_leader' && $user['role'] === 'admin') {
        $this->redirect('users', 'You are not allowed to edit an Admin user.', true);
    }

    if ($_SESSION['role'] === 'employee' && $_SESSION['id'] != $user_id) {
        $this->redirect('users', 'You can only edit your own profile.', true);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['password']) && strlen($_POST['password']) < 8) {
            $message = "Password must be at least 8 characters long.";
        } else {
            $data = [
                'id' => $this->sanitize($_POST['id']),
                'name' => $this->sanitize($_POST['name']),
                'email' => $this->sanitize($_POST['email']),
                'role' => $user['role'] // Preserve role unless admin
            ];

            if (!empty($_POST['password'])) {
                $data['password'] = $this->sanitize($_POST['password']);
            }

            if ($_SESSION['role'] === 'admin' && isset($_POST['role'])) {
                $data['role'] = $this->sanitize($_POST['role']);
            }

            $result = $this->models['user']->update($data);
            $message = match ($result) {
                true => "User updated successfully.",
                "Email is already registered" => "Email is already registered.",
                default => "Failed to update user."
            };
            $user = $this->models['user']->getUserById($user_id); // Refresh user data
        }
    }

    $this->view('users/update', [
        'user' => $user,
        'message' => $message
    ]);
}

    

    public function delete() {
        $this->requireRole('admin');
        
        $user_id = $_GET['id'] ?? null;
        if ($this->models['user']->softDelete($user_id)){
            header("Location: index.php?page=users&message=user+deleted+successfully");
            } else {
            header("Location: index.php?page=users&message=Failed+to+delete+user");
    }    
    }

    public function restore() {
        $this->requireRole('admin');
        
        $id = $_GET['id'] ?? null;
        if($this->models['user']->restoreUser($id)) {
            header("Location: index.php?page=users&message=user+restored+successfully");
            } else {
            header("Location: index.php?page=users&message=Failed+to+restore+user");
    }    
    }   
}