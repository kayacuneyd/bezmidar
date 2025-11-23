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
$id = isset($input['id']) ? (int) $input['id'] : 0;
$status = isset($input['status']) ? trim($input['status']) : null;
$notes = trim($input['admin_notes'] ?? '');

$allowedStatuses = ['new', 'in_progress', 'resolved'];

if (!$id || ($status && !in_array($status, $allowedStatuses, true))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Geçersiz veri']);
    exit;
}

try {
    $fields = [];
    $params = [];

    if ($status) {
        $fields[] = 'status = ?';
        $params[] = $status;

        if ($status === 'resolved') {
            $fields[] = 'handled_by = ?';
            $fields[] = 'handled_at = NOW()';
            $params[] = $admin['id'];
        }
    }

    if ($notes !== '') {
        $fields[] = 'admin_notes = ?';
        $params[] = $notes;
    }

    if (empty($fields)) {
        echo json_encode(['success' => true]);
        exit;
    }

    $params[] = $id;
    $sql = "UPDATE contact_messages SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    error_log('Admin support update error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Mesaj güncellenemedi']);
}
