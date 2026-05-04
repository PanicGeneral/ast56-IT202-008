<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

$id = $_GET["id"] ?? -1;

//UPDATE LOGIC
if (isset($_POST["title"])) {

    $allowed = [
        "title",
        "sub_title",
        "status",
        "type",
        "summary",
        "thumb",
        "genres",
        "nsfw"
    ];

    $manga = [];

    foreach ($_POST as $k => $v) {
        if (in_array($k, $allowed)) {
            $manga[$k] = trim($v);
        }
    }

    $db = getDB();
    $query = "UPDATE `IT202-S26-Manga` SET ";
    $params = [];

    foreach ($manga as $k => $v) {
        if ($params) {
            $query .= ",";
        }
        $query .= "`$k`=:$k";
        $params[":$k"] = $v;
    }

    $query .= " WHERE id = :id";
    $params[":id"] = $id;

    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Updated manga", "success");
    } catch (PDOException $e) {
        echo "<pre>";
        print_r($e->getMessage());
        die();
    }
}

//FETCH EXISTING DATA
$manga = [];

if ($id > -1) {
    $db = getDB();
    $query = "SELECT * FROM `IT202-S26-Manga` WHERE id = :id";

    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($r) {
            $manga = $r;
        }
    } catch (PDOException $e) {
        flash("Error fetching manga", "danger");
    }
} else {
    flash("Invalid id", "danger");
    die(header("Location:" . get_url("admin/list_manga.php")));
}
?>

<div class="container-fluid">
    <h3>Edit Manga</h3>

    <form method="POST">

        <input name="title" placeholder="Title" required value="<?php se($manga, "title"); ?>">

        <input name="sub_title" placeholder="Sub Title" value="<?php se($manga, "sub_title"); ?>">

        <input name="status" placeholder="Status" value="<?php se($manga, "status"); ?>">

        <input name="type" placeholder="Type" value="<?php se($manga, "type"); ?>">

        <textarea name="summary" placeholder="Summary"><?php se($manga, "summary"); ?></textarea>

        <input name="thumb" placeholder="Image URL" value="<?php se($manga, "thumb"); ?>">

        <input name="genres" placeholder="Genres (comma separated)" value="<?php se($manga, "genres"); ?>">

        <label>NSFW</label>
        <select name="nsfw">
            <option value="0" <?php echo (se($manga, "nsfw", 0, false) == 0 ? "selected" : ""); ?>>No</option>
            <option value="1" <?php echo (se($manga, "nsfw", 0, false) == 1 ? "selected" : ""); ?>>Yes</option>
        </select>

        <br><br>
        <input type="submit" value="Update" class="btn btn-primary">
    </form>

    <?php if (!empty($manga["thumb"])): ?>
        <h4>Preview</h4>
        <img src="<?php se($manga["thumb"]); ?>" width="150">
    <?php endif; ?>

</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>