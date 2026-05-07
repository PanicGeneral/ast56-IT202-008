<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

$title = se($_GET, "title", "", false);
$sort = se($_GET, "sort", "newest", false);
$limit = se($_GET, "limit", 10, false);

if (!is_numeric($limit)) {
    $limit = 10;
}

$limit = (int)$limit;

if ($limit < 1) {
    $limit = 1;
}

if ($limit > 100) {
    $limit = 100;
}

$order = "ORDER BY created DESC";

if ($sort == "title_asc") {
    $order = "ORDER BY title ASC";
} else if ($sort == "title_desc") {
    $order = "ORDER BY title DESC";
}

$query = "SELECT id, title, sub_title, summary, status, type, thumb, genres, nsfw, is_api 
FROM `IT202-S26-Manga`
WHERE title LIKE :title
$order
LIMIT :limit";

$db = getDB();
$stmt = $db->prepare($query);
$results = [];

try {

    $stmt->bindValue(":title", "%$title%");
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);

    $stmt->execute();

    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching manga " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}
?>

<div class="container-fluid">
    <h3>List Manga</h3>

    <form method="GET" class="row mb-4">

        <div class="col-md-3">
            <input class="form-control"
                type="text"
                name="title"
                placeholder="Search title"
                value="<?php se($_GET, 'title'); ?>">
        </div>

        <div class="col-md-2">
            <select class="form-select" name="sort">

                <option value="newest">
                    Newest
                </option>

                <option value="title_asc"
                    <?php echo se($_GET, "sort", "", false) == "title_asc" ? "selected" : ""; ?>>
                    Title A-Z
                </option>

                <option value="title_desc"
                    <?php echo se($_GET, "sort", "", false) == "title_desc" ? "selected" : ""; ?>>
                    Title Z-A
                </option>

            </select>
        </div>

        <div class="col-md-2">
            <input class="form-control"
                type="number"
                name="limit"
                min="1"
                max="100"
                value="<?php se($_GET, 'limit', 10); ?>">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary">
                Apply
            </button>
        </div>

    </form>

    <style>
        table td {
            vertical-align: middle;
        }

        .summary-cell {
            max-width: 300px;
            word-wrap: break-word;
        }

        .genre-cell {
            max-width: 250px;
            word-wrap: break-word;
        }

        img.thumb {
            border-radius: 6px;
        }
    </style>

    <?php if (count($results) == 0) : ?>
        <div class="alert alert-warning">
            No results to show
        </div>
    <?php else : ?>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>

                    <th>Title</th>
                    <th>Sub Title</th>
                    <th>Summary</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>Genres</th>
                    <th>NSFW</th>
                    <th>Source</th>
                    <th>Thumbnail</th>
                    <th>Actions</th>

                </tr>
            </thead>

            <tbody>
                <?php foreach ($results as $record) : ?>
                    <tr>

                        <td><?php se($record["title"]); ?></td>

                        <td><?php se($record["sub_title"]); ?></td>

                        <td class="summary-cell">

                            <?php
                            $summary = se($record, "summary", "", false);

                            if (strlen($summary) > 120) {
                                echo substr($summary, 0, 120) . "...";
                            } else {
                                echo $summary;
                            }
                            ?>

                        </td>

                        <td><?php se($record["status"]); ?></td>

                        <td><?php se($record["type"]); ?></td>

                        <td class="genre-cell">
                            <?php se($record["genres"]); ?>
                        </td>

                        <td><?php echo $record["nsfw"] ? "Yes" : "No"; ?></td>

                        <td><?php echo $record["is_api"] ? "API" : "Manual"; ?></td>

                        <td>
                            <?php if (!empty($record["thumb"])): ?>
                                <img src="<?php se($record["thumb"]); ?>"
                                    width="80"
                                    class="thumb">
                            <?php endif; ?>
                        </td>

                        <td>

                            <a class="btn btn-primary btn-sm mb-1"
                                href="<?php echo get_url("admin/view_manga.php"); ?>?id=<?php se($record, "id"); ?>">
                                Details
                            </a>

                            <br>

                            <a class="btn btn-warning btn-sm mb-1"
                                href="<?php echo get_url("admin/edit_manga.php"); ?>?id=<?php se($record, "id"); ?>">
                                Edit
                            </a>

                            <br>

                            <a class="btn btn-danger btn-sm"
                                href="<?php echo get_url("admin/delete_manga.php"); ?>?id=<?php se($record, "id"); ?>">
                                Delete
                            </a>

                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>