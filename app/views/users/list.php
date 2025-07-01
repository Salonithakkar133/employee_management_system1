<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Users</h2>

    <?php if (isset($_GET['message'])): ?>
        <p class="message"><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>

    <?php if (!empty($users)): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <?php echo $user['is_deleted'] ? '<span style="color:red;">Deleted</span>' : 'Active'; ?>
                    </td>
                    <td>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <?php if ($user['is_deleted']): ?>
                                <a href="index.php?page=restore_user&id=<?php echo $user['id']; ?>">Restore</a>
                            <?php else: ?>
                                <a href="index.php?page=edit_user&id=<?php echo $user['id']; ?>">Edit Role</a> |
                                <a href="index.php?page=update_user&id=<?php echo $user['id']; ?>">Update</a> |
                                <a href="index.php?page=delete_user&id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                            <?php endif; ?>
                        <?php elseif ($_SESSION['role'] === 'team_leader' && $user['role'] !== 'admin' && !$user['is_deleted']): ?>
                            <a href="index.php?page=update_user&id=<?php echo $user['id']; ?>">Update</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <p><a href="index.php?page=add_user">Add New User</a></p>
    <?php endif; ?>
</div>
<?php include 'app/views/template/footer.php'; ?>
