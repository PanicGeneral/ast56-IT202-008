<?php
require(__DIR__ . "/../../partials/nav.php");

$user_id = se($_GET, "id", -1, false);

$text = se($_GET, "text", "", false);
$type = se($_GET, "type", "", false);
$nsfw = se($_GET, "nsfw", "", false);

$limit = (int)se($_GET, "limit", 10, false);

if ($limit < 1 || $limit > 100) {

    $limit = 10;
}

if ($user_id < 1) {

    flash("Invalid user", "danger");

    header("Location: " . get_url("landing.php"));

    exit();
}

$db = getDB();

$stmt = $db->prepare("
SELECT id, username, created
FROM Users
WHERE id = :id
LIMIT 1
");

$stmt->execute([
    ":id" => $user_id
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {

    flash("User not found", "danger");

    header("Location: " . get_url("landing.php"));

    exit();
}

$query = "
SELECT m.*
FROM `IT202-S26-UserFavorites` uf
JOIN `IT202-S26-Manga` m
    ON uf.manga_id = m.id
WHERE uf.user_id = :uid
AND uf.is_active = 1
";

$params = [
    ":uid" => $user_id
];

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

$query .= " ORDER BY uf.created DESC LIMIT :limit";

$params[":limit"] = $limit;

$stmt = $db->prepare($query);

foreach ($params as $key => $value) {

    if ($key === ":limit") {

        $stmt->bindValue($key, $value, PDO::PARAM_INT);
    } else {

        $stmt->bindValue($key, $value);
    }
}

$stmt->execute();

$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">

    <div class="mb-4">

        <h1>
            <?php se($user, "username"); ?>
        </h1>
        <p class="text-muted">

            Joined:
            <?php echo date("F j, Y", strtotime($user["created"])); ?>

        </p>
        <p class="text-muted">

            Showing <?php echo count($favorites); ?> favorite manga

        </p>

    </div>

    <form class="mb-4">

        <input
            type="hidden"
            name="id"
            value="<?php echo $user_id; ?>">

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
                    placeholder="Search Title"
                    value="<?php se($_GET, "text"); ?>">

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

                    <option value="">All</option>

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
                    href="public_profile.php?id=<?php echo $user_id; ?>"
                    class="btn btn-secondary">

                    Reset

                </a>

            </div>

        </div>

    </form>

    <?php if (empty($favorites)) : ?>

        <div class="alert alert-info">

            No results available.

        </div>

    <?php else : ?>

        <div class="row">

            <?php foreach ($favorites as $manga) : ?>

                <div class="col-12 col-md-6 col-lg-4 mb-3">

                    <div class="card h-100 mx-auto" style="width: 20rem;">

                        <?php if (!empty($manga["thumb"])) : ?>

                            <div class="ratio ratio-1x1 d-flex justify-content-center" style="height:250px">

                                <img
                                    src="<?php se($manga, "thumb"); ?>"
                                    class="img-fluid object-fit-contain"
                                    alt="Manga Cover">

                            </div>

                        <?php endif; ?>

                        <div class="card-body">

                            <h5 class="card-title">

                                <?php se($manga, "title"); ?>

                            </h5>

                            <p class="mb-1">

                                <strong>Type:</strong>
                                <?php se($manga, "type"); ?>

                            </p>

                            <p class="mb-1">

                                <strong>Status:</strong>
                                <?php se($manga, "status"); ?>

                            </p>

                            <p class="mb-1">

                                <strong>Genres:</strong>
                                <?php se($manga, "genres"); ?>

                            </p>

                            <p class="mb-2">

                                <strong>NSFW:</strong>

                                <?php echo se($manga, "nsfw", 0, false) ? "Yes" : "No"; ?>

                            </p>

                            <p class="card-text">

                                <?php

                                $summary = se($manga, "summary", "", false);

                                if (strlen($summary) > 120) {

                                    $summary = substr($summary, 0, 120) . "...";
                                }

                                echo htmlspecialchars($summary);

                                ?>

                            </p>

                        </div>

                        <div class="card-footer">

                            <a
                                href="<?php echo get_url('view_details.php', true); ?>?id=<?php se($manga, "id"); ?>&source=profile&user_id=<?php echo $user_id; ?>"
                                class="btn btn-primary btn-sm">

                                View

                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<?php
require(__DIR__ . "/../../partials/flash.php");
?>