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
$status = trim($input['approval_status'] ?? '');
$allowedStatuses = ['pending', 'approved', 'rejected'];

if (!$userId || !in_array($status, $allowedStatuses, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Geçersiz kullanıcı veya durum']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE users
        SET approval_status = ?, updated_at = NOW()
        WHERE id = ? AND role = 'student'
    ");
    $stmt->execute([$status, $userId]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    error_log('Admin teacher update error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Durum güncellenemedi']);
}
