<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $text = se($_POST, "text", "", false);
    $mangaList = [];

    if ($action === "fetch" && $text) {

        // FETCH MULTIPLE FROM API
        $result = search_series($text);

        error_log("Data from API: " . var_export($result, true));

        if ($result) {
            foreach ($result as $manga) {

                // map API fields
                $manga["api_id"] = $manga["id"] ?? null;
                unset($manga["id"]);

                $manga["is_api"] = 1;
                $manga["title"] = $manga["title"] ?? "";
                $manga["thumb"] = $manga["thumb"] ?? "";
                $manga["type"] = $manga["type"] ?? "";
                $manga["nsfw"] = isset($manga["nsfw"]) ? (int)$manga["nsfw"] : 0;
                $manga["sub_title"] = $manga["sub_title"] ?? null;
                $manga["status"] = $manga["status"] ?? null;
                $manga["summary"] = $manga["summary"] ?? null;
                $manga["genres"] = $manga["genres"] ?? null;

                $mangaList[] = $manga;
            }
        } else {
            flash("No manga found", "warning");
        }

    } else if ($action === "create") {

        // CREATE MANUALLY
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
                $manga[$k] = $v;
            }
        }

        $manga["api_id"] = null;
        $manga["is_api"] = 0;
        $manga["nsfw"] = isset($manga["nsfw"]) ? (int)$manga["nsfw"] : 0;

        $mangaList[] = $manga;

        error_log("Manual Manga: " . var_export($manga, true));
    } else {
        flash("You must provide a search text", "warning");
    }

    // INSERT
    if (count($mangaList) > 0) {

        error_log("Transformed mangaList " . var_export($mangaList, true));

        try {
            $r = insert("IT202-S26-Manga", $mangaList, [
                "debug" => true,
                "update_duplicate" => true
            ]);

            if ($r["lastInsertId"] || $r["rowCount"] > 0) {
                flash("Inserted record " . $r["lastInsertId"], "success");
            } else {
                flash("Error inserting record", "warning");
            }

        } catch (PDOException $e) {
            error_log("Something broke with the query " . var_export($e, true));
            flash("An error occurred", "danger");
        } catch (Exception $e) {
            error_log("Something broke with the query " . var_export($e, true));
            flash("An error occurred: " . $e->getMessage(), "danger");
        }

    } else {
        flash("No manga fetched or provided", "warning");
    }
}
?>

<div class="container-fluid">
    <h3>Create or Fetch Manga Series</h3>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('fetch')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('create')">Create</a>
        </li>
    </ul>

    <!-- FETCH FORM -->
    <div id="fetch" class="tab-target">
        <form method="POST">
            <label>Manga Search</label>
            <input type="text" name="text" required>
            <input type="hidden" name="action" value="fetch">
            <input type="submit" value="Fetch" class="btn btn-primary">
        </form>
    </div>

    <!-- CREATE FORM -->
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