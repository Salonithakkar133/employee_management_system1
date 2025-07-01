<?php
$currentPage = $_GET['page'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employment Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <nav>
        <h1>Employee Management System</h1>
        <ul>
            <?php if (isset($_SESSION['id'])): ?>
                <li><a href="index.php?page=dashboard">Dashboard</a></li>
                <?php if (in_array($_SESSION['role'], ['admin', 'team_leader'])): ?>
                    <li><a href="index.php?page=tasks">Create Task</a></li>
                    <li><a href="index.php?page=users">Manage Users</a></li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="index.php?page=add_user">Add User</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <li><a href="index.php?page=profile">Profile</a></li>
                <li><a href="index.php?page=logout">Logout</a></li>
            <?php else: ?>
                <?php if ($currentPage !== 'login'): ?>
                    <li><a href="index.php?page=login">Login</a></li>
                <?php endif; ?>
                <?php if ($currentPage !== 'register'): ?>
                    <li><a href="index.php?page=register">Register</a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<div class="container">
