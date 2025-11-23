<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

$admin = requireAuth(['admin']);

try {
    $totalsStmt = $pdo->query("
        SELECT
            SUM(CASE WHEN u.role = 'parent' THEN lht.hours_completed ELSE 0 END) AS parent_hours,
            SUM(CASE WHEN u.role = 'student' THEN lht.hours_completed ELSE 0 END) AS teacher_hours,
            COUNT(DISTINCT CASE WHEN u.role = 'parent' THEN lht.user_id END) AS parent_count,
            COUNT(DISTINCT CASE WHEN u.role = 'student' THEN lht.user_id END) AS teacher_count
        FROM lesson_hours_tracking lht
        JOIN users u ON u.id = lht.user_id
    ");
    $overview = $totalsStmt->fetch(PDO::FETCH_ASSOC) ?: [];

    $recentStmt = $pdo->prepare("
        SELECT 
            lht.id,
            lht.user_id,
            u.full_name,
            u.role,
            lht.hours_completed,
            lht.notes,
            lht.completed_at
        FROM lesson_hours_tracking lht
        JOIN users u ON u.id = lht.user_id
        ORDER BY lht.completed_at DESC
        LIMIT 50
    ");
    $recentStmt->execute();
    $history = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'overview' => [
                'parent_hours' => (float) ($overview['parent_hours'] ?? 0),
                'teacher_hours' => (float) ($overview['teacher_hours'] ?? 0),
                'parent_count' => (int) ($overview['parent_count'] ?? 0),
                'teacher_count' => (int) ($overview['teacher_count'] ?? 0),
            ],
            'history' => $history
        ]
    ]);
} catch (Throwable $e) {
    error_log('Admin rewards overview error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Ödül verileri getirilemedi']);
}
