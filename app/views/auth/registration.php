<?php include 'app/views/template/header.php'; ?>
<div class="container">
    <h2>Register</h2>

    <?php if (!empty($error)): ?>
        <p class="error" style="color: red;">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php elseif (!empty($message)): ?>
        <p class="message" style="color: green;">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php else: ?>
        <p>Please fill out the form to register.</p>
    <?php endif; ?>

    <form method="POST" onsubmit="return validatePassword()" action="index.php?page=register">
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
            <input type="password" name="password" id="password" required>
            <p id="password-error" style="color: red; display: none;">
                Password must be at least 8 characters.
            </p>
        </div>
        <button type="submit">Register</button>
    </form>

    <p><a href="index.php?page=login">Back to Login</a></p>
</div>

<script>
function validatePassword() {
    const passwordInput = document.getElementById("password");
    const errorMessage = document.getElementById("password-error");

    if (passwordInput.value.length < 8) {
        errorMessage.style.display = "block";
        return false;
    } else {
        errorMessage.style.display = "none";
        return true;
    }
}
</script>

<?php include 'app/views/template/footer.php'; ?>