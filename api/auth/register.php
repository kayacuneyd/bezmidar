<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$full_name = $data['full_name'] ?? '';
$phone = $data['phone'] ?? '';
$password = $data['password'] ?? '';
$role = $data['role'] ?? 'parent';
$city = $data['city'] ?? null;
$zip_code = $data['zip_code'] ?? null;

if (empty($full_name) || empty($phone) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Full name, phone and password are required']);
    exit;
}

try {
    // Check if phone already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'Phone number already registered']);
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Determine approval status
    $approval_status = ($role === 'student') ? 'pending' : 'approved';

    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (phone, password_hash, full_name, role, city, zip_code, approval_status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$phone, $password_hash, $full_name, $role, $city, $zip_code, $approval_status]);

    $userId = $pdo->lastInsertId();

    // If teacher, create teacher profile
    if ($role === 'student') {
        $stmt = $pdo->prepare("
            INSERT INTO teacher_profiles (user_id, city, zip_code)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $city, $zip_code]);
    }

    // Fetch created user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    unset($user['password_hash']);

    // Generate token
    $token = 'mock-token-' . $userId . '-' . time();

    echo json_encode([
        'success' => true,
        'data' => [
            'token' => $token,
            'user' => $user
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
}
?>