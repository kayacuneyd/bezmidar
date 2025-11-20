<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$phone = $data['phone'] ?? '';
$password = $data['password'] ?? '';

if (empty($phone) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Phone and password are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT u.*, 
               CASE WHEN u.role = 'student' THEN tp.bio ELSE NULL END as bio,
               CASE WHEN u.role = 'student' THEN tp.university ELSE NULL END as university,
               CASE WHEN u.role = 'student' THEN tp.department ELSE NULL END as department
        FROM users u
        LEFT JOIN teacher_profiles tp ON u.id = tp.user_id
        WHERE u.phone = ? AND u.is_active = 1
    ");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }

    // Remove password hash from response
    unset($user['password_hash']);

    // Generate token (simple version, use JWT library in production)
    $token = 'mock-token-' . $user['id'] . '-' . time();

    echo json_encode([
        'success' => true,
        'data' => [
            'token' => $token,
            'user' => $user
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>