<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Add User</h2>
    <?php if (!empty($message)): ?>
        <p class="<?php echo strpos($message, 'failed') !== false || strpos($message, 'already') !== false ? 'error' : 'message'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>
    <form method="POST" action="index.php?page=add_user">
        <div>
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Role</label>
            <select name="role">
                <option value="employee">Employee</option>
                <option value="team_leader">Team Leader</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit">Add User</button>
    </form>
    <p><a href="index.php?page=users">Back to Users</a></p>
</div>
<?php include 'app/views/template/footer.php'; ?>