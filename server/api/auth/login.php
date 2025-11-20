<?php
require_once '../config/cors.php';
require_once '../config/db.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'error' => 'Method not allowed']));
}

$data = json_decode(file_get_contents('php://input'), true);

$phone = trim($data['phone'] ?? '');
$password = $data['password'] ?? '';

if (empty($phone) || empty($password)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Phone and password required']));
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if (!$user || !verifyPassword($password, $user['password_hash'])) {
        http_response_code(401);
        die(json_encode(['success' => false, 'error' => 'Invalid credentials']));
    }

    if (!$user['is_active']) {
        http_response_code(403);
        die(json_encode(['success' => false, 'error' => 'Account is disabled']));
    }

    $token = generateToken($user['id'], $user['role']);
    $userData = buildUserResponse((int) $user['id']);

    echo json_encode([
        'success' => true,
        'data' => [
            'token' => $token,
            'user' => $userData
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Login failed']);
}
