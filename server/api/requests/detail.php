<?php
require_once '../config/cors.php';
require_once '../config/db.php';
require_once '../config/auth.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID gerekli']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT r.*, s.name as subject_name, u.full_name as parent_name, u.avatar_url as parent_avatar
        FROM lesson_requests r
        JOIN subjects s ON r.subject_id = s.id
        JOIN users u ON r.parent_id = u.id
        WHERE r.id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Talep bulunamadı']);
        exit;
    }

    echo json_encode(['success' => true, 'data' => $request]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Veritabanı hatası']);
}
