<?php
require(__DIR__ . "/../../lib/functions.php");

session_start();

if (!has_role("Admin")) {

    flash("No permission", "danger");

    header("Location: " . get_url("landing.php"));

    exit();
}

$users = $_POST["users"] ?? [];
$manga = $_POST["manga"] ?? [];

$db = getDB();

foreach ($users as $uid) {

    foreach ($manga as $mid) {

        /*
         * Check if association exists
         */
        $stmt = $db->prepare("
            SELECT *
            FROM `IT202-S26-UserFavorites`
            WHERE user_id = :uid
            AND manga_id = :mid
            LIMIT 1
        ");

        $stmt->execute([
            ":uid" => $uid,
            ":mid" => $mid
        ]);

        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        /*
         * Toggle association
         */
        if ($existing) {

            remove_favorite($uid, $mid);

        } else {

            add_favorite($uid, $mid);
        }
    }
}

flash("Associations updated", "success");

header("Location: " . get_url("admin/manage_associations.php"));

exit();
?>