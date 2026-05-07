<?php

/**
 * Fetch ONE manga (0–1 result)
 */
function fetch_manga($text, $nsfw = false, $type = "All")
{
    $data = ["text" => $text, "nsfw" => $nsfw, "type" => $type];
    $endpoint = "https://mangaverse-api.p.rapidapi.com/manga/search";
    $isRapidAPI = true;
    $rapidAPIHost = "mangaverse-api.p.rapidapi.com";

    $result = get($endpoint, "MANGA_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

    error_log("API Response: " . var_export($result, true));

    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }

    $transformedResult = [];

    if (isset($result["data"]) && count($result["data"]) > 0) {

        $manga = $result["data"][0];

        $transformedResult = [
            "id" => $manga["id"] ?? "",
            "title" => $manga["title"] ?? "",
            "sub_title" => $manga["sub_title"] ?? "",
            "status" => $manga["status"] ?? "Unknown",
            "thumb" => $manga["thumb"] ?? "",
            "summary" => $manga["summary"] ?? "",
            "genres" => isset($manga["genres"]) ? implode(", ", $manga["genres"]) : "N/A",

            "nsfw" => $manga["nsfw"] ?? false,
            "type" => $manga["type"] ?? ""
        ];
    }

    return $transformedResult;
}


/**
 * Fetch MULTIPLE manga (search results)
 */
function search_series($search, $nsfw = false, $type = "All")
{
    $data = ["text" => $search, "nsfw" => $nsfw, "type" => $type];
    $endpoint = "https://mangaverse-api.p.rapidapi.com/manga/search";
    $isRapidAPI = true;
    $rapidAPIHost = "mangaverse-api.p.rapidapi.com";

    $result = get($endpoint, "MANGA_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

    error_log("API Response: " . var_export($result, true));

    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }

    $transformedResult = [];

    if (isset($result["data"]) && is_array($result["data"])) {

        foreach ($result["data"] as $manga) {

            $transformedResult[] = [
                "id" => $manga["id"] ?? "",
                "title" => $manga["title"] ?? "",
                "sub_title" => $manga["sub_title"] ?? "",
                "status" => $manga["status"] ?? "Unknown",
                "thumb" => $manga["thumb"] ?? "",
                "summary" => $manga["summary"] ?? "",
                "genres" => isset($manga["genres"]) ? implode(", ", $manga["genres"]) : "N/A",

                "nsfw" => $manga["nsfw"] ?? 0,
                "type" => $manga["type"] ?? "",
                
            ];
        }
    }

    return $transformedResult;
}
