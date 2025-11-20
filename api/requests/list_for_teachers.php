<?php
require_once '../config/database.php';

$user = getCurrentUser();

if (!$user || $user['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(['error' => 'Only teachers can access lesson requests']);
    exit;
}

try {
    $query = "
        SELECT 
            lr.id,
            lr.title,
            lr.description,
            lr.city,
            lr.budget_range,
            lr.status,
            lr.created_at,
            s.name as subject_name,
            s.icon as subject_icon,
            u.full_name as parent_name,
            u.phone as parent_phone,
            u.city as parent_city
        FROM lesson_requests lr
        INNER JOIN subjects s ON lr.subject_id = s.id
        INNER JOIN users u ON lr.parent_id = u.id
        WHERE lr.status = 'active'
        ORDER BY lr.created_at DESC
    ";

    $stmt = $pdo->query($query);
    $requests = $stmt->fetchAll();

    // Hide phone numbers for non-premium users
    foreach ($requests as &$request) {
        if (!$user['is_premium']) {
            $request['parent_phone'] = null;
            $request['is_premium_required'] = true;
        } else {
            $request['is_premium_required'] = false;
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $requests,
        'user_premium' => (bool) $user['is_premium']
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>