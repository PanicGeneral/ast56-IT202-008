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
    $data = ["text" => $text,"nsfw" => $nsfw,"type" => $type];
    $endpoint = "https://mangaverse-api.p.rapidapi.com/manga/search";
    $result = get($endpoint, "MANGA_API_KEY", $data, true, "mangaverse-api.p.rapidapi.com");

    error_log("API Response: " . var_export($result, true));

    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        return [];
    }

    if (!isset($result["data"]) || count($result["data"]) === 0) {
        return [];
    }

    $manga = $result["data"][0];

    return [
        "id" => se($manga, "id", ""),
        "title" => se($manga, "title", ""),
        "sub_title" => se($manga, "sub_title", ""),
        "status" => se($manga, "status", ""),
        "thumb" => se($manga, "thumb", ""),
        "summary" => se($manga, "summary", ""),
        "genres" => se($manga, "genres", []),
        "nsfw" => se($manga, "nsfw", false),
        "type" => se($manga, "type", "")
    ];
}

/**
 * Fetch MULTIPLE manga (search results)
 */
function search_series($text, $nsfw = false, $type = "All")
{
    $data = ["text" => $text,"nsfw" => $nsfw,"type" => $type];
    $endpoint = "https://mangaverse-api.p.rapidapi.com/manga/search";
    $result = get($endpoint, "MANGA_API_KEY", $data, true, "mangaverse-api.p.rapidapi.com");

    error_log("API Response: " . var_export($result, true));

    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        return [];
    }

    if (!isset($result["data"])) {
        return [];
    }

    $transformedResult = [];

    foreach ($result["data"] as $manga) {
        $transformedResult[] = [
            "id" => se($manga, "id", ""),
            "title" => se($manga, "title", ""),
            "thumb" => se($manga, "thumb", ""),
            "type" => se($manga, "type", ""),
            "nsfw" => se($manga, "nsfw", false)
        ];
    }

    return $transformedResult;
}