<?php
require_once __DIR__ . '/server/config/db.php';

try {
    $stmt = $pdo->query("SELECT id, slug, title, is_published, processing_status, audio_url FROM podcast_episodes LIMIT 10");
    $episodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Episodes found: " . count($episodes) . "\n";
    foreach ($episodes as $ep) {
        echo "ID: {$ep['id']}, Slug: {$ep['slug']}, Published: {$ep['is_published']}, Status: {$ep['processing_status']}, URL: {$ep['audio_url']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
