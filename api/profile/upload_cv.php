<?php
require_once '../config/database.php';

$user = getCurrentUser();

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Only teachers can upload CV
if ($user['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(['error' => 'Only teachers can upload CV']);
    exit;
}

// Check premium status
if (!$user['is_premium']) {
    http_response_code(403);
    echo json_encode([
        'error' => 'Premium membership required',
        'message' => 'CV yükleme özelliği premium üyeler içindir. 10€ Amazon Hediye Kartı göndererek premium üye olabilirsiniz.'
    ]);
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
$allowed = ['pdf', 'doc', 'docx'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Allowed: PDF, DOC, DOCX']);
    exit;
}

// Validate file size
if ($file['size'] > MAX_CV_SIZE) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large. Max size: 2MB']);
    exit;
}

try {
    // Create uploads directory if not exists
    $uploadDir = UPLOAD_DIR . 'cv/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $filename = 'cv_' . $user['id'] . '_' . time() . '.' . $ext;
    $uploadPath = $uploadDir . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $cvUrl = '/uploads/cv/' . $filename;

        // Update database
        $stmt = $pdo->prepare("UPDATE teacher_profiles SET cv_url = ? WHERE user_id = ?");
        $stmt->execute([$cvUrl, $user['id']]);

        echo json_encode([
            'success' => true,
            'data' => [
                'cv_url' => $cvUrl
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