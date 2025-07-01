<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Edit Task</h2>
    <?php
    if (isset($_SESSION['success'])) {
        echo '<p class="message success">' . htmlspecialchars($_SESSION['success']) . '</p>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<p class="message error">' . htmlspecialchars($_SESSION['error']) . '</p>';
        unset($_SESSION['error']);
    }
    
    if (!empty($message)) {
        echo '<p class="message">' . htmlspecialchars($message) . '</p>';
    }
    ?>
    <form method="POST" action="index.php?page=edit_task&id=<?php echo $task['id']; ?>">
        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
        <?php if ($_SESSION['role'] === 'employee'): ?>
            <!-- EMPLOYEE: Only allowed to update status -->
            <div>
                <label>Status</label>
                <select name="status">
                    <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $task['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
        <?php else: ?>      <div>
                <label>Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
            </div>
            <div>
                <label>Description</label>
                <textarea name="description"><?php echo htmlspecialchars($task['description']); ?></textarea>
            </div>
            
            <div>
                <label>Assign To</label>
                <select name="assigned_to">
                    <option value="">Unassigned</option>
                    <?php while ($user = $users->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo $user['id'] == $task['assigned_to'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label>Start Date</label>
                <input type="date" name="start_date" value="<?php echo htmlspecialchars($task['start_date']); ?>" required>
            </div>
            <div>
                <label>End Date</label>
                <input type="date" name="end_date" value="<?php echo htmlspecialchars($task['end_date']); ?>" required>
            </div>
        <?php endif; ?>
        <button type="submit">Update Task</button>
    </form>
    <p><a href="index.php?page=tasks">Back to Tasks</a></p>
</div>
<?php include 'app/views/template/footer.php'; ?>