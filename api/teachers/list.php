<?php
require_once '../config/database.php';

$city = $_GET['city'] ?? null;
$subject = $_GET['subject'] ?? null;
$max_rate = $_GET['max_rate'] ?? null;

try {
    $query = "
        SELECT DISTINCT
            u.id,
            u.full_name,
            u.avatar_url,
            u.city,
            u.zip_code,
            u.approval_status,
            tp.university,
            tp.department,
            tp.graduation_year,
            tp.bio,
            tp.hourly_rate,
            tp.experience_years,
            tp.rating_avg,
            tp.review_count
        FROM users u
        INNER JOIN teacher_profiles tp ON u.id = tp.user_id
        WHERE u.role = 'student' 
          AND u.is_active = 1
          AND u.approval_status = 'approved'
    ";

    $params = [];

    if ($city) {
        $query .= " AND u.city = ?";
        $params[] = $city;
    }

    if ($subject) {
        $query .= " AND EXISTS (
            SELECT 1 FROM teacher_subjects ts
            INNER JOIN subjects s ON ts.subject_id = s.id
            WHERE ts.teacher_id = u.id 
              AND (s.slug = ? OR s.name = ?)
        )";
        $params[] = $subject;
        $params[] = $subject;
    }

    if ($max_rate) {
        $query .= " AND tp.hourly_rate <= ?";
        $params[] = floatval($max_rate);
    }

    $query .= " ORDER BY tp.rating_avg DESC, tp.review_count DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $teachers = $stmt->fetchAll();

    // Fetch subjects for each teacher
    foreach ($teachers as &$teacher) {
        $stmt = $pdo->prepare("
            SELECT s.name, s.icon, ts.proficiency_level
            FROM teacher_subjects ts
            INNER JOIN subjects s ON ts.subject_id = s.id
            WHERE ts.teacher_id = ?
        ");
        $stmt->execute([$teacher['id']]);
        $teacher['subjects'] = $stmt->fetchAll();

        // Add lat/lng based on city (simplified geocoding)
        $teacher['lat'] = 52.52; // Default Berlin
        $teacher['lng'] = 13.405;
    }

    echo json_encode([
        'success' => true,
        'data' => $teachers
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>