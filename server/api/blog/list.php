<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // En basit haliyle tüm yazıları tarihe göre yeni → eski sıralayalım
    $stmt = $pdo->prepare("
        SELECT
            id,
            slug,
            title,
            excerpt,
            author,
            image,
            likes,
            DATE(created_at) AS date
        FROM blog_posts
        ORDER BY created_at DESC
        LIMIT 100
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Frontend mockData.posts şekline uyacak şekilde alanları dönüştürelim
    $posts = array_map(static function (array $row): array {
        return [
            'id'      => (int) $row['id'],
            'title'   => $row['title'],
            'slug'    => $row['slug'],
            'excerpt' => $row['excerpt'],
            'author'  => $row['author'],
            'date'    => $row['date'],
            'image'   => $row['image'],
            'likes'   => isset($row['likes']) ? (int) $row['likes'] : 0,
        ];
    }, $rows);

    echo json_encode([
        'success' => true,
        'data'    => $posts
    ]);
} catch (Throwable $e) {
    error_log('Blog list error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Blog yazıları getirilemedi'
    ]);
}
