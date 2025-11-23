<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

$admin = requireAuth(['admin']);

$conversationId = isset($_GET['conversation_id']) ? (int) $_GET['conversation_id'] : 0;

if (!$conversationId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'conversation_id gereklidir']);
    exit;
}

try {
    $conversationStmt = $pdo->prepare("
        SELECT 
            c.id,
            c.teacher_id,
            c.parent_id,
            c.created_at,
            t.full_name AS teacher_name,
            p.full_name AS parent_name
        FROM conversations c
        LEFT JOIN users t ON t.id = c.teacher_id
        LEFT JOIN users p ON p.id = c.parent_id
        WHERE c.id = ?
        LIMIT 1
    ");
    $conversationStmt->execute([$conversationId]);
    $conversation = $conversationStmt->fetch(PDO::FETCH_ASSOC);

    if (!$conversation) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Konuşma bulunamadı']);
        exit;
    }

    $messageStmt = $pdo->prepare("
        SELECT 
            m.id,
            m.sender_id,
            u.full_name AS sender_name,
            u.role AS sender_role,
            m.message_text,
            m.created_at
        FROM messages m
        JOIN users u ON u.id = m.sender_id
        WHERE m.conversation_id = ?
        ORDER BY m.created_at ASC
    ");
    $messageStmt->execute([$conversationId]);
    $messages = $messageStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'conversation' => $conversation,
            'messages' => $messages
        ]
    ]);
} catch (Throwable $e) {
    error_log('Admin conversation fetch error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Konuşma getirilemedi']);
}
