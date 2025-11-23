<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$admin = requireAuth(['admin']);

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$userId = isset($input['user_id']) ? (int) $input['user_id'] : 0;
$isActive = isset($input['is_active']) ? (int) !!$input['is_active'] : null;

if (!$userId || $isActive === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Geçersiz kullanıcı veya durum']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
    $stmt->execute([$isActive, $userId]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    error_log('Admin toggle active error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Kullanıcı güncellenemedi']);
}
