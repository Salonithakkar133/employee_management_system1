<?php include 'app/views/template/header.php'; ?>

<div class="container mt-4">
    <h2>Update User</h2>

    <div id="messageContainer">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= strpos(strtolower($message), 'failed') !== false ? 'danger' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
    </div>

    <form id="updateForm" method="post" action="index.php?page=update_user&id=<?= $user['id'] ?>">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Password (Leave blank to keep current)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <?php if ($_SESSION['role'] === 'admin'): ?>
        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="team_leader" <?= $user['role'] === 'team_leader' ? 'selected' : '' ?>>Team Leader</option>
                <option value="employee" <?= $user['role'] === 'employee' ? 'selected' : '' ?>>Employee</option>
            </select>
        </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary" id="submitBtn">Update</button>
        <a href="index.php?page=users" class="btn btn-secondary ml-2">Cancel</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updateForm');
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = document.getElementById('submitBtn');
        const originalBtnText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const messageContainer = document.getElementById('messageContainer');
            messageContainer.innerHTML = `<div class="alert alert-${data.success ? 'success' : 'danger'}">${data.message}</div>`;
            
            if (data.success) {
                // Optionally reload after delay
                setTimeout(() => {
                    if (data.requires_refresh) {
                        window.location.reload();
                    }
                }, 1500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('messageContainer').innerHTML = 
                '<div class="alert alert-danger">Error updating user. Please try again.</div>';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });
});
</script>

<?php include 'app/views/template/footer.php'; ?>