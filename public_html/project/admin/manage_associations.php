<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {

    flash("No permission", "danger");

    header("Location: " . get_url("landing.php"));

    exit();
}

$username = se($_GET, "username", "", false);
$text = se($_GET, "text", "", false);

$db = getDB();

$users = [];
$manga = [];

/*
 * Search users
 */
if (!empty($username)) {

    $stmt = $db->prepare("
        SELECT id, username
        FROM Users
        WHERE username LIKE :username
        LIMIT 25
    ");

    $stmt->execute([
        ":username" => "%$username%"
    ]);

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*
 * Search manga
 */
if (!empty($text)) {

    $stmt = $db->prepare("
        SELECT id, title
        FROM `IT202-S26-Manga`
        WHERE title LIKE :text
        LIMIT 25
    ");

    $stmt->execute([
        ":text" => "%$text%"
    ]);

    $manga = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid">

    <h1>Manage Associations</h1>

    <form method="GET" class="mb-4">

        <div class="row">

            <div class="col-md-6">

                <input
                    type="text"
                    name="username"
                    class="form-control"
                    placeholder="Search Username"
                    value="<?php echo htmlspecialchars($username); ?>">

            </div>

            <div class="col-md-6">

                <input
                    type="text"
                    name="text"
                    class="form-control"
                    placeholder="Search Manga"
                    value="<?php echo htmlspecialchars($text); ?>">

            </div>

        </div>

        <div class="mt-3 d-flex gap-2">

            <button
                type="submit"
                class="btn btn-primary">

                Search

            </button>

            <a
                href="manage_associations.php"
                class="btn btn-secondary">

                Reset

            </a>

        </div>

    </form>

    <form
        method="POST"
        action="<?php echo get_url('toggle_associations.php', true); ?>">

        <div class="row">

            <div class="col-md-6">

                <h3>Users</h3>

                <?php if (empty($users)) : ?>

                    <div class="alert alert-info">

                        No users available.

                    </div>

                <?php else : ?>

                    <div class="list-group">

                        <?php foreach ($users as $user) : ?>

                            <label class="list-group-item">

                                <input
                                    class="form-check-input me-2"
                                    type="checkbox"
                                    name="users[]"
                                    value="<?php echo $user["id"]; ?>">

                                <?php echo htmlspecialchars($user["username"]); ?>

                            </label>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

            <div class="col-md-6">

                <h3>Manga</h3>

                <?php if (empty($manga)) : ?>

                    <div class="alert alert-info">

                        No manga available.

                    </div>

                <?php else : ?>

                    <div class="list-group">

                        <?php foreach ($manga as $row) : ?>

                            <label class="list-group-item">

                                <input
                                    class="form-check-input me-2"
                                    type="checkbox"
                                    name="manga[]"
                                    value="<?php echo $row["id"]; ?>">

                                <?php echo htmlspecialchars($row["title"]); ?>

                            </label>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

        </div>

        <div class="mt-4">

            <button
                type="submit"
                class="btn btn-success">

                Apply Associations

            </button>

        </div>

    </form>

</div>

<?php
require(__DIR__ . "/../../../partials/flash.php");
?>