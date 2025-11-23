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

if (!$postId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'postId zorunludur'
    ]);
    exit;
}

try {
    ensureBlogTables($pdo);
    $stmt = $pdo->prepare("
        UPDATE blog_posts
        SET likes = COALESCE(likes, 0) + 1
        WHERE id = ?
    ");
    $stmt->execute([$postId]);

    echo json_encode([
        'success' => true,
        'message' => 'Beğenildi'
    ]);
} catch (Throwable $e) {
    error_log('Blog like error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Beğeni kaydedilemedi'
    ]);
}
