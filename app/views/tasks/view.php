<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Task Details</h2>
    <?php if ($task): ?>
        <p><strong>ID:</strong> <?php echo htmlspecialchars($task['id']); ?></p>
        <p><strong>Title:</strong> <?php echo htmlspecialchars($task['title']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($task['description']); ?></p>
        <p><strong>Assigned To:</strong> <?php echo htmlspecialchars($task['assigned_to'] ?? 'Unassigned'); ?></p>
        <p><strong>Assigned By:</strong> <?php echo htmlspecialchars($task['created_by']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>
        <p><strong>Created At:</strong> <?php echo htmlspecialchars($task['created_at']); ?></p>
        <p><strong>Start date:</strong> <?php echo htmlspecialchars($task['start_date']); ?></p>
        <p><strong>End date:</strong> <?php echo htmlspecialchars($task['end_date']); ?></p>

        <p><a href="index.php?page=tasks">Back to Task List</a></p>
    <?php else: ?>
        <p>Task not found.</p>
        <p><a href="index.php?page=tasks">Back to Task List</a></p>
    <?php endif; ?>
    
</div>



<?php include 'app/views/template/footer.php'; ?>