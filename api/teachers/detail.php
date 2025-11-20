<?php
require_once '../config/database.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Teacher ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
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
            tp.review_count,
            tp.video_intro_url
        FROM users u
        INNER JOIN teacher_profiles tp ON u.id = tp.user_id
        WHERE u.id = ? AND u.role = 'student' AND u.is_active = 1
    ");
    $stmt->execute([$id]);
    $teacher = $stmt->fetch();

    if (!$teacher) {
        http_response_code(404);
        echo json_encode(['error' => 'Teacher not found']);
        exit;
    }

    // Fetch subjects
    $stmt = $pdo->prepare("
        SELECT s.name, s.icon, s.slug, ts.proficiency_level
        FROM teacher_subjects ts
        INNER JOIN subjects s ON ts.subject_id = s.id
        WHERE ts.teacher_id = ?
    ");
    $stmt->execute([$id]);
    $teacher['subjects'] = $stmt->fetchAll();

    // Fetch reviews
    $stmt = $pdo->prepare("
        SELECT r.rating, r.comment, r.created_at, u.full_name as parent_name
        FROM reviews r
        INNER JOIN users u ON r.parent_id = u.id
        WHERE r.teacher_id = ? AND r.is_approved = 1
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$id]);
    $teacher['reviews'] = $stmt->fetchAll();

    // Add lat/lng
    $teacher['lat'] = 52.52;
    $teacher['lng'] = 13.405;

    echo json_encode([
        'success' => true,
        'data' => $teacher
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>