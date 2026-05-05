<?php
require(__DIR__ . "/../../partials/nav.php");

if (is_logged_in(true)) {
    error_log("Session data: " . var_export($_SESSION, true));
}

/*
 * Get filters from URL
 */
$text = se($_GET, "text", "", false);
$type = se($_GET, "type", "", false);
$nsfw = se($_GET, "nsfw", "", false);

/*
 * Build query dynamically
 */
$params = [];
$query = "SELECT * FROM `IT202-S26-Manga` WHERE 1=1";

if (!empty($text)) {
    $query .= " AND title LIKE :text";
    $params[":text"] = "%$text%";
}

if (!empty($type) && $type !== "All") {
    $query .= " AND type = :type";
    $params[":type"] = $type;
}

if ($nsfw !== "" && ($nsfw === "0" || $nsfw === "1")) {
    $query .= " AND nsfw = :nsfw";
    $params[":nsfw"] = $nsfw;
}

/*
 * Run query
 */
$db = getDB();
$stmt = $db->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$results = [];

try {
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("DB ERROR: " . $e->getMessage());
    flash("Error loading manga", "danger");
}

/*
 * Form config
 */
$form = [
    [
        "type" => "text",
        "id" => "text",
        "name" => "text",
        "label" => "Search Title",
        "value" => se($_GET, "text", "", false),
    ],
    [
    "type" => "select",
    "id" => "type",
    "name" => "type",
    "label" => "Type",
    "options" => [
        ["" => "All"],
        ["japan" => "Japan"],
        ["korea" => "Korea"],
        ["china" => "China"]
    ],
    "value" => se($_GET, "type", "", false),
],
    [
        "type" => "select",
        "id" => "nsfw",
        "name" => "nsfw",
        "label" => "NSFW",
        "options" => [
            ["" => "All"],
            ["0" => "No"],
            ["1" => "Yes"]
        ],
        "value" => se($_GET, "nsfw", "", false),
    ]
];
?>

<div class="container-fluid">
    <h1>My Manga Collection</h1>

    <!-- FILTER FORM -->
    <div>
        <form>
            <div class="row">
                <?php foreach ($form as $field): ?>
                    <div class="col">
                        <?php render_input($field); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php render_button(["text" => "Filter", "type" => "submit"]); ?>
            <a href="?" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    <!-- RESULTS -->
    <?php if (count($results) == 0) : ?>
        <p>No manga found</p>
    <?php else : ?>
        <div class="row">
            <?php foreach ($results as $manga): ?>
                <div class="col-md-4 mb-3">
                    <?php render_manga_card($manga); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require(__DIR__ . "/../../partials/flash.php");
?>