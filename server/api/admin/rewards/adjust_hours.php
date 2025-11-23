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
$hours = isset($input['hours']) ? (float) $input['hours'] : 0;
$notes = trim($input['notes'] ?? '');

if (!$userId || $hours === 0.0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Kullanıcı ve saat değeri zorunlu']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO lesson_hours_tracking (user_id, agreement_id, hours_completed, notes, completed_at)
        VALUES (?, NULL, ?, ?, NOW())
    ");
    $stmt->execute([$userId, $hours, $notes === '' ? null : $notes]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    error_log('Admin adjust hours error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Saat kaydedilemedi']);
}
