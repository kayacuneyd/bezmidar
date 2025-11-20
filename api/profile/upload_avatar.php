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

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];

// Validate file type
$allowed = ['jpg', 'jpeg', 'png', 'webp'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Allowed: jpg, jpeg, png, webp']);
    exit;
}

// Validate file size
if ($file['size'] > MAX_AVATAR_SIZE) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large. Max size: 2MB']);
    exit;
}

try {
    // Create uploads directory if not exists
    $uploadDir = UPLOAD_DIR . 'avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $filename = 'avatar_' . $user['id'] . '_' . time() . '.' . $ext;
    $uploadPath = $uploadDir . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $avatarUrl = '/uploads/avatars/' . $filename;

        // Update database
        $stmt = $pdo->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
        $stmt->execute([$avatarUrl, $user['id']]);

        echo json_encode([
            'success' => true,
            'data' => [
                'avatar_url' => $avatarUrl
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Upload failed']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>