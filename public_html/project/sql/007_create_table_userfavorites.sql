CREATE TABLE `IT202-S26-UserFavorites` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `manga_id` int NOT NULL,
    `user_id` int NOT NULL,
    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    `is_active` tinyint(1) DEFAULT 1,

    FOREIGN KEY (`manga_id`)
        REFERENCES `IT202-S26-Manga` (`id`)
        ON DELETE CASCADE,

    FOREIGN KEY (`user_id`)
        REFERENCES `Users` (`id`)
        ON DELETE CASCADE,

    UNIQUE KEY (`manga_id`, `user_id`)
);