<?php
require_once '../config/cors.php';
require_once '../config/db.php';
require_once '../config/auth.php';

// Require authentication
$user = requireAuth(['student', 'parent']);

try {
    $userId = isset($user['user_id']) ? (int) $user['user_id'] : (int) ($user['id'] ?? 0);
    $userRole = $user['role'];

    // Debug logging
    error_log("=== Conversation Start Request ===");
    error_log("User ID: $userId");
    error_log("User Role: $userRole");
    error_log("User Data: " . json_encode($user));

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    error_log("Input Data: " . json_encode($input));

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

        // Specific error messages
        if ($userRole === 'student' && $otherUser['role'] === 'student') {
            $errorMsg = 'Öğretmenler birbirleriyle mesajlaşamaz';
        } elseif ($userRole === 'parent' && $otherUser['role'] === 'parent') {
            $errorMsg = 'Veliler birbirleriyle mesajlaşamaz';
        } else {
            $errorMsg = 'Mesajlaşma sadece öğretmen ve veli arasında başlatılabilir';
        }

        echo json_encode([
            'success' => false,
            'error' => $errorMsg
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

    error_log("Creating conversation: teacher_id=$teacherId, parent_id=$parentId");

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
    // Enhanced error logging
    error_log("=== Database Error in messages/start.php ===");
    error_log("Error Message: " . $e->getMessage());
    error_log("Error Code: " . $e->getCode());
    error_log("SQL State: " . ($e->errorInfo[0] ?? 'N/A'));
    error_log("Stack Trace: " . $e->getTraceAsString());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred',
        'debug' => [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'sql_state' => $e->errorInfo[0] ?? null
        ]
    ]);
} catch (Exception $e) {
    error_log("=== General Error in messages/start.php ===");
    error_log("Error: " . $e->getMessage());
    error_log("Stack Trace: " . $e->getTraceAsString());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred',
        'debug' => [
            'message' => $e->getMessage()
        ]
    ]);
}
