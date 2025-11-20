<?php
require_once '../config/db.php';
require_once '../config/auth.php';

$user = authenticate(['parent']);

try {
    $stmt = $pdo->prepare("
        SELECT r.*, s.name as subject_name
        FROM lesson_requests r
        JOIN subjects s ON r.subject_id = s.id
        WHERE r.parent_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$user['id']]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($requests);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Veritabanı hatası']);
}
