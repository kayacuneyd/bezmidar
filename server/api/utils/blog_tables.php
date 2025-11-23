<?php

if (!function_exists('dm_addColumnIfMissing')) {
    function dm_addColumnIfMissing(PDO $pdo, $table, $column, $definition)
    {
        try {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
            $stmt->execute([$column]);
            if ($stmt->rowCount() === 0) {
                $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN {$definition}");
            }
        } catch (PDOException $e) {
            error_log("dm_addColumnIfMissing ({$table}.{$column}) failed: " . $e->getMessage());
            throw $e;
        }
    }
}

/**
 * Ensure blog-related tables exist. This allows admin/blog endpoints
 * to work even if the migration wasn't run manually beforehand.
 */
function ensureBlogTables(PDO $pdo)
{
    static $ensured = false;

    if ($ensured) {
        return;
    }

    $queries = [
        <<<SQL
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(191) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `author` varchar(191) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_blog_posts_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
        <<<SQL
CREATE TABLE IF NOT EXISTS `blog_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_name` varchar(191) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_blog_comments_post_id` (`post_id`),
  CONSTRAINT `fk_blog_comments_post` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL,
    ];

    foreach ($queries as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            error_log('ensureBlogTables failed: ' . $e->getMessage());
            throw $e;
        }
    }

    // Legacy deployments might miss columns. Patch them idempotently.
    dm_addColumnIfMissing($pdo, 'blog_posts', 'content_markdown', "`content_markdown` longtext NULL AFTER `content`");
    dm_addColumnIfMissing($pdo, 'blog_posts', 'is_published', "`is_published` tinyint(1) NOT NULL DEFAULT 1 AFTER `likes`");
    dm_addColumnIfMissing($pdo, 'blog_posts', 'created_at', "`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP AFTER `is_published`");
    dm_addColumnIfMissing($pdo, 'blog_posts', 'updated_at', "`updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");

    $ensured = true;
}
