<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/blog_tables.php';

header('Content-Type: application/json; charset=utf-8');

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if ($slug === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'slug parametresi gereklidir'
    ]);
    exit;
}

try {
    ensureBlogTables($pdo);
    // Önce yazıyı bul
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
            DATE(created_at) AS date
        FROM blog_posts
        WHERE slug = ? AND is_published = 1
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Yazı bulunamadı'
        ]);
        exit;
    }

    $postId = (int) $row['id'];

    // Yorumları çek
    $commentStmt = $pdo->prepare("
        SELECT
            id,
            user_name,
            comment_text,
            DATE(created_at) AS date
        FROM blog_comments
        WHERE post_id = ?
        ORDER BY created_at ASC
    ");
    $commentStmt->execute([$postId]);
    $commentsRows = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

    $comments = array_map(static function (array $c): array {
        return [
            'id'   => (int) $c['id'],
            'user' => $c['user_name'],
            'text' => $c['comment_text'],
            'date' => $c['date'],
        ];
    }, $commentsRows);

    $post = [
        'id'       => $postId,
        'title'    => $row['title'],
        'slug'     => $row['slug'],
        'excerpt'  => $row['excerpt'],
        'content'  => $row['content'],
        'author'   => $row['author'],
        'image'    => $row['image'],
        'likes'    => isset($row['likes']) ? (int) $row['likes'] : 0,
        'date'     => $row['date'],
        'comments' => $comments
    ];

    echo json_encode([
        'success' => true,
        'data'    => $post
    ]);
} catch (Throwable $e) {
    error_log('Blog detail error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Blog yazısı getirilemedi'
    ]);
}
