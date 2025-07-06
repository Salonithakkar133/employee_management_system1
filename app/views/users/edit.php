<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Assign User Role Edit</h2>
    <div id="message" class="message" style="display: none;"></div>
    
    <?php if ($user): ?>
    <form id="edit-role-form" method="POST" action="index.php?page=edit_user&id=<?php echo $user['id']; ?>" onsubmit="return validateForm(event)">
        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
        <div>
            <label>Name</label>
            <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" disabled>
        </div>
        <div>
            <label>Email</label>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </div>
        <div>
            <label>Role</label>
            <select name="role" id="role" required>
                <option value="pending" <?php echo $user['role'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="employee" <?php echo $user['role'] === 'employee' ? 'selected' : ''; ?>>Employee</option>
                <option value="team_leader" <?php echo $user['role'] === 'team_leader' ? 'selected' : ''; ?>>Team Leader</option>
                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
            <span id="role-error" class="error-message"></span>
        </div>
        <button type="submit" id="submit-button">Update Role</button>
    </form>
    <?php else: ?>
    <p class="error">User not found.</p>
    <?php endif; ?>
    
    <p><a href="index.php?page=users">Back to Users</a></p>

    <script>
        function validateForm(event) {
            event.preventDefault(); // Prevent default form submission

            // Get form elements
            const role = document.getElementById("role").value.trim();
            const messageDiv = document.getElementById("message");
            const roleErr = document.getElementById("role-error");
            const submitButton = document.getElementById("submit-button");

            // Reset error messages
            roleErr.textContent = "";
            messageDiv.style.display = "none";

            let isValid = true;

            // Validate role
            const validRoles = ['pending', 'employee', 'team_leader', 'admin'];
            if (role === "" || !validRoles.includes(role)) {
                roleErr.textContent = "Please select a valid role.";
                isValid = false;
            }

            if (!isValid) {
                return false;
            }

            // Prepare form data for AJAX
            const form = document.getElementById("edit-role-form");
            const formData = new FormData(form);
            const submitButtonOriginalText = submitButton.textContent;

            // Disable submit button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';

            // Perform AJAX request
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                messageDiv.textContent = data.message || "Operation completed";
                messageDiv.className = 'message ' + (data.success ? 'success' : 'error');
                messageDiv.style.display = 'block';

                if (data.success) {
                    // Show success message and redirect after 2 seconds
                    setTimeout(() => {
                        messageDiv.textContent = "Role updated successfully";
                        messageDiv.className = 'message success';
                        messageDiv.style.display = 'block';
                        setTimeout(() => {
                            window.location.href = 'index.php?page=users';
                        }, 2000);
                    }, 500);
                }
            })
            .catch(error => {
                console.error("Fetch Error:", error);
                messageDiv.textContent = "Error updating role: " + error.message;
                messageDiv.className = 'message error';
                messageDiv.style.display = 'block';
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = submitButtonOriginalText;
            });

            return false; // Prevent form submission
        }
    </script>
</div>
<?php include 'app/views/template/footer.php'; ?>