<?php
require(__DIR__ . "/../../partials/nav.php"); // 

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

            $ambigify = false;

            if ($user) {
                $hash = $user["password"];
                unset($user["password"]);

                if (password_verify($password, $hash)) {

                    $_SESSION["user"] = $user;

                    $stmt = $db->prepare("SELECT Roles.name FROM Roles
                        JOIN UserRoles ON Roles.id = UserRoles.role_id
                        WHERE UserRoles.user_id = :user_id
                        AND Roles.is_active = 1
                        AND UserRoles.is_active = 1");

                    $stmt->execute([":user_id" => $user["id"]]);
                    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $_SESSION["user"]["roles"] = $roles ? $roles : [];

                    header("Location: landing.php");
                    exit;
                } else {
                    $ambigify = true;
                }
            } else {
                $ambigify = true;
            }

            if ($ambigify) {
                flash("Invalid login attempt. Please check your email and password.", "danger");
            }

        } catch (Exception $e) {
            flash("There was an error logging in. Please try again later.", "danger");
            error_log("Login Error: " . var_export($e, true));
        }
    }
}
?>