<?php
/**
 * Database Configuration
 * 
 * IMPORTANT: Update these values with your Hostinger database credentials
 * For local development, use localhost settings
 */

// Determine environment from env vars instead of hardcoding secrets
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'dijitalmentor_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// JWT Secret for token generation (must be set in environment)
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'CHANGE_ME_IN_ENV');

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/../../uploads/');
define('MAX_AVATAR_SIZE', 2 * 1024 * 1024); // 2MB
define('MAX_CV_SIZE', 2 * 1024 * 1024); // 2MB

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed']));
}

// Helper function to get current user from JWT token
function getCurrentUser()
{
    global $pdo;

    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        return null;
    }

    $token = $matches[1];

    // Simple token validation (in production, use proper JWT library)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");

    // Extract user ID from token (format: mock-token-{userId}-{timestamp})
    if (preg_match('/mock-token-(\d+)/', $token, $userMatches)) {
        $stmt->execute([$userMatches[1]]);
        return $stmt->fetch();
    }

    return null;
}

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
?>
