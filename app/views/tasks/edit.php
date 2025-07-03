<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Edit Task</h2>
    <p id="message" class="message" style="display: none;"></p>
    <form id="task-edit-form" method="POST" action="index.php?page=edit_task&id=<?php echo $tasks['id']; ?>">
        <input type="hidden" name="task_id" value="<?php echo $tasks['id']; ?>">
        <?php if ($_SESSION['role'] === 'employee'): ?>
            <!-- EMPLOYEE: Only allowed to update status -->
            <div>
                <label>Status</label>
                <select name="status">
                    <option value="pending" <?php echo $tasks['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $tasks['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $tasks['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
        <?php else: ?>
            <div>
                <label>Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($tasks['title']); ?>" required>
            </div>
            <div>
                <label>Description</label>
                <textarea name="description"><?php echo htmlspecialchars($tasks['description']); ?></textarea>
            </div>
            <div>
                <label>Assign To</label>
                <select name="assigned_to">
                    <option value="">Unassigned</option>
                    <?php while ($user = $users->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo $user['id'] == $tasks['assigned_to'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label>Start Date</label>
                <input type="date" name="start_date" value="<?php echo htmlspecialchars($tasks['start_date']); ?>" required>
            </div>
            <div>
                <label>End Date</label>
                <input type="date" name="end_date" value="<?php echo htmlspecialchars($tasks['end_date']); ?>" required>
            </div>
        <?php endif; ?>
        <button type="submit">Update Task</button>
    </form>
    <p><a href="index.php?page=tasks">Back to Tasks</a></p>

    <script>
        // AJAX for form submission
        document.getElementById('task-edit-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const messageDiv = document.getElementById('message');

            fetch('index.php?page=edit_task&id=<?php echo $tasks['id']; ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                messageDiv.textContent = data.message;
                messageDiv.className = 'message' + (data.success ? ' success' : ' error');
                messageDiv.style.display = 'block';
                if (data.success) {
                    // Optionally redirect to task list after a delay
                    setTimeout(() => {
                        window.location.href = 'index.php?page=tasks';
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.textContent = 'Error updating task';
                messageDiv.className = 'message error';
                messageDiv.style.display = 'block';
            });
        });
    </script>
</div>
<?php include 'app/views/template/footer.php'; ?>