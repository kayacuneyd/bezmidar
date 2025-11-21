<?php
require_once '../config/cors.php';
require_once '../config/db.php';
require_once '../config/auth.php';

// Require authentication
$user = requireAuth(['student', 'parent']);

try {
    $userId = $user['id'];
    $userRole = $user['role'];

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['conversation_id']) || !isset($input['message_text'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'conversation_id and message_text are required'
        ]);
        exit();
    }

    $conversationId = (int)$input['conversation_id'];
    $messageText = trim($input['message_text']);

    // Validate message is not empty
    if (empty($messageText)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Message cannot be empty'
        ]);
        exit();
    }

    // Verify user is a participant in this conversation
    $userField = ($userRole === 'student') ? 'teacher_id' : 'parent_id';
    $otherField = ($userRole === 'student') ? 'parent_id' : 'teacher_id';

    $stmt = $pdo->prepare("
        SELECT id, $otherField as other_user_id
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

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Insert message
        $stmt = $pdo->prepare("
            INSERT INTO messages (conversation_id, sender_id, message_text, is_read, created_at)
            VALUES (?, ?, ?, 0, NOW())
        ");
        $stmt->execute([$conversationId, $userId, $messageText]);
        $messageId = $pdo->lastInsertId();

        // Update conversation metadata
        $otherUnreadField = ($userRole === 'student') ? 'parent_unread_count' : 'teacher_unread_count';

        $stmt = $pdo->prepare("
            UPDATE conversations
            SET
                last_message_text = ?,
                last_message_at = NOW(),
                $otherUnreadField = $otherUnreadField + 1,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$messageText, $conversationId]);

        // Commit transaction
        $pdo->commit();

        // Get the created message with sender info
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
            WHERE m.id = ?
        ");
        $stmt->execute([$messageId]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => [
                'id' => (int)$message['id'],
                'conversation_id' => (int)$message['conversation_id'],
                'sender_id' => (int)$message['sender_id'],
                'message_text' => $message['message_text'],
                'is_read' => (bool)$message['is_read'],
                'is_mine' => true,
                'sender_name' => $message['sender_name'],
                'sender_role' => $message['sender_role'],
                'created_at' => $message['created_at']
            ],
            'message' => 'Message sent successfully'
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    error_log("Database error in messages/send.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
