<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Employee Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>! View your assigned tasks below.</p>
    <h3>Your Tasks</h3>
    <ul>
        <li><a href="index.php?page=tasks">View Assigned Tasks</a></li>
    </ul>
</div>
<?php include 'app/views/template/footer.php'; ?>