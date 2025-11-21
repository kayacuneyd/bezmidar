<?php
require_once '../config/cors.php';
require_once '../config/db.php';
require_once '../config/auth.php';

// Authenticated user info (used by frontend to refresh session)
$user = authenticate(); // returns full user object via buildUserResponse

echo json_encode([
    'success' => true,
    'data' => $user
]);
