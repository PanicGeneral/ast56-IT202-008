<?php
require(__DIR__ . "/../../lib/functions.php");

session_start();

is_logged_in(true);

$manga_id = se($_POST, "manga_id", -1, false);

$user_id = se($_POST, "user_id", get_user_id(), false);

if ($manga_id > 0) {

    remove_favorite($user_id, $manga_id);
}

if (has_role("Admin")) {

    header(
        "Location: " .
        get_url(
            "admin/manga_users.php",
            true
        ) .
        "?manga_id=" . $manga_id
    );

} else {

    header("Location: " . get_url("favorites.php"));
}

exit();
?>