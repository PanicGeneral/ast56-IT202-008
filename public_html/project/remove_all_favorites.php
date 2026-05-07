<?php
require(__DIR__ . "/../../lib/functions.php");

session_start();

is_logged_in(true);

$db = getDB();

$stmt = $db->prepare("
DELETE FROM `IT202-S26-UserFavorites`
WHERE user_id = :uid
");

$stmt->execute([
    ":uid" => get_user_id()
]);

flash("All favorites removed", "success");

header("Location: " . get_url("favorites.php"));

exit();
?>