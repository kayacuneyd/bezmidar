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

    if (!isset($input['other_user_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'other_user_id is required'
        ]);
        exit();
    }

    $otherUserId = (int)$input['other_user_id'];

    // Prevent messaging yourself
    if ($otherUserId === $userId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Cannot start conversation with yourself'
        ]);
        exit();
    }

    // Get other user info and validate they exist
    $stmt = $pdo->prepare("
        SELECT id, full_name, role, is_active, approval_status
        FROM users
        WHERE id = ?
    ");
    $stmt->execute([$otherUserId]);
    $otherUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$otherUser) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'User not found'
        ]);
        exit();
    }

    // Validate user roles (one must be teacher, other must be parent)
    $isValidPair = (
        ($userRole === 'student' && $otherUser['role'] === 'parent') ||
        ($userRole === 'parent' && $otherUser['role'] === 'student')
    );

    if (!$isValidPair) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Conversations can only be started between teachers and parents'
        ]);
        exit();
    }

    // Check if other user is active and approved
    if (!$otherUser['is_active'] || $otherUser['approval_status'] !== 'approved') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Cannot start conversation with this user'
        ]);
        exit();
    }

    // Determine teacher_id and parent_id
    $teacherId = ($userRole === 'student') ? $userId : $otherUserId;
    $parentId = ($userRole === 'parent') ? $userId : $otherUserId;

    // Check if conversation already exists
    $stmt = $pdo->prepare("
        SELECT id FROM conversations
        WHERE teacher_id = ? AND parent_id = ?
    ");
    $stmt->execute([$teacherId, $parentId]);
    $existingConversation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingConversation) {
        // Return existing conversation
        echo json_encode([
            'success' => true,
            'data' => [
                'conversation_id' => (int)$existingConversation['id'],
                'is_new' => false
            ],
            'message' => 'Conversation already exists'
        ]);
        exit();
    }

    // Create new conversation
    $stmt = $pdo->prepare("
        INSERT INTO conversations (teacher_id, parent_id, created_at, updated_at)
        VALUES (?, ?, NOW(), NOW())
    ");
    $stmt->execute([$teacherId, $parentId]);
    $conversationId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'data' => [
            'conversation_id' => (int)$conversationId,
            'is_new' => true,
            'other_user' => [
                'id' => (int)$otherUser['id'],
                'name' => $otherUser['full_name'],
                'role' => $otherUser['role']
            ]
        ],
        'message' => 'Conversation created successfully'
    ]);

} catch (PDOException $e) {
    error_log("Database error in messages/start.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
