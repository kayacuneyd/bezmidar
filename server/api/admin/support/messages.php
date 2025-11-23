<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

$admin = requireAuth(['admin']);

$status = isset($_GET['status']) ? trim($_GET['status']) : null;
$allowedStatuses = ['new', 'in_progress', 'resolved'];
$params = [];

$sql = "
    SELECT 
        cm.*,
        handler.full_name AS handler_name
    FROM contact_messages cm
    LEFT JOIN users handler ON handler.id = cm.handled_by
";

if ($status && in_array($status, $allowedStatuses, true)) {
    $sql .= " WHERE cm.status = ? ";
    $params[] = $status;
}

$sql .= " ORDER BY cm.created_at DESC LIMIT 200";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $messages
    ]);
} catch (Throwable $e) {
    error_log('Admin support messages error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Destek mesajları getirilemedi']);
}
