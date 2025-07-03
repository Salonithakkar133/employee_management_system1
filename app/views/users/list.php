<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Users</h2>

    <div id="message-container">
        <?php if (isset($_GET['message'])): ?>
            <p class="message"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
    </div>

    <div id="users-table-container">
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
                                    <a href="index.php?page=restore_user&id=<?php echo $user['id']; ?>" class="ajax-action">Restore</a>
                                <?php else: ?>
                                    <a href="index.php?page=edit_user&id=<?php echo $user['id']; ?>">Edit Role</a> |
                                    <a href="index.php?page=update_user&id=<?php echo $user['id']; ?>">Update</a> |
                                    <a href="index.php?page=delete_user&id=<?php echo $user['id']; ?>" class="ajax-action confirm-action">Delete</a>
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
    </div>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <p><a href="index.php?page=add_user">Add New User</a></p>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle AJAX actions (delete/restore)
            document.querySelectorAll('.ajax-action').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    if (this.classList.contains('confirm-action') && !confirm('Are you sure?')) {
                        return;
                    }
                    
                    const messageContainer = document.getElementById('message-container');
                    messageContainer.innerHTML = '<p class="message">Processing...</p>';
                    
                    fetch(this.href, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload the table content
                            fetch('index.php?page=users', {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            })
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newTable = doc.getElementById('users-table-container');
                                if (newTable) {
                                    document.getElementById('users-table-container').innerHTML = newTable.innerHTML;
                                }
                            });
                        }
                        
                        messageContainer.innerHTML = `<p class="message ${data.success ? 'success' : 'error'}">${data.message}</p>`;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        messageContainer.innerHTML = '<p class="message error">Action failed. Please try again.</p>';
                    });
                });
            });
        });
    </script>
</div>
<?php include 'app/views/template/footer.php'; ?>