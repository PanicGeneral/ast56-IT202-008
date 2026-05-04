<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $text = se($_POST, "text", "", false);
    $manga = [];

    if ($action === "fetch" && $text) {

        //FETCH FROM API
        $result = fetch_manga($text);

        error_log("Data from API: " . var_export($result, true));

        if ($result) {
            $manga = $result;

            $manga["api_id"] = $manga["id"];
            unset($manga["id"]);

            $manga["is_api"] = 1;
        } else {
            flash("No manga found", "warning");
        }
    } else if ($action === "create") {

        //CREATE MANUALLY
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

        foreach ($_POST as $k => $v) {
            if (in_array($k, $allowed)) {
                $manga[$k] = $v;
            }
        }

        $manga["api_id"] = null;
        $manga["is_api"] = 0;

        error_log("Manual Manga: " . var_export($manga, true));
    } else {
        flash("You must provide a search text", "warning");
    }

    //INSERT INTO DB
    if (!empty($manga)) {
        $db = getDB();

        if (!empty($manga["api_id"])) {
            $check = "SELECT id FROM `IT202-S26-Manga` WHERE api_id = :api_id";
            $stmt = $db->prepare($check);
            $stmt->execute([":api_id" => $manga["api_id"]]);
            $exists = $stmt->fetch();

            if ($exists) {
                flash("Manga already exists in database", "warning");
                return;
            }
        }

        $query = "INSERT INTO `IT202-S26-Manga` ";
        $columns = [];
        $params = [];

        foreach ($manga as $k => $v) {
            $columns[] = "`$k`";
            $params[":$k"] = $v;
        }

        $query .= "(" . join(",", $columns) . ")";
        $query .= " VALUES (" . join(",", array_keys($params)) . ")";

        error_log("Query: " . $query);
        error_log("Params: " . var_export($params, true));

        try {
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            flash("Inserted record " . $db->lastInsertId(), "success");
        } catch (PDOException $e) {
            error_log("DB Error: " . var_export($e, true));
            flash("Error inserting record", "danger");
        }
    }
}
?>

<div class="container-fluid">
    <h3>Create or Fetch Manga</h3>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('fetch')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('create')">Create</a>
        </li>
    </ul>

    <!--FETCH FORM -->
    <div id="fetch" class="tab-target">
        <form method="POST">
            <label>Manga Search</label>
            <input type="text" name="text" required>
            <input type="hidden" name="action" value="fetch">
            <input type="submit" value="Fetch" class="btn btn-primary">
        </form>
    </div>

    <!--CREATE FORM -->
    <div id="create" style="display:none;" class="tab-target">
        <form method="POST">

            <input name="title" placeholder="Title" required>
            <input name="sub_title" placeholder="Sub Title">
            <input name="status" placeholder="Status">
            <input name="type" placeholder="Type">

            <textarea name="summary" placeholder="Summary"></textarea>
            <input name="thumb" placeholder="Image URL">
            <input name="genres" placeholder="Genres (comma separated)">

            <label>NSFW</label>
            <select name="nsfw">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>

            <input type="hidden" name="action" value="create">
            <input type="submit" value="Create" class="btn btn-primary">
        </form>
    </div>
</div>

<script>
    function switchTab(tab) {
        let eles = document.getElementsByClassName("tab-target");
        for (let ele of eles) {
            ele.style.display = (ele.id === tab) ? "block" : "none";
        }
    }
</script>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>