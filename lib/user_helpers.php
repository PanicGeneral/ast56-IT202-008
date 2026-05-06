<?php
/**
 * Checks if user key is set in session
 */
function is_logged_in($redirect = false, $destination = "login.php")
{
    $isLoggedIn = isset($_SESSION["user"]);

    if ($redirect && !$isLoggedIn) {

        flash("You must be logged in to view this page", "warning");

        $path = $destination;

        if (!str_starts_with($path, "/")) {

            global $BASE_PATH;

            if (!str_ends_with($BASE_PATH, "/")) {
                $BASE_PATH .= "/";
            }

            $path = $BASE_PATH . $path;
        }

        die(header("Location: $path"));
    }

    return $isLoggedIn;
}

function has_role($role)
{
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {

        foreach ($_SESSION["user"]["roles"] as $r) {

            if ($r["name"] === $role) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Returns the current user's username or empty string
 */
function get_username()
{
    if (is_logged_in()) {
        return se($_SESSION["user"], "username", "", false);
    }

    return "";
}

/**
 * Returns the current user's email or empty string
 */
function get_user_email()
{
    if (is_logged_in()) {
        return se($_SESSION["user"], "email", "", false);
    }

    return "";
}

/**
 * Returns the current user's id or -1
 */
function get_user_id()
{
    if (is_logged_in()) {
        return se($_SESSION["user"], "id", -1, false);
    }

    return -1;
}

/**
 * Adds a manga to a user's favorites
 */
function add_favorite($user_id, $manga_id)
{
    $db = getDB();

    $query = "
    INSERT INTO `IT202-S26-UserFavorites`
    (user_id, manga_id)
    VALUES (:uid, :mid)
    ";

    try {

        $stmt = $db->prepare($query);

        $stmt->execute([
            ":uid" => $user_id,
            ":mid" => $manga_id
        ]);

        flash("Added to favorites", "success");

    } catch (PDOException $e) {

        error_log("Error adding favorite: " . var_export($e, true));

        // Duplicate protection
        if ($e->errorInfo[1] == 1062) {

            flash("Already in favorites", "warning");

        } else {

            flash("Error adding favorite", "danger");
        }
    }
}

/**
 * Removes a manga from a user's favorites
 */
function remove_favorite($user_id, $manga_id)
{
    $db = getDB();

    $query = "
    DELETE FROM `IT202-S26-UserFavorites`
    WHERE user_id = :uid
    AND manga_id = :mid
    ";

    try {

        $stmt = $db->prepare($query);

        $stmt->execute([
            ":uid" => $user_id,
            ":mid" => $manga_id
        ]);

        flash("Removed from favorites", "success");

    } catch (PDOException $e) {

        error_log("Error removing favorite: " . var_export($e, true));

        flash("Error removing favorite", "danger");
    }
}

/**
 * Gets all favorites for a user
 */
function get_user_favorites($user_id)
{
    $db = getDB();

    $query = "
    SELECT m.*
    FROM `IT202-S26-UserFavorites` uf
    JOIN `IT202-S26-Manga` m
        ON uf.manga_id = m.id
    WHERE uf.user_id = :uid
    AND uf.is_active = 1
    ORDER BY uf.created DESC
    ";

    try {

        $stmt = $db->prepare($query);

        $stmt->execute([
            ":uid" => $user_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {

        error_log("Error fetching favorites: " . var_export($e, true));

        flash($e->getMessage(), "danger");
    }

    return [];
}
?>