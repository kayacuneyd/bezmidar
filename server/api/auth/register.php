<?php
require_once '../config/cors.php';
require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/rate_limit.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'error' => 'Method not allowed']));
}

$data = json_decode(file_get_contents('php://input'), true);

// Basic rate limit to prevent abuse of registrations
enforceRateLimit('auth:register', 20, 300);

$phone = trim($data['phone'] ?? '');
$password = $data['password'] ?? '';
$fullName = trim($data['full_name'] ?? '');
$role = $data['role'] ?? 'student'; // student or parent
$email = trim($data['email'] ?? '');
$city = trim($data['city'] ?? '');
$zipCode = trim($data['zip_code'] ?? '');

// Validation
if (empty($phone) || empty($password) || empty($fullName)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'All fields are required']));
}

if (!in_array($role, ['student', 'parent'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Invalid role']));
}

// Check if user exists
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);

    if ($stmt->fetch()) {
        http_response_code(409);
        die(json_encode(['success' => false, 'error' => 'Phone number already registered']));
    }

    $pdo->beginTransaction();

    // Create user
    $passwordHash = hashPassword($password);
    $approvalStatus = $role === 'student' ? 'pending' : 'approved';
    $isVerified = $role === 'student' ? 0 : 1;

    $stmt = $pdo->prepare("
        INSERT INTO users (
            phone,
            password_hash,
            full_name,
            role,
            email,
            city,
            zip_code,
            approval_status,
            is_premium,
            is_verified,
            is_active
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 1)
    ");
    $stmt->execute([
        $phone,
        $passwordHash,
        $fullName,
        $role,
        $email ?: null,
        $city ?: null,
        $zipCode ?: null,
        $approvalStatus,
        $isVerified
    ]);
    $userId = $pdo->lastInsertId();

    // If student, create profile with sensible defaults to avoid NULLs on UI
    if ($role === 'student') {
        $stmt = $pdo->prepare("
            INSERT INTO teacher_profiles (
                user_id,
                city,
                zip_code,
                bio,
                hourly_rate,
                experience_years,
                total_students,
                rating_avg,
                review_count
            )
            VALUES (?, ?, ?, '', 20.00, 0, 0, 0.00, 0)
        ");
        $stmt->execute([
            $userId,
            $city ?: null,
            $zipCode ?: null
        ]);
    }

    $pdo->commit();

    // Generate token
    $token = generateToken($userId, $role);
    $userData = buildUserResponse((int) $userId);

    echo json_encode([
        'success' => true,
        'data' => [
            'token' => $token,
            'user' => $userData
        ]
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Registration failed']);
}
