<?php
// Base allowed origins (app + api subdomain)
$allowedOrigins = [
    'https://dijitalmentor.de',
    'https://www.dijitalmentor.de',
    'https://api.dijitalmentor.de'
];

// Extra origins via env (comma separated)
$extraOrigins = getenv('ALLOWED_ORIGINS') ?: '';
if (!empty($extraOrigins)) {
    $extraList = array_filter(array_map('trim', explode(',', $extraOrigins)));
    $allowedOrigins = array_merge($allowedOrigins, $extraList);
}

// Optional: allow Vercel preview deployments when this env flag is true
$allowVercelPreview = filter_var(getenv('ALLOW_VERCEL_PREVIEW_ORIGINS'), FILTER_VALIDATE_BOOLEAN);
$allowLocalhost = filter_var(getenv('ALLOW_LOCALHOST_ORIGINS'), FILTER_VALIDATE_BOOLEAN);

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$originHost = $origin ? parse_url($origin, PHP_URL_HOST) : '';
$currentHost = $_SERVER['HTTP_HOST'] ?? '';

$isAllowed = in_array($origin, $allowedOrigins, true);

// Same-host shortcut (fetch from api.dijitalmentor.de to api.dijitalmentor.de)
if (!$isAllowed && $originHost && $currentHost && strcasecmp($originHost, $currentHost) === 0) {
    $isAllowed = true;
}

// Allow Vercel preview (e.g. https://foo.vercel.app)
if (!$isAllowed && $allowVercelPreview && preg_match('~^https://[a-z0-9-]+\\.vercel\\.app$~i', $origin)) {
    $isAllowed = true;
}

// Allow localhost for dev if enabled
if (
    !$isAllowed &&
    $allowLocalhost &&
    $origin &&
    preg_match('~^https?://localhost(?::\\d+)?$~i', $origin)
) {
    $isAllowed = true;
}

if ($isAllowed && $origin) {
    header("Access-Control-Allow-Origin: {$origin}");
    header("Vary: Origin");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code($isAllowed ? 200 : 403);
    exit();
}

if (!$isAllowed) {
    http_response_code(403);
    echo json_encode(['error' => 'CORS not allowed for this origin']);
    exit();
}
