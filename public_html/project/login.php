<?php
ob_start();
require(__DIR__ . "/../../partials/nav.php");

if (isset($_POST["email"], $_POST["password"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);

    $hasError = false;

    if (empty($email)) {
        flash("Email/Username must not be empty.", "danger");
        $hasError = true;
    }

    if (str_contains($email, "@")) {
        $email = sanitize_email($email);
        if (!is_valid_email($email)) {
            flash("Invalid email address.", "danger");
            $hasError = true;
        }
    } else {
        $email = strtolower(trim($email));
        if (!is_valid_username($email)) {
            flash("Username must be lowercase, alphanumerical, and can only contain _ or -", "danger");
            $hasError = true;
        }
    }

    if (empty($password)) {
        flash("Password must not be empty.", "danger");
        $hasError = true;
    }

    if (!is_valid_password($password)) {
        flash("Password must be at least 8 characters long.", "danger");
        $hasError = true;
    }

    if (!$hasError) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, password, username FROM Users WHERE email = :email OR username = :email");

        try {
            $stmt->execute([":email" => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $hash = $user["password"];
                unset($user["password"]);

                if (password_verify($password, $hash)) {

                    $_SESSION["user"] = $user;

                    try {
                        $stmt = $db->prepare("SELECT Roles.name FROM Roles
                            JOIN UserRoles ON Roles.id = UserRoles.role_id
                            WHERE UserRoles.user_id = :user_id
                            AND Roles.is_active = 1
                            AND UserRoles.is_active = 1");

                        $stmt->execute([":user_id" => $user["id"]]);
                        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (Exception $e) {
                        error_log(var_export($e, true));
                    }

                    $_SESSION["user"]["roles"] = isset($roles) ? $roles : [];

                    header("Location: landing.php");
                    exit;
                }
            }

            flash("Invalid login attempt. Please check your email and password.", "danger");

        } catch (Exception $e) {
            flash("There was an error logging in. Please try again later.", "danger");
            error_log("Login Error: " . var_export($e, true));
        }
    }
}
?>

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