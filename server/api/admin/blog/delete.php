<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/blog_tables.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$admin = requireAuth(['admin']);
ensureBlogTables($pdo);

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$id = isset($input['id']) ? (int) $input['id'] : 0;

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Geçersiz yazı']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    error_log('Admin blog delete error: ' . $e->getMessage());
    http_response_code(500);
    $message = 'Blog yazısı silinemedi';

    if ($e instanceof PDOException) {
        $driverCode = $e->errorInfo[1] ?? null;
        if ($driverCode === 1146) {
            $message = 'Blog tabloları bulunamadı (migrasyon gerekli)';
        } else {
            $message .= ' (' . $e->getMessage() . ')';
        }
    } else {
        $message .= ' (' . $e->getMessage() . ')';
    }

    echo json_encode(['success' => false, 'error' => $message]);
}
