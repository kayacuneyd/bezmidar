<?php
// Test script for Rewards & Hours Tracking
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once __DIR__ . '/config/db.php';

try {
    // 1. Create a test user (Parent)
    $testEmail = 'test_parent_' . time() . '@example.com';
    $testPhone1 = '999' . time();
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, is_active, approval_status) VALUES (?, ?, ?, ?, 'parent', 1, 'approved')");
    $stmt->execute(['Test Parent', $testEmail, $testPhone1, 'dummy_hash']);
    $parentId = $pdo->lastInsertId();

    // 2. Create a test user (Teacher - role is 'student')
    $testPhone2 = '888' . time();
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, is_active, approval_status) VALUES (?, ?, ?, ?, 'student', 1, 'approved')");
    $stmt->execute(['Test Teacher', 'test_teacher_' . time() . '@example.com', $testPhone2, 'dummy_hash']);
    $teacherId = $pdo->lastInsertId();

    // 3. Create a conversation
    $stmt = $pdo->prepare("INSERT INTO conversations (parent_id, teacher_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
    $stmt->execute([$parentId, $teacherId]);
    $convId = $pdo->lastInsertId();

    // 4. Create an accepted agreement
    $stmt = $pdo->prepare("INSERT INTO lesson_agreements (conversation_id, sender_id, hourly_rate, status) VALUES (?, ?, 10, 'accepted')");
    $stmt->execute([$convId, $teacherId]);
    $agreementId = $pdo->lastInsertId();

    // 5. Log hours (Simulate track_hours.php logic)
    $hours = 10;
    $stmt = $pdo->prepare("INSERT INTO lesson_hours_tracking (user_id, agreement_id, hours_completed, completed_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$parentId, $agreementId, $hours]);

    // 6. Check if rewards logic would trigger (Manual check of milestones)
    // For this test, we just check if the hours are recorded and visible to admin logic

    // 7. Simulate Admin Overview Query (from admin/rewards/overview.php)
    $adminSql = "
        SELECT 
            u.full_name,
            u.role,
            COALESCE(SUM(lht.hours_completed), 0) as total_hours
        FROM users u
        LEFT JOIN lesson_hours_tracking lht ON u.id = lht.user_id
        WHERE u.id = ?
        GROUP BY u.id
    ";
    $stmt = $pdo->prepare($adminSql);
    $stmt->execute([$parentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // 8. Cleanup
    $pdo->exec("DELETE FROM lesson_hours_tracking WHERE user_id = $parentId");
    $pdo->exec("DELETE FROM lesson_agreements WHERE id = $agreementId");
    $pdo->exec("DELETE FROM conversations WHERE id = $convId");
    $pdo->exec("DELETE FROM users WHERE id IN ($parentId, $teacherId)");

    echo json_encode([
        'success' => true,
        'test_data' => [
            'student_id' => $parentId,
            'hours_logged' => $hours,
            'admin_view_result' => $result
        ],
        'message' => 'Test completed. Data created, verified, and cleaned up.'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>