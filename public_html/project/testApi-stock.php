<?php
require(__DIR__ . "/../../partials/nav.php");
//AST56
//This is my edited version for my mnaga search api
$result = [];
if (isset($_GET["text"])) {
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    $data = ["text" => $_GET["text"], "nsfw" => $_GET["nsfw"], "type" => $_GET["type"]];
    $endpoint = "https://mangaverse-api.p.rapidapi.com/manga/search";
    $isRapidAPI = true;
    $rapidAPIHost = "mangaverse-api.p.rapidapi.com";
    $result = get($endpoint, "MANGA_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    //example of cached data to save the quotas, don't forget to comment out the get() if using the cached data for testing
    /* $result = ["status" => 200, "response" => '{
    "Global Quote": {
        "01. symbol": "MSFT",
        "02. open": "420.1100",
        "03. high": "422.3800",
        "04. low": "417.8400",
        "05. price": "421.4400",
        "06. volume": "17861855",
        "07. latest trading day": "2024-04-02",
        "08. previous close": "424.5700",
        "09. change": "-3.1300",
        "10. change percent": "-0.7372%"
    }
}'];*/
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
}
?>
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
