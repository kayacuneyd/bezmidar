<?php
// Test database connection
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json');

try {
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'message' => 'Database connection successful!',
        'users_count' => $result['count'],
        'database' => DB_NAME,
        'host' => DB_HOST
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed',
        'message' => $e->getMessage()
    ]);
}
?>