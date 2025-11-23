<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/blog_tables.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error'   => 'Method not allowed'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$postId = isset($input['postId']) ? (int) $input['postId'] : 0;
$text   = isset($input['text']) ? trim($input['text']) : '';

if (!$postId || $text === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'postId ve text zorunludur'
    ]);
    exit;
}

// Kullanıcı adı – şimdilik auth zorunlu değil, ileride requireAuth eklenebilir
$userName = isset($input['user']) && trim($input['user']) !== ''
    ? trim($input['user'])
    : 'Anonim';

try {
    ensureBlogTables($pdo);
    $stmt = $pdo->prepare("
        INSERT INTO blog_comments (post_id, user_name, comment_text, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$postId, $userName, $text]);

    echo json_encode([
        'success' => true,
        'message' => 'Yorum eklendi'
    ]);
} catch (Throwable $e) {
    error_log('Blog comment error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Yorum eklenemedi'
    ]);
}
