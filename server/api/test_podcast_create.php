<?php
// Test script for Podcast Creation
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once __DIR__ . '/config/db.php';

// Mock Admin User (we need to bypass auth or simulate it, but for this test we'll just test the DB insert part if possible, 
// OR we can try to hit the endpoint with a valid token if we had one. 
// Better approach: Replicate the logic of create.php but without the auth check to test the DB/Logic part first.)

try {
    echo "Testing Podcast Creation Logic...\n";

    // 1. Validate Input (Simulated)
    $title = "Test Podcast " . time();
    $topic = "Testing the podcast creation flow";
    $publishDate = date('Y-m-d');

    // Generate slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    echo "Generated Slug: $slug\n";

    // 2. Insert into DB
    $sql = "INSERT INTO podcast_episodes (
        slug, title, topic_prompt, publish_date, processing_status
    ) VALUES (
        ?, ?, ?, ?, 'pending'
    )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$slug, $title, $topic, $publishDate]);
    $id = $pdo->lastInsertId();

    echo "Success! Podcast Episode created with ID: $id\n";

    // Check GITHUB_TOKEN
    $githubToken = getenv('GITHUB_TOKEN');
    echo "GITHUB_TOKEN: " . ($githubToken ? "SET (length: " . strlen($githubToken) . ")" : "NOT SET") . "\n";

    if (!$githubToken) {
        echo "WARNING: GITHUB_TOKEN is missing. Automatic generation will NOT work.\n";
    }

    // 3. Cleanup
    $pdo->exec("DELETE FROM podcast_episodes WHERE id = $id");
    echo "Cleanup complete.\n";

    echo json_encode(['success' => true, 'id' => $id]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}
?>