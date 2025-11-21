<?php
require_once '../config/cors.php';
require_once '../config/db.php';
require_once '../config/auth.php';

// Public endpoint or teacher only? Let's make it public for now but usually for teachers
// authenticate(); 

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$where = ["status = 'active'"];
$params = [];

if (isset($_GET['city']) && !empty($_GET['city'])) {
    $where[] = "city = ?";
    $params[] = $_GET['city'];
}

if (isset($_GET['subject_id']) && !empty($_GET['subject_id'])) {
    $where[] = "subject_id = ?";
    $params[] = $_GET['subject_id'];
}

$whereSQL = implode(' AND ', $where);

try {
    // Count total
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM lesson_requests WHERE $whereSQL");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();

    // Fetch items
    $sql = "
        SELECT r.*, s.name as subject_name, u.full_name as parent_name, u.avatar_url as parent_avatar
        FROM lesson_requests r
        JOIN subjects s ON r.subject_id = s.id
        JOIN users u ON r.parent_id = u.id
        WHERE $whereSQL
        ORDER BY r.created_at DESC
        LIMIT $limit OFFSET $offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'requests' => $requests,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'pages' => ceil($total / $limit)
            ]
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Veritabanı hatası']);
}
