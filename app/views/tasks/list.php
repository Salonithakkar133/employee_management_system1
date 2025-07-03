<?php include 'app/views/template/header.php'; ?>

<div class="container">

<form action="index.php" method="get" id ="search-item">
    <input type="hidden" name="page" value="tasks">
    
    <label>Search by User:</label>
    <select name="user_search" id="user_search">
        <option value="">All Users</option>
        <?php foreach ($users as $user): ?>
            <option value="<?php echo htmlspecialchars($user['id']); ?>" <?php echo $user['id'] == $user_search ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($user['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="title_search">Search:</label>
    <input type="text" name="title_search" id="title_search" value="<?php echo $title_search; ?>" placeholder="Enter task title">

    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($start_date ?? ''); ?>">

    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($end_date ?? ''); ?>">

    <button type="submit" id="task-search-form">Search</button>

    <a href="index.php?page=tasks">Reset</a>
</form>
    <h2>Tasks</h2>
    <div id="message"> 
    <?php 
    
    if (isset($_SESSION['success'])) {
        echo '<p class="message success">' . htmlspecialchars($_SESSION['success']) . '</p>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<p class="message error">' . htmlspecialchars($_SESSION['error']) . '</p>';
        unset($_SESSION['error']);
    }
    if (isset($_GET['message'])) {
        echo '<p class="message">' . htmlspecialchars($_GET['message']) . '</p>';
    }
    ?>
    </div>
    <?php if (!empty($tasks)): ?>
    <table id="task-table">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Assigned To</th>
            <th>Assigned By</th>
            <th>Created At</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($tasks as $task): ?>
        <tr>
                        <td><?php echo htmlspecialchars($task['id']); ?></td>
                        <td><?php echo htmlspecialchars($task['title']); ?></td>
                        <td><?php echo htmlspecialchars($task['description']); ?></td>
                        <td><?php echo htmlspecialchars($task['status']); ?></td>
                        <td><?php echo htmlspecialchars($task['assigned_to'] ?? 'Unassigned'); ?></td>
                        <td><?php echo htmlspecialchars($task['created_by']); ?></td>
                        <td><?php echo htmlspecialchars($task['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($task['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($task['end_date']); ?></td>
                
            <td>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <?php if ($task['is_deleted']): ?>
                        <a href="index.php?page=restore_task&id=<?php echo $task['id']; ?>"class="ajax-action">Restore</a>
                    <?php else: ?>
                        <a href="index.php?page=edit_task&id=<?php echo $task['id']; ?>">Edit</a> |
                        <a href="index.php?page=delete_task&id=<?php echo $task['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a> |
                        <a href="index.php?page=view_task&id=<?php echo htmlspecialchars($task['id']); ?>">View</a>
                    <?php endif; ?>
                <?php elseif ($_SESSION['role'] === 'team_leader'): ?>
                    <?php if ($task['is_deleted']): ?>
                        <span style="color:gray;">Deleted</span>
                    <?php else: ?>
                        <a href="index.php?page=edit_task&id=<?php echo $task['id']; ?>">Edit</a> |
                        <a href="index.php?page=delete_task&id=<?php echo $task['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a> |
                        <a href="index.php?page=view_task&id=<?php echo htmlspecialchars($task['id']); ?>">View</a>
                    <?php endif; ?>
                <?php elseif ($_SESSION['role'] === 'employee' && $task['assigned_to'] == $_SESSION['id'] && !$task['is_deleted']): ?>
                    <a href="index.php?page=edit_task&id=<?php echo $task['id']; ?>">Update Status</a> |
                    <a href="index.php?page=view_task&id=<?php echo htmlspecialchars($task['id']); ?>">View</a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <p>No tasks found.</p>
    <?php endif; ?>
    <?php if ($_SESSION['role'] !== 'employee'): ?>
        <p><a href="index.php?page=add_task">Add New Task</a></p>
    <?php endif; ?>

<script>
// Debugging check - this should show immediately
console.log("Script is loading!");

document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM is fully loaded");
    
    // 1. Get the form element
    const searchForm = document.getElementById('search-item');
    console.log("Form element:", searchForm);
    
    if (!searchForm) {
        console.error("CRITICAL ERROR: Could not find form with ID 'search-item'");
        alert("Error: Could not find search form!");
        return;
    }
    
    // 2. Add submit handler
    searchForm.addEventListener('submit', function(e) {
        console.log("Form submit event triggered");
        
        // 3. COMPLETELY prevent default behavior
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // 4. Get form elements
        const submitButton = searchForm.querySelector('button[type="submit"]');
        const messageDiv = document.getElementById('message');
        
        console.log("Submit button:", submitButton);
        console.log("Message div:", messageDiv);
        
        // 5. Set loading state
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = "Searching...";
        
        // 6. Prepare form data (for GET request)
        const formData = new URLSearchParams(new FormData(searchForm));
        const url = `index.php?${formData.toString()}`;
        
        console.log("Making request to:", url);
        
        // 7. Make AJAX request
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log("Received response, status:", response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Received data:", data);
            
            // 8. Update message
            if (messageDiv) {
                messageDiv.textContent = data.message || "Search completed";
                messageDiv.className = data.success ? "message success" : "message error";
                messageDiv.style.display = "block";
            }
            
            // 9. Update table if HTML is returned
            if (data.html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data.html, 'text/html');
                const newTable = doc.querySelector('#task-table');
                
                if (newTable) {
                    const oldTable = document.querySelector('#task-table');
                    if (oldTable) {
                        oldTable.replaceWith(newTable);
                    }
                }
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            if (messageDiv) {
                messageDiv.textContent = "Error: " + error.message;
                messageDiv.className = "message error";
                messageDiv.style.display = "block";
            }
        })
        .finally(() => {
            console.log("Request completed");
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
        
        // 10. Explicitly return false
        return false;
    });
    
    console.log("Event listener successfully attached to form");
});
</script>
</div>

<?php include 'app/views/template/footer.php'; ?>