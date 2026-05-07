<?php
require(__DIR__ . "/../../partials/nav.php");

is_logged_in(true);

$user_id = get_user_id();

$text = se($_GET, "text", "", false);
$type = se($_GET, "type", "", false);
$nsfw = se($_GET, "nsfw", "", false);

$params = [
    ":uid" => $user_id
];

$query = "
SELECT m.*
FROM `IT202-S26-userfavorites` uf
JOIN `IT202-S26-Manga` m
    ON uf.manga_id = m.id
WHERE uf.user_id = :uid
AND uf.is_active = 1
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

$query .= " ORDER BY uf.created DESC";

$db = getDB();

$stmt = $db->prepare($query);

$stmt->execute($params);

$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">

    <h1>My Favorites</h1>

    <p class="text-muted">
        Total Favorites: <?php echo count($favorites); ?>
    </p>

    <form class="mb-4">

        <div class="row">

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

            <div class="col-md-2 d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-primary">

                    Filter

                </button>

                <a
                    href="favorites.php"
                    class="btn btn-secondary">

                    Reset

                </a>

            </div>

        </div>

    </form>

    <?php if (empty($favorites)) : ?>

        <div class="alert alert-info">
            You have no favorite manga yet.
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
                                href="<?php echo get_url('view_details.php', true); ?>?id=<?php se($manga, "id"); ?>&source=favorites"
                                class="btn btn-primary btn-sm">

                                View

                            </a>

                            <form
                                method="POST"
                                action="<?php echo get_url('remove_favorite.php', true); ?>"
                                class="d-inline">

                                <input
                                    type="hidden"
                                    name="manga_id"
                                    value="<?php se($manga, "id"); ?>">

                                <button
                                    type="submit"
                                    class="btn btn-danger btn-sm">

                                    Remove

                                </button>

                            </form>

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