<?php
require_once '../config/cors.php';
require_once '../config/db.php';
require_once '../config/auth.php';

// Require authentication
$user = requireAuth(['student', 'parent']);

try {
    $userId = $user['id'];
    $userRole = $user['role'];

    // Get conversation_id from query parameter
    if (!isset($_GET['conversation_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'conversation_id parameter is required'
        ]);
        exit();
    }

    $conversationId = (int)$_GET['conversation_id'];

    // Verify user is a participant in this conversation
    $userField = ($userRole === 'student') ? 'teacher_id' : 'parent_id';

    $stmt = $pdo->prepare("
        SELECT id, teacher_id, parent_id
        FROM conversations
        WHERE id = ? AND $userField = ?
    ");
    $stmt->execute([$conversationId, $userId]);
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$conversation) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Conversation not found or access denied'
        ]);
        exit();
    }

    // Get pagination parameters
    $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 100) : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    // Get messages for this conversation
    $stmt = $pdo->prepare("
        SELECT
            m.id,
            m.conversation_id,
            m.sender_id,
            m.message_text,
            m.is_read,
            m.created_at,
            u.full_name as sender_name,
            u.role as sender_role
        FROM messages m
        INNER JOIN users u ON u.id = m.sender_id
        WHERE m.conversation_id = ?
        ORDER BY m.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$conversationId, $limit, $offset]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format messages
    $formattedMessages = array_map(function($msg) use ($userId) {
        return [
            'id' => (int)$msg['id'],
            'conversation_id' => (int)$msg['conversation_id'],
            'sender_id' => (int)$msg['sender_id'],
            'message_text' => $msg['message_text'],
            'is_read' => (bool)$msg['is_read'],
            'is_mine' => (int)$msg['sender_id'] === $userId,
            'sender_name' => $msg['sender_name'],
            'sender_role' => $msg['sender_role'],
            'created_at' => $msg['created_at']
        ];
    }, $messages);

    // Mark messages as read (messages sent by the other user)
    $unreadField = ($userRole === 'student') ? 'teacher_unread_count' : 'parent_unread_count';

    $stmt = $pdo->prepare("
        UPDATE messages
        SET is_read = 1
        WHERE conversation_id = ? AND sender_id != ? AND is_read = 0
    ");
    $stmt->execute([$conversationId, $userId]);

    // Reset unread count for this user
    $stmt = $pdo->prepare("
        UPDATE conversations
        SET $unreadField = 0
        WHERE id = ?
    ");
    $stmt->execute([$conversationId]);

    // Get other user info
    $otherUserId = ($userRole === 'student') ? $conversation['parent_id'] : $conversation['teacher_id'];

    $stmt = $pdo->prepare("
        SELECT
            u.id,
            u.full_name,
            u.role,
            tp.hourly_rate,
            tp.rating_avg
        FROM users u
        LEFT JOIN teacher_profiles tp ON tp.user_id = u.id AND u.role = 'student'
        WHERE u.id = ?
    ");
    $stmt->execute([$otherUserId]);
    $otherUser = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'conversation_id' => $conversationId,
            'other_user' => [
                'id' => (int)$otherUser['id'],
                'name' => $otherUser['full_name'],
                'role' => $otherUser['role'],
                'hourly_rate' => $otherUser['hourly_rate'] ? (float)$otherUser['hourly_rate'] : null,
                'rating_avg' => $otherUser['rating_avg'] ? (float)$otherUser['rating_avg'] : null
            ],
            'messages' => $formattedMessages
        ]
    ]);

} catch (PDOException $e) {
    error_log("Database error in messages/detail.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
