<?php
/**
 * Checks if user key is set in session
 */
function is_logged_in($redirect = false, $destination = "login.php")
{
    $isLoggedIn = isset($_SESSION["user"]);
    if ($redirect && !$isLoggedIn) {
        //if this triggers, the calling script won't receive a reply since die()/exit() terminates it
        flash("You must be logged in to view this page", "warning");
        $path = $destination;
        // handle relative paths
        if (!str_starts_with($path, "/")) {
            global $BASE_PATH; // pull from global scope of functions.php
            // ensure BASE_PATH ends with a slash so the url doesn't get malformed
            if (!str_ends_with($BASE_PATH, "/")) {
                $BASE_PATH .= "/";
            }
            $path = $BASE_PATH . $path; // prepend the base path
        }// the else part is for absolute paths

        die(header("Location: $path"));
    }
    return $isLoggedIn;
}
function has_role($role)
{
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] === $role) {
                return true;
            }
        }
    }
    return false;
}
/**
 * Returns the current user's username or empty string
 */
function get_username() {
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "username", "", false);
    }
    return "";
}
/**
 * Returns the current user's email or empty string
 */
function get_user_email() {
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "email", "", false);
    }
    return "";
}
/**
 * Returns the current user's id or -1
 */
function get_user_id() {
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "id", -1, false);
    }
    return -1;
}