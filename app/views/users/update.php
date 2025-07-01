<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Update User</h2>
    <?php if (!empty($message)): ?>
        <p class="<?php echo strpos($message, 'failed') !== false || strpos($message, 'already') !== false || strpos($message, 'Password') !== false ? 'error' : 'message'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    <form method="POST" action="index.php?page=update_user&id=<?php echo $user['id']; ?>">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        <div>
            <label>Name</label>
            <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
        </div>
        <div>
            <label>Password (leave blank to keep current)</label>
            <input type="password" name="password">
        </div>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <div>
            <label>Role</label>
            <select name="role">
                <option value="employee" <?php echo $user['role'] === 'employee' ? 'selected' : ''; ?>>Employee</option>
                <option value="team_leader" <?php echo $user['role'] === 'team_leader' ? 'selected' : ''; ?>>Team Leader</option>
                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>
        <?php else: ?>
        <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
        <?php endif; ?>
        <button type="submit">Update User</button>
    </form>
    <p><a href="index.php?page=users">Back to Users</a></p>
</div>
<?php include 'app/views/template/footer.php'; ?>
