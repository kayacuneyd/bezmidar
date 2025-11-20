<?php
require_once '../config/database.php';

try {
    $stmt = $pdo->query("SELECT id, name, slug, icon FROM subjects ORDER BY sort_order, name");
    $subjects = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $subjects
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>