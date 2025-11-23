<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

$admin = requireAuth(['admin']);

$limit = isset($_GET['limit']) ? max(10, min((int) $_GET['limit'], 200)) : 100;

try {
    $stmt = $pdo->prepare("
        SELECT
            m.id,
            m.conversation_id,
            m.sender_id,
            m.message_text,
            m.created_at,
            u.full_name AS sender_name,
            u.role AS sender_role,
            c.teacher_id,
            c.parent_id
        FROM messages m
        JOIN users u ON u.id = m.sender_id
        JOIN conversations c ON c.id = m.conversation_id
        ORDER BY m.created_at DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $messages
    ]);
} catch (Throwable $e) {
    error_log('Admin recent messages error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Mesajlar getirilemedi']);
}
