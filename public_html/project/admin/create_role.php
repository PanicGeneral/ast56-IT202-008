<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

if (isset($_POST["name"],$_POST["description"])) {
    $name = se($_POST, "name", "", false);
    $desc = se($_POST, "description", "", false);
    if (empty($name)) {
        flash("Name is required", "warning");
    } else {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Roles (name, description, is_active) VALUES(:name, :desc, 1)");
        try {
            $stmt->execute([":name" => $name, ":desc" => $desc]);
            flash("Successfully created role $name!", "success");
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                flash("A role with this name already exists, please try another", "warning");
            } else {
                flash("There was an error creating the role, please try again later", "danger");
                error_log ("Error creating role: " . var_export($e->errorInfo, true));
            }
        }
    }
}
?>
<div class="container mt-4">
    <h3 class="text-center mb-4">Create Role</h3>

    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">

            <form method="POST">

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input class="form-control" id="name" name="name" required />
                </div>

                <div class="mb-3">
                    <label for="d" class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="d" rows="3"></textarea>
                </div>

                <div class="text-center">
                    <input class="btn btn-primary" type="submit" value="Create Role" />
                </div>

            </form>

        </div>
    </div>
</div>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>