
CREATE DATABASE IF NOT EXISTS `news` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `article_ratings` (
    `id` int(11) NOT NULL,
    `saved_article_id` int(11) DEFAULT NULL,
    `rating` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `saved_article_id` (`saved_article_id`),
    CONSTRAINT `article_ratings_ibfk_1` FOREIGN KEY (`saved_article_id`) REFERENCES `saved_articles` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `comments` (
    `id` int(11) NOT NULL,
    `saved_article_id` int(11) DEFAULT NULL,
    `commenter_name` varchar(50) DEFAULT NULL,
    `comment_text` text DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `saved_article_id` (`saved_article_id`),
    CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`saved_article_id`) REFERENCES `saved_articles` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `saved_articles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `source_name` varchar(255) DEFAULT NULL,
    `title` varchar(255) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `url` varchar(255) DEFAULT NULL,
    `urlToImage` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
