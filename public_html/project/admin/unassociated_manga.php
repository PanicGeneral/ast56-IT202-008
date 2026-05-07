<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {

    flash("No permission", "danger");

    header("Location: " . get_url("landing.php"));

    exit();
}

$text = se($_GET, "text", "", false);
$type = se($_GET, "type", "", false);
$nsfw = se($_GET, "nsfw", "", false);

$limit = (int)se($_GET, "limit", 10, false);

if ($limit < 1 || $limit > 100) {

    $limit = 10;
}

$params = [];

$query = "
SELECT
    m.id,
    m.title,
    m.summary,
    m.type,
    m.nsfw

FROM `IT202-S26-Manga` m

LEFT JOIN `IT202-S26-UserFavorites` uf
    ON m.id = uf.manga_id
    AND uf.is_active = 1

WHERE uf.user_id IS NULL
";

if (!empty($text)) {

    $query .= " AND m.title LIKE :text";

    $params[":text"] = "%$text%";
}

if (!empty($type) && $type !== "All") {

    $query .= " AND m.type = :type";

    $params[":type"] = $type;
}

if ($nsfw !== "" && ($nsfw === "0" || $nsfw === "1")) {

    $query .= " AND m.nsfw = :nsfw";

    $params[":nsfw"] = $nsfw;
}

$query .= "
ORDER BY m.title ASC
LIMIT :limit
";

$params[":limit"] = $limit;

$db = getDB();

$stmt = $db->prepare($query);

foreach ($params as $key => $value) {

    if ($key === ":limit") {

        $stmt->bindValue($key, $value, PDO::PARAM_INT);

    } else {

        $stmt->bindValue($key, $value);
    }
}

$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">

    <h1>Unassociated Manga</h1>

    <p class="text-muted">

        Showing <?php echo count($results); ?> unassociated manga

    </p>

    <form class="mb-4">

        <div class="row">

            <div class="col-md-2">

                <input
                    type="number"
                    name="limit"
                    class="form-control"
                    min="1"
                    max="100"
                    value="<?php echo $limit; ?>"
                    placeholder="Limit">

            </div>

            <div class="col-md-4">

                <input
                    type="text"
                    name="text"
                    class="form-control"
                    placeholder="Search Manga"
                    value="<?php echo htmlspecialchars($text); ?>">

            </div>

            <div class="col-md-3">

                <select name="type" class="form-select">

                    <option value="">All Types</option>

                    <option
                        value="japan"
                        <?php echo $type === "japan" ? "selected" : ""; ?>>

                        Japan

                    </option>

                    <option
                        value="korea"
                        <?php echo $type === "korea" ? "selected" : ""; ?>>

                        Korea

                    </option>

                    <option
                        value="china"
                        <?php echo $type === "china" ? "selected" : ""; ?>>

                        China

                    </option>

                </select>

            </div>

            <div class="col-md-3">

                <select name="nsfw" class="form-select">

                    <option value="">NSFW</option>

                    <option
                        value="0"
                        <?php echo $nsfw === "0" ? "selected" : ""; ?>>

                        No

                    </option>

                    <option
                        value="1"
                        <?php echo $nsfw === "1" ? "selected" : ""; ?>>

                        Yes

                    </option>

                </select>

            </div>

            <div class="col-md-12 mt-3 d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-primary">

                    Filter

                </button>

                <a
                    href="unassociated_manga.php"
                    class="btn btn-secondary">

                    Reset

                </a>

            </div>

        </div>

    </form>

    <?php if (empty($results)) : ?>

        <div class="alert alert-info">

            No results available.

        </div>

    <?php else : ?>

        <div class="table-responsive">

            <table class="table table-striped table-bordered align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>Manga</th>
                        <th>Type</th>
                        <th>NSFW</th>
                        <th>Summary</th>
                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($results as $row) : ?>

                        <tr>

                            <td>

                                <?php echo htmlspecialchars($row["title"]); ?>

                            </td>

                            <td>

                                <?php echo htmlspecialchars($row["type"]); ?>

                            </td>

                            <td>

                                <?php echo $row["nsfw"] ? "Yes" : "No"; ?>

                            </td>

                            <td>

                                <?php

                                $summary = $row["summary"] ?? "";

                                if (strlen($summary) > 100) {

                                    $summary = substr($summary, 0, 100) . "...";
                                }

                                echo htmlspecialchars($summary);

                                ?>

                            </td>

                            <td>

                                <a
                                    href="<?php echo get_url('view_details.php', true); ?>?id=<?php echo $row["id"]; ?>&source=unassociated"
                                    class="btn btn-primary btn-sm">

                                    View

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

<?php
require(__DIR__ . "/../../../partials/flash.php");
?>