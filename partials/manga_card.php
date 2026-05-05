<?php
if (!isset($data) || !is_array($data)) {
    return;
}
?>

<div class="card mx-auto my-3" style="width: 20rem;">

    <div class="ratio ratio-1x1 d-flex justify-content-center" style="height:150px">
        <img 
            src="<?php echo !empty($data["thumb"]) ? $data["thumb"] : 'https://via.placeholder.com/150'; ?>" 
            class="img-fluid object-fit-contain" 
            alt="Manga Image">
    </div>

    <div class="card-body">
        <h5 class="card-title">
            <?php echo htmlspecialchars($data["title"] ?? "Unknown Title"); ?>
        </h5>

        <ul class="list-group list-group-flush">

            <li class="list-group-item">
                Type: <?php echo htmlspecialchars($data["type"] ?? "N/A"); ?>
            </li>

            <li class="list-group-item">
                Status: <?php echo htmlspecialchars($data["status"] ?? "N/A"); ?>
            </li>

            <li class="list-group-item">
                Genres: <?php echo htmlspecialchars($data["genres"] ?? "N/A"); ?>
            </li>

            <li class="list-group-item">
                NSFW: <?php echo !empty($data["nsfw"]) ? "Yes" : "No"; ?>
            </li>

            <li class="list-group-item">
                Source: <?php echo !empty($data["is_api"]) ? "API" : "Manual"; ?>
            </li>

        </ul>

    </div>
</div>