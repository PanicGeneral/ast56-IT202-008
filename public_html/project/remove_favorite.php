<?php
require(__DIR__ . "/../../lib/functions.php");

is_logged_in(true);

$manga_id = se($_POST, "manga_id", -1, false);

if ($manga_id > 0) {

    remove_favorite(get_user_id(), $manga_id);
}

header("Location: " . get_url("favorites.php"));

exit();
?>