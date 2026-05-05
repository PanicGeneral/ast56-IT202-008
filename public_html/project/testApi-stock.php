<?php
require(__DIR__ . "/../../partials/nav.php");
//AST56
//This is my edited version for my mnaga search api
$result = [];
if (isset($_GET["text"])) {
 
    $data = ["text" => $_GET["text"], "nsfw" => $_GET["nsfw"], "type" => $_GET["type"]];
    $endpoint = "https://mangaverse-api.p.rapidapi.com/manga/search";
    $isRapidAPI = true;
    $rapidAPIHost = "mangaverse-api.p.rapidapi.com";
    $result = get($endpoint, "MANGA_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
}
?>
//edited page layout
<div class="container-fluid">
    <h1>Manga Search</h1>
    <p>Search For Your Favorite Mangas or Something New!</p>
    <form>
        <div>
            <label>KeyWord</label>
            <input name="text" />

            <label>Type</label>
            <input name="type" />

            <label>NSFW</label>
             <select name="nsfw">
                <option value="false">False</option>
                <option value="true">True</option>
            </select>

            <br>
            <input type="submit" value="Search" />

        </div>
    </form>
    <div class="row ">
        <?php if (isset($result["data"])) : ?>
            <?php foreach ($result["data"] as $manga) : ?>
                <pre>
            <?php var_export($manga); ?>
        </pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");
