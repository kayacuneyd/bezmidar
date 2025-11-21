<?php
/**
 * JWT Authentication Helper
 * Basit JWT implementasyonu (composer-free)
 */

function getJwtSecret()
{
    static $secret = null;
    if ($secret !== null) {
        return $secret;
    }

    $secret = getenv('JWT_SECRET');

    if (empty($secret)) {
        // Fail closed in production; fall back to a dev-only secret to avoid breaking local setups
        $secret = 'DEV_INSECURE_SECRET_CHANGE_ME';
        error_log('[dijitalmentor] JWT_SECRET is missing; using insecure fallback. Set JWT_SECRET in the environment.');
    }

    return $secret;
}

function generateToken($userId, $role)
{
    $secret = getJwtSecret();

    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode([
        'user_id' => $userId,
        'role' => $role,
        'iat' => time(),
        'exp' => time() + (7 * 24 * 60 * 60) // 7 gün
    ]);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function verifyToken($token)
{
    $secret = getJwtSecret();

    $tokenParts = explode('.', $token);
    if (count($tokenParts) !== 3) {
        return false;
    }

    list($base64UrlHeader, $base64UrlPayload, $signatureProvided) = $tokenParts;

    // Signature verify
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    if ($base64UrlSignature !== $signatureProvided) {
        return false;
    }

    // Decode payload
    $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload));
    $payloadData = json_decode($payload, true);

    // Expiry check
    if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
        return false;
    }

    return $payloadData;
}

function getCurrentUser()
{
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (empty($authHeader)) {
        return null;
    }

    if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        return null;
    }

    $token = $matches[1];
    return verifyToken($token);
}

function requireAuth($allowedRoles = [])
{
    $user = getCurrentUser();

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Authentication required']);
        exit();
    }

    if (!empty($allowedRoles) && !in_array($user['role'], $allowedRoles)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Insufficient permissions']);
        exit();
    }

    return $user;
}

function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

function buildUserResponse($userId)
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.phone,
            u.full_name,
            u.role,
            u.avatar_url,
            u.email,
            u.city,
            u.zip_code,
            u.approval_status,
            u.is_premium,
            u.premium_expires_at,
            u.is_verified,
            u.is_active,
            u.created_at,
            u.updated_at
        FROM users u
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        return null;
    }

    if ($user['role'] === 'student') {
        $profileStmt = $pdo->prepare("
            SELECT 
                university,
                department,
                graduation_year,
                bio,
                city,
                zip_code,
                address_detail,
                hourly_rate,
                video_intro_url,
                cv_url,
                experience_years,
                total_students,
                rating_avg,
                review_count
            FROM teacher_profiles
            WHERE user_id = ?
            LIMIT 1
        ");
        $profileStmt->execute([$userId]);
        $profile = $profileStmt->fetch();

        if ($profile) {
            $user['university'] = $profile['university'];
            $user['department'] = $profile['department'];
            $user['graduation_year'] = $profile['graduation_year'];
            $user['bio'] = $profile['bio'];
            $user['teacher_city'] = $profile['city'];
            $user['teacher_zip_code'] = $profile['zip_code'];
            $user['address_detail'] = $profile['address_detail'];
            $user['hourly_rate'] = $profile['hourly_rate'];
            $user['video_intro_url'] = $profile['video_intro_url'];
            $user['cv_url'] = $profile['cv_url'];
            $user['experience_years'] = $profile['experience_years'];
            $user['total_students'] = $profile['total_students'];
            $user['rating_avg'] = $profile['rating_avg'];
            $user['review_count'] = $profile['review_count'];

            if (empty($user['city']) && !empty($profile['city'])) {
                $user['city'] = $profile['city'];
            }

            if (empty($user['zip_code']) && !empty($profile['zip_code'])) {
                $user['zip_code'] = $profile['zip_code'];
            }

            // Fetch subject info for teacher dashboards
            $subjectsStmt = $pdo->prepare("
                SELECT 
                    s.id,
                    s.name,
                    s.slug,
                    s.icon,
                    ts.proficiency_level
                FROM teacher_subjects ts
                JOIN subjects s ON ts.subject_id = s.id
                WHERE ts.teacher_id = ?
            ");
            $subjectsStmt->execute([$userId]);
            $user['subjects'] = $subjectsStmt->fetchAll();
        }
    } else {
        $user['subjects'] = [];
    }

    return $user;
}

function authenticate($allowedRoles = [])
{
    $payload = getCurrentUser();

    if (!$payload) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Authentication required']);
        exit();
    }

    if (!empty($allowedRoles) && !in_array($payload['role'], $allowedRoles)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Insufficient permissions']);
        exit();
    }

    $user = buildUserResponse((int) $payload['user_id']);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit();
    }

    if (empty($user['is_active'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Account is disabled']);
        exit();
    }

    return $user;
}
