<?php include 'app/views/template/header.php'; ?>
<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id'])) {
    die("You are not logged in.");
}
?>

<div class="container">
    <h2>My Profile</h2>

    <!-- Display message from controller -->
    <?php if (!empty($message)): ?>
        <p class="<?php echo strpos(strtolower($message), 'failed') !== false || strpos(strtolower($message), 'already') !== false || strpos(strtolower($message), 'password') !== false ? 'error' : 'message'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <!-- Check if user data is available -->
    <?php if (empty($user) || !is_array($user)): ?>
        <p class="error">Error: User data not found. Please try logging in again.</p>
        <?php error_log("User data is empty or invalid in profile.php: " . print_r($user, true)); ?>
    <?php else: ?>
        <!-- Debug profile_image value and path -->
        <?php
            $image_path = 'C:\xampp\htdocs\employee_management_system1\uploads\\' . ($user['profile_image'] ?? '');
            $web_path = '/employee_management_system1/uploads/' . ($user['profile_image'] ?? '');
            error_log("Profile image value: " . ($user['profile_image'] ?? 'null'));
            error_log("Image path checked: " . $image_path);
            error_log("Web path for image: " . $web_path);
            error_log("File exists check: " . (file_exists($image_path) ? 'true' : 'false'));
            if (!file_exists($image_path) && !empty($user['profile_image'])) {
                error_log("File does not exist at: $image_path");
            }
        ?>

        <!-- Show profile image if exists -->
        <?php if (!empty($user['profile_image']) && file_exists($image_path)): ?>
            <img src="<?php echo htmlspecialchars($web_path); ?>?t=<?php echo time(); ?>" width="120" alt="Profile Image">
        <?php else: ?>
            <p><em>No profile image uploaded.</em></p>
        <?php endif; ?>

        <form method="POST" action="index.php?page=update_profile" enctype="multipart/form-data">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($user['profile_image'] ?? ''); ?>">
            <div>
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>
            <div>
                <label>New Password (leave blank to keep current)</label>
                <input type="password" name="password">
            </div>
            <div>
                <label>Role</label>
                <input type="text" value="<?php echo htmlspecialchars($user['role'] ?? ''); ?>" readonly>
            </div>
            <div>
                <label>Upload Profile Image</label>
                <input type="file" name="profile_image" accept="image/*">
            </div>
            <button type="submit">Update Profile</button>
        </form>
    <?php endif; ?>
</div>
<?php include 'app/views/template/footer.php'; ?>