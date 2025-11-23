<?php
// Lightweight .env loader for shared hosting where env vars aren't auto-loaded
function loadDotEnv($path)
{
    if (!is_readable($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'"); // strip quotes if any
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
    return true;
}

function loadEnvUpwards($startDir, $maxDepth = 5)
{
    $loaded = [];
    $dir = $startDir;
    for ($i = 0; $i <= $maxDepth; $i++) {
        if (!$dir) {
            break;
        }
        if (loadDotEnv($dir . '/.env')) {
            $loaded[] = $dir . '/.env';
        }
        if (loadDotEnv($dir . '/.env.local')) {
            $loaded[] = $dir . '/.env.local';
        }
        $parent = realpath($dir . '/..');
        if ($parent === $dir || !$parent) {
            break;
        }
        $dir = $parent;
    }
    return $loaded;
}

// Try to load env files relative to repo root
$baseDir = realpath(__DIR__ . '/../../');
$loadedFiles = [];

if ($baseDir) {
    $loadedFiles = array_merge($loadedFiles, loadEnvUpwards($baseDir, 3));
}
// Additional upward search from one level higher (covers api_root/server)
$altDir = realpath(__DIR__ . '/../../../');
if ($altDir) {
    $loadedFiles = array_merge($loadedFiles, loadEnvUpwards($altDir, 2));
}

// Read database credentials from environment variables
// Production: Set these in .env file on Hostinger
// Local dev: Set these in .env.local file
$envHost = getenv('DB_HOST') ?: getenv('MYSQL_HOST');
$envName = getenv('DB_NAME') ?: getenv('MYSQL_DATABASE');
$envUser = getenv('DB_USER') ?: getenv('MYSQL_USER');
$envPass = getenv('DB_PASS');

// Heroku/ClearDB style single URL fallback: mysql://user:pass@host/dbname
$databaseUrl = getenv('DATABASE_URL') ?: getenv('CLEARDB_DATABASE_URL') ?: getenv('JAWSDB_URL');
if ((!$envHost || !$envName || $envUser === false) && $databaseUrl) {
    $parts = parse_url($databaseUrl);
    if ($parts) {
        $envHost = $envHost ?: ($parts['host'] ?? null);
        $envName = $envName ?: (isset($parts['path']) ? ltrim($parts['path'], '/') : null);
        $envUser = $envUser !== false ? $envUser : ($parts['user'] ?? null);
        if ($envPass === false || $envPass === null) {
            $envPass = $parts['pass'] ?? '';
        }
    }
}

// Require environment variables - no hardcoded fallbacks for security
if (empty($envHost) || empty($envName) || $envUser === false) {
    http_response_code(500);
    error_log('[dijitalmentor] Database configuration missing. Set DB_HOST, DB_NAME, DB_USER, DB_PASS in .env file. Tried env files: ' . json_encode($loadedFiles));
    die(json_encode(['error' => 'Database configuration missing']));
}

$host = $envHost;
$dbname = $envName;
$username = $envUser;
$password = $envPass ?: '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed']));
}
