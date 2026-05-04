<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

$query = "SELECT id, title, sub_title, status, type, thumb, genres, nsfw, is_api FROM `IT202-S26-Manga` ORDER BY created DESC LIMIT 25";

$db = getDB();
$stmt = $db->prepare($query);
$results = [];

try {
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

    <style>
        table td {
            vertical-align: middle;
        }
    </style>

    <?php if (count($results) == 0) : ?>
        <p>No results to show</p>
    <?php else : ?>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
               
                    <th>Title</th>
                    <th>Sub Title</th>
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

                        <td><?php se($record["status"]); ?></td>

                        <td><?php se($record["type"]); ?></td>

                        <td style="max-width:250px; word-wrap:break-word;">
                            <?php se($record["genres"]); ?>
                        </td>

                        <td><?php echo $record["nsfw"] ? "Yes" : "No"; ?></td>

                        <td><?php echo $record["is_api"] ? "API" : "Manual"; ?></td>

                        <td>
                            <?php if (!empty($record["thumb"])): ?>
                                <img src="<?php se($record["thumb"]); ?>" width="80" style="border-radius:6px;">
                            <?php endif; ?>
                        </td>

                        <td>
                            <a class="btn btn-warning btn-sm"
                               href="<?php echo get_url("admin/edit_manga.php"); ?>?id=<?php se($record, "id"); ?>">
                                Edit
                            </a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>