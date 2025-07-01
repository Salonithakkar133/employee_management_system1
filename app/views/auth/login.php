<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Login</h2>
    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="index.php?page=login">
        <div>
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <p><a href="index.php?page=register">Register</a></p>
</div>
<?php include 'app/views/template/footer.php'; ?>