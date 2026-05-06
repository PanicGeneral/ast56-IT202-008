<?php
require(__DIR__ . "/../../partials/nav.php");

is_logged_in(true);

$user_id = get_user_id();

$favorites = get_user_favorites($user_id);
?>

<div class="container-fluid">

    <h1>My Favorites</h1>

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
                                href="<?php echo get_url('view_details.php', true); ?>?id=<?php se($manga, "id"); ?>&source=favorites""
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