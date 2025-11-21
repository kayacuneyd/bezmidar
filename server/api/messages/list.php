<?php
require_once '../config/cors.php';
require_once '../config/db.php';
require_once '../config/auth.php';

// Require authentication (both teachers and parents can access)
$user = requireAuth(['student', 'parent']);

try {
    $userId = $user['id'];
    $userRole = $user['role'];

    // Determine which field to use based on user role
    // Note: 'student' role is actually teachers in this system
    $userField = ($userRole === 'student') ? 'teacher_id' : 'parent_id';
    $otherField = ($userRole === 'student') ? 'parent_id' : 'teacher_id';
    $unreadField = ($userRole === 'student') ? 'teacher_unread_count' : 'parent_unread_count';

    // Get all conversations for this user
    $stmt = $pdo->prepare("
        SELECT
            c.id,
            c.teacher_id,
            c.parent_id,
            c.last_message_text,
            c.last_message_at,
            c.$unreadField as unread_count,
            c.created_at,
            c.updated_at,
            u.id as other_user_id,
            u.full_name as other_user_name,
            u.role as other_user_role,
            tp.hourly_rate,
            tp.rating_avg
        FROM conversations c
        INNER JOIN users u ON u.id = c.$otherField
        LEFT JOIN teacher_profiles tp ON tp.user_id = c.$otherField AND u.role = 'student'
        WHERE c.$userField = ?
        ORDER BY c.updated_at DESC
    ");

    $stmt->execute([$userId]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the response
    $formattedConversations = array_map(function($conv) {
        return [
            'id' => (int)$conv['id'],
            'teacher_id' => (int)$conv['teacher_id'],
            'parent_id' => (int)$conv['parent_id'],
            'other_user' => [
                'id' => (int)$conv['other_user_id'],
                'name' => $conv['other_user_name'],
                'role' => $conv['other_user_role'],
                'hourly_rate' => $conv['hourly_rate'] ? (float)$conv['hourly_rate'] : null,
                'rating_avg' => $conv['rating_avg'] ? (float)$conv['rating_avg'] : null
            ],
            'last_message' => $conv['last_message_text'],
            'last_message_at' => $conv['last_message_at'],
            'unread_count' => (int)$conv['unread_count'],
            'created_at' => $conv['created_at'],
            'updated_at' => $conv['updated_at']
        ];
    }, $conversations);

    echo json_encode([
        'success' => true,
        'data' => $formattedConversations
    ]);

} catch (PDOException $e) {
    error_log("Database error in messages/list.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
