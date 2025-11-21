<?php
require_once '../config/cors.php';
require_once '../config/db.php';
require_once '../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    die(json_encode(['success' => false, 'error' => 'Method not allowed']));
}

$city = $_GET['city'] ?? '';
$subjectSlug = $_GET['subject'] ?? '';
$maxRate = isset($_GET['max_rate']) ? (float) $_GET['max_rate'] : null;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    $where = ["u.role = 'student'", "u.is_active = 1", "u.approval_status = 'approved'"]; // Sadece onaylı öğretmenler
    $params = [];

    if (!empty($city)) {
        $where[] = "tp.city = ?";
        $params[] = $city;
    }

    if ($maxRate) {
        $where[] = "tp.hourly_rate <= ?";
        $params[] = $maxRate;
    }

    // Subject filtering requires a subquery or join
    if (!empty($subjectSlug)) {
        $where[] = "EXISTS (
            SELECT 1 FROM teacher_subjects ts 
            JOIN subjects s ON ts.subject_id = s.id 
            WHERE ts.teacher_id = u.id AND s.slug = ?
        )";
        $params[] = $subjectSlug;
    }

    $whereClause = implode(' AND ', $where);

    // Count total
    $countSql = "
        SELECT COUNT(*) 
        FROM users u 
        JOIN teacher_profiles tp ON u.id = tp.user_id 
        WHERE $whereClause
    ";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();

    // Fetch teachers
    $sql = "
        SELECT 
            u.id,
            u.full_name,
            u.avatar_url,
            u.is_verified,
            u.approval_status,
            COALESCE(tp.city, u.city) AS city,
            COALESCE(tp.zip_code, u.zip_code) AS zip_code,
            tp.university,
            tp.department,
            tp.graduation_year,
            tp.bio,
            tp.hourly_rate, 
            tp.experience_years,
            tp.rating_avg,
            tp.review_count,
            (
                SELECT GROUP_CONCAT(s.name SEPARATOR ',')
                FROM teacher_subjects ts
                JOIN subjects s ON ts.subject_id = s.id
                WHERE ts.teacher_id = u.id
            ) as subjects
        FROM users u
        JOIN teacher_profiles tp ON u.id = tp.user_id
        WHERE $whereClause
        ORDER BY tp.rating_avg DESC, tp.review_count DESC
        LIMIT $limit OFFSET $offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $teachers = $stmt->fetchAll();

    foreach ($teachers as &$teacher) {
        $coords = getCityCoordinates($teacher['city'] ?? '', $teacher['zip_code'] ?? '');
        $teacher['lat'] = $coords['lat'];
        $teacher['lng'] = $coords['lng'];

        if (!empty($teacher['subjects'])) {
            $teacher['subjects'] = array_filter(array_map('trim', explode(',', $teacher['subjects'])));
        } else {
            $teacher['subjects'] = [];
        }
    }
    unset($teacher);

    echo json_encode([
        'success' => true,
        'data' => [
            'teachers' => $teachers,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'pages' => ceil($total / $limit)
            ]
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to fetch teachers']);
}
