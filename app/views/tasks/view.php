<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Task Details</h2>
    <div id="message" style="display:none;"></div>

    <?php if (!empty($task) && is_array($task)): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Assigned To</th>
                <th>Assigned By</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($task['id']); ?></td>
                <td><?php echo htmlspecialchars($task['title']); ?></td>
                <td><?php echo htmlspecialchars($task['description']); ?></td>
                <td><?php echo htmlspecialchars($task['user_assigned_name'] ?? 'Unassigned'); ?></td>
                <td><?php echo htmlspecialchars($task['user_created_name'] ?? 'Unknown'); ?></td>
                <td><?php echo htmlspecialchars($task['status']); ?></td>
                <td><?php echo htmlspecialchars($task['created_at']); ?></td>
                <td><?php echo htmlspecialchars($task['start_date'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($task['end_date'] ?? 'N/A'); ?></td>
            </tr>
        </table>
        <p><a href="index.php?page=tasks">Back to Task List</a></p>
    <?php else: ?>
        <p>No task found.</p>
        <p><a href="index.php?page=tasks">Back to Task List</a></p>
    <?php endif; ?>