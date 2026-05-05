<?php require(__DIR__ . "/../../partials/nav.php"); ?>

<div class="container mt-5" style="max-width: 500px;">
    <h3 class="mb-4 text-center">Login</h3>

    <form onsubmit="return validate(this)" method="POST">

        <div class="mb-3">
            <label for="email" class="form-label">Email or Username</label>
            <input id="email" 
                   type="text" 
                   name="email" 
                   class="form-control"
                   required 
                   value="<?php echo se($_POST, 'email'); ?>" />
        </div>

        <div class="mb-3">
            <label for="pw" class="form-label">Password</label>
            <input type="password" 
                   id="pw" 
                   name="password" 
                   class="form-control"
                   required 
                   minlength="8" />
        </div>

        <div class="d-grid">
            <button class="btn btn-primary" type="submit">Login</button>
        </div>

    </form>
</div>

<script>
function validate(form) {
    let email = form.email.value;
    let password = form.password.value;

    let isValid = true;

    let flashDiv = document.getElementById("flash");
    if (flashDiv) flashDiv.innerHTML = "";

    if (!isNotEmpty(email)) {
        flash("Email/Username cannot be empty", "danger");
        isValid = false;
    }

    if (!isValidPassword(password)) {
        flash("Password must be at least 8 characters", "danger");
        isValid = false;
    }

    return isValid;
}
</script>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>