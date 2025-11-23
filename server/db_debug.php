<?php
// Debug script to check DB connection
header('Content-Type: text/plain');
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "--- Database Connection Debugger ---\n";
echo "Current Directory: " . __DIR__ . "\n";

// Helper to load .env
function loadDotEnv($path)
{
    if (!file_exists($path))
        return false;
    echo "Found .env at: $path\n";
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#'))
            continue;
        if (str_contains($line, '=')) {
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
    return true;
}

// Try to load .env from various locations
$possiblePaths = [
    __DIR__ . '/.env',
    __DIR__ . '/api/.env',
    __DIR__ . '/../.env',
    $_SERVER['DOCUMENT_ROOT'] . '/.env',
    $_SERVER['DOCUMENT_ROOT'] . '/server/.env'
];

$loaded = false;
foreach ($possiblePaths as $path) {
    if (loadDotEnv($path)) {
        $loaded = true;
    }
}

if (!$loaded) {
    echo "WARNING: No .env file found in common locations.\n";
}

$host = getenv('DB_HOST') ?: getenv('MYSQL_HOST');
$name = getenv('DB_NAME') ?: getenv('MYSQL_DATABASE');
$user = getenv('DB_USER') ?: getenv('MYSQL_USER');
$pass = getenv('DB_PASS');

echo "\nConfiguration:\n";
echo "DB_HOST: " . ($host ?: 'NOT SET') . "\n";
echo "DB_NAME: " . ($name ?: 'NOT SET') . "\n";
echo "DB_USER: " . ($user ?: 'NOT SET') . "\n";
echo "DB_PASS: " . ($pass ? 'SET (length: ' . strlen($pass) . ')' : 'NOT SET') . "\n";

if (!$host || !$name || !$user) {
    die("\nERROR: Missing required configuration variables.\n");
}

echo "\nAttempting connection...\n";

try {
    $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "SUCCESS: Connected to database successfully!\n";
} catch (PDOException $e) {
    echo "ERROR: Connection failed.\n";
    echo "Message: " . $e->getMessage() . "\n";
}
?>