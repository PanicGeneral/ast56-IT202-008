<?php
require(__DIR__ . "/../../partials/nav.php");


$id = se($_GET, "id", -1, false);

$source = se($_GET, "source", "landing", false);

$profile_user_id = se($_GET, "user_id", -1, false);

if ($id < 1) {

    flash("Invalid manga id", "danger");

    header("Location: " . get_url("landing.php"));
    exit();
}

$db = getDB();

$stmt = $db->prepare("
SELECT * FROM `IT202-S26-Manga`
WHERE id = :id
LIMIT 1
");

$result = null;

try {

    $stmt->execute([
        ":id" => $id
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {

        flash("Manga not found", "warning");

        header("Location: " . get_url("landing.php"));
        exit();
    }
} catch (PDOException $e) {

    error_log(var_export($e, true));

    flash("Error loading manga", "danger");

    header("Location: " . get_url("landing.php"));
    exit();
}
?>

<style>
    .manga-card {
        max-width: 900px;
        margin: auto;
        border-radius: 12px;
    }

    .manga-img {
        border-radius: 10px;
        max-width: 100%;
    }
</style>

<div class="container mt-4">

    <div class="card shadow manga-card">

        <div class="card-body">

            <?php if ($result) : ?>

                <h2><?php se($result, "title"); ?></h2>

                <p>
                    <strong>Sub Title:</strong>
                    <?php se($result, "sub_title"); ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <?php se($result, "status"); ?>
                </p>

                <p>
                    <strong>Type:</strong>
                    <?php se($result, "type"); ?>
                </p>

                <p>
                    <strong>Genres:</strong>
                    <?php se($result, "genres"); ?>
                </p>

                <p>
                    <strong>Summary:</strong>
                    <?php se($result, "summary"); ?>
                </p>

                <?php if (!empty($result["thumb"])) : ?>

                    <img
                        class="manga-img"
                        src="<?php se($result, "thumb"); ?>"
                        width="250">

                <?php endif; ?>

                <br><br>

                <?php

                $back_url = "landing.php";
                $back_text = "Back to Collection";

                if ($source === "favorites") {

                    $back_url = "favorites.php";
                    $back_text = "Back to Favorites";
                }

                if ($source === "profile" && $profile_user_id > 0) {

                    $back_url = "public_profile.php?id=" . $profile_user_id;
                    $back_text = "Back to Profile";
                }

                if ($source === "unassociated") {

                    $back_url = "admin/unassociated_manga.php";
                    $back_text = "Back to Unassociated Manga";
                }
                ?>

                <a
                    class="btn btn-secondary"
                    href="<?php echo get_url($back_url); ?>">

                    <?php echo $back_text; ?>

                </a>

            <?php else : ?>

                <p>Manga not found</p>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>