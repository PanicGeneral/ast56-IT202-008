<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<h3>Register</h3>
<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email" required value="<?php echo se($_POST, 'username'); ?>" />
    </div>
    <div>
        <label for="username">Username</label>
        <input type="text" name="username" required maxlength="30" value="<?php echo se($_POST, 'username'); ?>" />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirm">Confirm</label>
        <input type="password" name="confirm" required minlength="8" />
    </div>
    <input type="submit" value="Register" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation (you'll do this on your own towards the end of Milestone1)
        //ensure it returns false for an error and true for success
        let email = form.email.value;
        let username = form.username.value;
        let password = form.password.value;
        let confirm = form.confirm.value;
        let isValid = true;

        let flashDiv = document.getElementById("flash");
        if (flashDiv) flashDiv.innerHTML = "";

        if(!isNotEmpty(email)){
            flash("Email cannot be empty","danger");
            isValid=false;
        }else if (!isValidEmail(email)){
            flash("Invalid email address", "danger");
            isValid = false;
        }
        if(!isValidUsername(username)){
            flash("Invalid username format","danger");
            isValid = false;
        }
        if(!isNotEmpty(password)){
            flash("Password cannot be empty", "danger");
            isValid = false;
        } else if (!isValidPassword(password)){
            flash("Password must be at least 8 characters long", "danger");
            isValid = false;
        }
        if(!isNotEmpty(confirm)){
            flash("Confirm cannot be empty", "danger");
            isValid = false;
        }else if (!isValidConfirm(password,confirm)){
            flash("Passwords do not match", "danger");
            isValid = false;
        }
        return isValid;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"], $_POST["password"], $_POST["confirm"], $_POST["username"])) {

    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se($_POST, "confirm", "", false);
    $username = se($_POST, "username", "", false);
    // TODO 3: validate/use
    $hasError = false;

    if (empty($email)) {
        //echo "Email must not be empty<br>";
        flash("Email must not be empty.", "danger");
        $hasError = true;
    }
    // Sanitize and validate email
    $email = sanitize_email($email);
    if (!is_valid_email($email)) {
        //echo "Invalid email address<br>";
        flash("Invalid email address.", "danger");
        $hasError = true;
    }
    if (!is_valid_username($username)) {
        flash("Username must be lowercase, alphanumerical, can only contain _ or -, and be between 3 to 30 characters", "danger");
        $hasError = true;
    }
    if (empty($password)) {
        //echo "Password must not be empty<br>";
        flash("Password must not be empty.", "danger");
        $hasError = true;
    }

    if (empty($confirm)) {
        //echo "Confirm password must not be empty<br>";
        flash("Confirm password must not be empty.", "danger");
        $hasError = true;
    }

    if (!is_valid_password($password)) {
        //echo "Password too short<br>";
        flash("Password must be at least 8 characters long.", "danger");
        $hasError = true;
    }

    if (!is_valid_confirm($password, $confirm)) {
        //echo "Passwords must match<br>";
        flash("Passwords must match.", "danger");
        $hasError = true;
    }

    if (!$hasError) {
        // TODO 4: Hash password and store record in DB
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB(); // available due to the `require()` of `functions.php`
        // Code for inserting user data into the database
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES (:email, :password, :username)");
        try {
            $stmt->execute([':email' => $email, ':password' => $hashed_password, ':username' => $username]);
            //echo "Successfully registered!<br>";
            flash("Successfully registered! You can now log in.", "success");
        }
        catch (PDOException $e){
            users_check_duplicate($e);
        }
         catch (Exception $e) {
            //echo "There was an error registering<br>"; // user-friendly message
            flash("There was an error registering. Please try again.", "danger");
            error_log("Registration Error: " . var_export($e, true)); // log the technical error for debugging
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
reset_session();
?>