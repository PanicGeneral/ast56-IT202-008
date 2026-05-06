<?php
require(__DIR__ . "/../../../lib/functions.php");

if (!has_role("Admin")) {
    flash("No permission", "danger");
    die(header("Location: " . get_url("landing.php")));
}

$id = se($_GET, "id", -1, false);

if ($id > 0) {

    $db = getDB();

    $stmt = $db->prepare("DELETE FROM `IT202-S26-Manga`
    WHERE id = :id");

    try {

        $stmt->execute([
            ":id" => $id
        ]);

        flash("Deleted manga", "success");

    } catch (PDOException $e) {

        error_log(var_export($e, true));

        flash("Error deleting manga", "danger");
    }
}

header("Location: " . get_url("admin/list_manga.php"));
exit();