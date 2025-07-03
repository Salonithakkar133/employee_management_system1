<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Add User</h2>
    <div id="message" class="message" style="display: none;"></div>
    
    <form id="add-user-form" method="POST" action="index.php?page=add_user">
        <div>
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Role</label>
            <select name="role">
                <option value="employee">Employee</option>
                <option value="team_leader">Team Leader</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit">Add User</button>
    </form>
    
    <p><a href="index.php?page=users">Back to Users</a></p>

    <script>
        document.getElementById('add-user-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            const messageDiv = document.getElementById('message');
            const submitButton = form.querySelector('button[type="submit"]');
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
            
            fetch(form.action, {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Show message
                messageDiv.textContent = data.message;
                messageDiv.className = 'message ' + (data.success ? 'success' : 'error');
                messageDiv.style.display = 'block';
                
                if (data.success) {
                    // Reset form on success
                    form.reset();
                    // Optionally redirect after delay
                    setTimeout(() => {
                        window.location.href = 'index.php?page=users';
                    }, 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.textContent = 'Error adding user. Please try again.';
                messageDiv.className = 'message error';
                messageDiv.style.display = 'block';
            })
            .finally(() => {
                // Restore button state
                submitButton.disabled = false;
                submitButton.textContent = 'Add User';
            });
        });
    </script>
</div>
<?php include 'app/views/template/footer.php'; ?>