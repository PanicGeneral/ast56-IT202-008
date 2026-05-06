<?php
require(__DIR__ . "/../../../partials/nav.php");

$id = se($_GET, "id", -1, false);

$db = getDB();

$stmt = $db->prepare("SELECT * FROM `IT202-S26-Manga`
WHERE id = :id LIMIT 1");

$result = null;

try {

    $stmt->execute([
        ":id" => $id
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    error_log(var_export($e, true));

    flash("Error loading manga", "danger");
}
?>

<div class="container">

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

            <img src="<?php se($result, "thumb"); ?>"
                 width="250">

        <?php endif; ?>

    <?php else : ?>

        <p>Manga not found</p>

    <?php endif; ?>

</div>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>