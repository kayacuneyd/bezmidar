<?php
require_once __DIR__ . '/../config/cors.php';

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/data.php';

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
    $post = null;
    foreach ($BLOG_POSTS as $item) {
        if ($item['slug'] === $slug) {
            $post = $item;
            break;
        }
    }

    if (!$post) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Yazı bulunamadı'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => $post
    ]);
} catch (Throwable $e) {
    error_log('Blog detail error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Blog yazısı getirilemedi'
    ]);
}

