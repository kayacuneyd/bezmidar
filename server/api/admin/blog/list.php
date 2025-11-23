<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

$admin = requireAuth(['admin']);

try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            slug,
            title,
            excerpt,
            content,
            author,
            image,
            likes,
            is_published,
            created_at,
            updated_at
        FROM blog_posts
        ORDER BY created_at DESC
        LIMIT 200
    ");
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $posts]);
} catch (Throwable $e) {
    error_log('Admin blog list error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Blog yazıları getirilemedi']);
}
