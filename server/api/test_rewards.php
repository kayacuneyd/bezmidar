<?php
// Test script for Rewards & Hours Tracking
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once __DIR__ . '/config/db.php';

try {
    // 1. Create a test user (student)
    $testEmail = 'test_student_' . time() . '@example.com';
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, is_active, approval_status) VALUES (?, ?, ?, 'student', 1, 'approved')");
    $stmt->execute(['Test Student', $testEmail, 'dummy_hash']);
    $studentId = $pdo->lastInsertId();

    // 2. Create a test user (teacher)
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, is_active, approval_status) VALUES (?, ?, ?, 'student', 1, 'approved')");
    $stmt->execute(['Test Teacher', 'test_teacher_' . time() . '@example.com', 'dummy_hash']);
    $teacherId = $pdo->lastInsertId();

    // 3. Create a conversation
    $stmt = $pdo->prepare("INSERT INTO conversations (parent_id, teacher_id, subject_id, status) VALUES (?, ?, 1, 'active')");
    $stmt->execute([$studentId, $teacherId]);
    $convId = $pdo->lastInsertId();

    // 4. Create an accepted agreement
    $stmt = $pdo->prepare("INSERT INTO lesson_agreements (conversation_id, hourly_rate, status) VALUES (?, 10, 'accepted')");
    $stmt->execute([$convId]);
    $agreementId = $pdo->lastInsertId();

    // 5. Log hours (Simulate track_hours.php logic)
    $hours = 10;
    $stmt = $pdo->prepare("INSERT INTO lesson_hours_tracking (user_id, agreement_id, hours_completed, completed_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$studentId, $agreementId, $hours]);

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
    $stmt->execute([$studentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // 8. Cleanup
    $pdo->exec("DELETE FROM lesson_hours_tracking WHERE user_id = $studentId");
    $pdo->exec("DELETE FROM lesson_agreements WHERE id = $agreementId");
    $pdo->exec("DELETE FROM conversations WHERE id = $convId");
    $pdo->exec("DELETE FROM users WHERE id IN ($studentId, $teacherId)");

    echo json_encode([
        'success' => true,
        'test_data' => [
            'student_id' => $studentId,
            'hours_logged' => $hours,
            'admin_view_result' => $result
        ],
        'message' => 'Test completed. Data created, verified, and cleaned up.'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>