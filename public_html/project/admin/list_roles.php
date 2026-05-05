<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}
//handle the toggle first so SELECT pulls fresh data
if (isset($_POST["role_id"])) {
    $role_id = se($_POST, "role_id", "", false);
    if (!empty($role_id)) {
        $db = getDB();
        // toggle is_active via negation
        $stmt = $db->prepare("UPDATE Roles SET is_active = !is_active WHERE id = :role_id");
        try {
            $stmt->execute([":role_id" => $role_id]);
            flash("Updated Role", "success");
        } catch (PDOException $e) {
            flash("There was an error toggling the role, please try again later", "danger");
            error_log("Error toggling role: " . var_export($e->errorInfo, true));
        }
    }
}
$query = "SELECT id , name, description, is_active from Roles";
$params = null;
if (isset($_POST["role"])) {
    $search = se($_POST, "role", "", false);
    $query .= " WHERE name LIKE :role";
    // for LIKE queries, we need to use wildcards that get added to the data rather than the query
    $params =  [":role" => "%$search%"];
}
// always apply some finite limit to avoid performance issues
$query .= " ORDER BY modified desc LIMIT 10";
$db = getDB();
$stmt = $db->prepare($query);
$roles = [];
try {
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $roles = $results;
    } else {
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    flash("There was an error fetching roles, please try again later", "danger");
    error_log("Error fetching roles: " . var_export($e->errorInfo, true));
}

?>
<h3>List Roles</h3>
<form method="POST">
    <?php render_input([
        "type" => "search",
        "name" => "role",
        "placeholder" => "Role Filter",
        "value" => se($_POST, "role")
    ]); ?>

    <?php render_button([
        "text" => "Search",
        "type" => "submit"
    ]); ?>
</form>
<small>Note: If you disabled Admin, you won't be able to login as Admin again until you re-enable it (may require a manual table edit).</small>
<?php
$table = [
    "data" => $roles,
    "table_class" => "table table-striped table-bordered",
    "post_self_form" => [
        "name" => "role_id",
        "label" => "Toggle",
        "classes" => "btn btn-secondary"
    ]
];
render_table($table);
?>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>