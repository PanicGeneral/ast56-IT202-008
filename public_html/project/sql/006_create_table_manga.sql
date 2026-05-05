CREATE TABLE `IT202-S26-Manga` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `api_id` varchar(50) UNIQUE,
  `title` varchar(255) NOT NULL,
  `sub_title` varchar(255),
  `status` varchar(50),
  `type` varchar(50),
  `summary` text,
  `thumb` varchar(500),
  `genres` text,
  `nsfw` tinyint(1) DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_api` tinyint(1) DEFAULT '1'
);