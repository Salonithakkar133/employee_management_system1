<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Add Task</h2>
    <?php
    // Display session-based messages
    if (isset($_SESSION['success'])) {
        echo '<p class="message success">' . htmlspecialchars($_SESSION['success']) . '</p>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<p class="message error">' . htmlspecialchars($_SESSION['error']) . '</p>';
        unset($_SESSION['error']);
    }
    // Display view-passed message
    if (!empty($message)) {
        echo '<p class="message">' . htmlspecialchars($message) . '</p>';
    }
    ?>
    <form method="POST" action="index.php?page=add_task">
        <div>
            <label>Title</label>
            <input type="text" name="title" required>
        </div>
        <div>
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>
        <div>
            <label>Status</label>
            <select name="status">
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        <div>
            <label>Assign To</label>
            <select name="assigned_to">
                <option value="">Unassigned</option>
                <?php while ($user = $users->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label>Start Date</label>
            <input type="date" name="start_date" required>
        </div>
        <div>
            <label>End Date</label>
            <input type="date" name="end_date" required>
        </div>
        <button type="submit">Add Task</button>
    </form>
    <p><a href="index.php?page=tasks">Back to Tasks</a></p>
</div>
<?php include 'app/views/template/footer.php'; ?>