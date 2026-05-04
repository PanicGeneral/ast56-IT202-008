<?php

/**
 * This file is a wrapper for our API calls.
 * Here, each endpoint needed will be exposes as a function.
 * The function will take the parameters needed for the API call and return the result.
 * The function will also handle the API key and endpoint.
 * Requires the api_helper.php file and load_api_keys.php file.
 */

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

        // map to clean structure
        $transformedResult = [
            "id" => $manga["id"] ?? "",
            "title" => $manga["title"] ?? "",
            "sub_title" => $manga["sub_title"] ?? "",
            "status" => $manga["status"] ?? "",
            "thumb" => $manga["thumb"] ?? "",
            "summary" => $manga["summary"] ?? "",
            "genres" => isset($manga["genres"]) ? implode(",", $manga["genres"]) : "",
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

    if (isset($result["data"])) {

        foreach ($result["data"] as $manga) {

            $transformedResult[] = [
                "id" => $manga["id"] ?? "",
                "title" => $manga["title"] ?? "",
                "thumb" => $manga["thumb"] ?? "",
                "type" => $manga["type"] ?? "",
                "nsfw" => $manga["nsfw"] ?? 0,
                "is_api" => 1
            ];
        }
    }

    return $transformedResult;
}
