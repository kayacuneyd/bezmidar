<?php
require_once '../config/db.php';
require_once '../config/auth.php';

$user = authenticate(['parent']);

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['subject_id']) || !isset($data['title'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Eksik bilgi']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO lesson_requests (parent_id, subject_id, title, description, city, budget_range)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user['id'],
        $data['subject_id'],
        $data['title'],
        $data['description'] ?? '',
        $data['city'] ?? '',
        $data['budget_range'] ?? ''
    ]);

    echo json_encode(['success' => true, 'message' => 'Talep oluşturuldu', 'id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
