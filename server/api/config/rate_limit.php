<?php
/**
 * Simple file-based rate limiter to throttle brute-force attempts.
 *
 * Usage:
 *   require_once '../config/rate_limit.php';
 *   enforceRateLimit('auth:login', 30, 60); // 30 req/min per IP
 */

function enforceRateLimit($key, $limit = 60, $windowSeconds = 60)
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $bucketKey = $key . ':' . $ip;

    $storageFile = sys_get_temp_dir() . '/dijitalmentor_rate_limits.json';
    $now = time();

    // Load and prune storage atomically
    $storage = [];
    if (file_exists($storageFile)) {
        $raw = file_get_contents($storageFile);
        $storage = json_decode($raw, true) ?: [];
    }

    $entries = $storage[$bucketKey] ?? [];
    // Keep only events within the window
    $entries = array_filter($entries, function ($ts) use ($now, $windowSeconds) {
        return ($ts + $windowSeconds) >= $now;
    });

    if (count($entries) >= $limit) {
        http_response_code(429);
        echo json_encode(['success' => false, 'error' => 'Too many requests, please try again later.']);
        exit();
    }

    $entries[] = $now;
    $storage[$bucketKey] = $entries;

    // Best-effort write; ignore failures so the API keeps working
    file_put_contents($storageFile, json_encode($storage), LOCK_EX);
}
