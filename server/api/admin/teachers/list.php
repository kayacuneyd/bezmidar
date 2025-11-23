<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

$admin = requireAuth(['admin']);

$status = isset($_GET['status']) ? trim($_GET['status']) : 'pending';
$allowedStatuses = ['pending', 'approved', 'rejected'];
$statusFilter = in_array($status, $allowedStatuses, true) ? $status : null;

try {
    $sql = "
        SELECT 
            u.id,
            u.full_name,
            u.email,
            u.phone,
            u.city,
            u.zip_code,
            u.approval_status,
            u.is_active,
            u.created_at,
            tp.university,
            tp.department,
            tp.hourly_rate,
            tp.rating_avg,
            tp.review_count,
            tp.experience_years
        FROM users u
        LEFT JOIN teacher_profiles tp ON tp.user_id = u.id
        WHERE u.role = 'student'
    ";

    $params = [];
    if ($statusFilter) {
        $sql .= " AND u.approval_status = ? ";
        $params[] = $statusFilter;
    }

    $sql .= " ORDER BY u.created_at DESC LIMIT 200";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $teachers
    ]);
} catch (Throwable $e) {
    error_log('Admin teacher list error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Öğretmen listesi getirilemedi'
    ]);
}
