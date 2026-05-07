<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {

    flash("No permission", "danger");

    header("Location: " . get_url("landing.php"));

    exit();
}

$manga_id = se($_GET, "manga_id", -1, false);

if ($manga_id < 1) {

    flash("Invalid manga", "danger");

    header("Location: " . get_url("admin/all_favorites.php"));

    exit();
}

$db = getDB();

/*
 * Get manga info
 */
$stmt = $db->prepare("
SELECT *
FROM `IT202-S26-Manga`
WHERE id = :id
LIMIT 1
");

$stmt->execute([
    ":id" => $manga_id
]);

$manga = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$manga) {

    flash("Manga not found", "danger");

    header("Location: " . get_url("admin/all_favorites.php"));

    exit();
}

/*
 * Get associated users
 */
$stmt = $db->prepare("
SELECT
    uf.user_id,
    u.username,
    uf.created

FROM `IT202-S26-UserFavorites` uf

JOIN Users u
    ON uf.user_id = u.id

WHERE uf.manga_id = :mid
AND uf.is_active = 1

ORDER BY u.username ASC
");

$stmt->execute([
    ":mid" => $manga_id
]);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">

    <h1>

        Users Who Favorited:
        <?php se($manga, "title"); ?>

    </h1>

    <p class="text-muted">

        Total Users:
        <?php echo count($users); ?>

    </p>

    <?php if (empty($users)) : ?>

        <div class="alert alert-info">

            No users associated with this manga.

        </div>

    <?php else : ?>

        <div class="table-responsive">

            <table class="table table-striped table-bordered align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>Username</th>
                        <th>Profile</th>
                        <th>Favorited On</th>
                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($users as $user) : ?>

                        <tr>

                            <td>

                                <?php echo htmlspecialchars($user["username"]); ?>

                            </td>

                            <td>

                                <a
                                    href="<?php echo get_url('public_profile.php', true); ?>?id=<?php echo $user["user_id"]; ?>"
                                    class="btn btn-primary btn-sm">

                                    View Profile

                                </a>

                            </td>

                            <td>

                                <?php
                                echo date(
                                    "F j, Y",
                                    strtotime($user["created"])
                                );
                                ?>

                            </td>

                            <td>

                                <form
                                    method="POST"
                                    action="<?php echo get_url('remove_favorite.php', true); ?>"
                                    class="d-inline">

                                    <input
                                        type="hidden"
                                        name="manga_id"
                                        value="<?php echo $manga_id; ?>">

                                    <input
                                        type="hidden"
                                        name="user_id"
                                        value="<?php echo $user["user_id"]; ?>">

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm">

                                        Remove Association

                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

    <a
        href="<?php echo get_url('admin/all_favorites.php', true); ?>"
        class="btn btn-secondary mt-3">

        Back to All Favorites

    </a>

</div>

<?php
require(__DIR__ . "/../../../partials/flash.php");
?>