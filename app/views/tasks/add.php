<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Add Task</h2>
    <p id="message" class="message" style="display: none;"></p>
    <form id="task-add-form" method="POST" action="index.php?page=add_task">
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

    <script>
        // Progressive enhancement - form works with and without JavaScript
        document.getElementById('task-add-form').addEventListener('submit', function(e) {
            // Only intercept if JavaScript is enabled
            if (typeof fetch === 'function') {
                e.preventDefault();
                const formData = new FormData(this);
                const messageDiv = document.getElementById('message');
                const submitButton = this.querySelector('button[type="submit"]');
                
                // Show loading state
                const originalButtonText = submitButton.textContent;
                submitButton.disabled = true;
                submitButton.textContent = 'Adding...';
                
                fetch(this.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    // Show message
                    messageDiv.textContent = data.message;
                    messageDiv.className = 'message' + (data.success ? ' success' : ' error');
                    messageDiv.style.display = 'block';
                    
                    if (data.success) {
                        // Reset form on success
                        this.reset();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageDiv.textContent = 'Error adding task. Please try again.';
                    messageDiv.className = 'message error';
                    messageDiv.style.display = 'block';
                })
                .finally(() => {
                    // Restore button state
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;
                    
                    // If fetch fails completely, the form will still submit normally
                    // due to the preventDefault() being conditional
                });
            }
        });
    </script>
</div>
<?php include 'app/views/template/footer.php'; ?>