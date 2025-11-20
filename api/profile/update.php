<?php
require_once '../config/database.php';

$user = getCurrentUser();

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo->beginTransaction();

    // Update users table
    $stmt = $pdo->prepare("
        UPDATE users 
        SET full_name = ?, city = ?, zip_code = ?, email = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $data['full_name'] ?? $user['full_name'],
        $data['city'] ?? $user['city'],
        $data['zip_code'] ?? $user['zip_code'],
        $data['email'] ?? $user['email'],
        $user['id']
    ]);

    // If teacher, update teacher_profiles
    if ($user['role'] === 'student') {
        $stmt = $pdo->prepare("
            UPDATE teacher_profiles 
            SET university = ?, department = ?, graduation_year = ?, 
                bio = ?, hourly_rate = ?, experience_years = ?
            WHERE user_id = ?
        ");
        $stmt->execute([
            $data['university'] ?? null,
            $data['department'] ?? null,
            $data['graduation_year'] ?? null,
            $data['bio'] ?? null,
            $data['hourly_rate'] ?? 20.00,
            $data['experience_years'] ?? 0,
            $user['id']
        ]);
    }

    $pdo->commit();

    // Fetch updated user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user['id']]);
    $updatedUser = $stmt->fetch();
    unset($updatedUser['password_hash']);

    echo json_encode([
        'success' => true,
        'data' => $updatedUser
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Update failed']);
}
?>