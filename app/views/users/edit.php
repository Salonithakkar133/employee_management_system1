
<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Assign User Role Edit</h2>
    <?php if (!empty($message)): ?>
        <p class="<?php echo strpos($message, 'failed') !== false ? 'error' : 'message'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>
    <?php if ($user): ?>
    <form method="POST" action="index.php?page=edit_user&id=<?php echo $user['id']; ?>">
        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
        <div>
            <label>Name</label>
            <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" disabled>
        </div>
        <div>
            <label>Email</label>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </div>
        <div>
            <label>Role</label>
            <select name="role" required>
                <option value="pending" <?php echo $user['role'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="employee" <?php echo $user['role'] === 'employee' ? 'selected' : ''; ?>>Employee</option>
                <option value="team_leader" <?php echo $user['role'] === 'team_leader' ? 'selected' : ''; ?>>Team Leader</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>
        <button type="submit">Update Role</button>
    </form>
    <?php else: ?>
    <p class="error">User not found.</p>
    <?php endif; ?>
    <p><a href="index.php?page=users">Back to Users</a></p>
</div>

<?php include 'app/views/template/footer.php'; ?>