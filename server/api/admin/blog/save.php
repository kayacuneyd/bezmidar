<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/blog_tables.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$admin = requireAuth(['admin']);
ensureBlogTables($pdo);

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$id = isset($data['id']) ? (int) $data['id'] : 0;
$slug = trim($data['slug'] ?? '');
$title = trim($data['title'] ?? '');
$excerpt = trim($data['excerpt'] ?? '');
$content = trim($data['content'] ?? '');
$contentMarkdown = isset($data['content_markdown']) ? trim($data['content_markdown']) : null;
$author = trim($data['author'] ?? '');
$image = trim($data['image'] ?? '');
$isPublished = isset($data['is_published']) ? (int) !!$data['is_published'] : 1;

if ($slug === '' || $title === '' || $content === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Slug, başlık ve içerik zorunludur']);
    exit;
}

try {
    if ($id > 0) {
        $stmt = $pdo->prepare("
            UPDATE blog_posts
            SET slug = ?, title = ?, excerpt = ?, content = ?, content_markdown = ?, author = ?, image = ?, is_published = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$slug, $title, $excerpt, $content, $contentMarkdown, $author, $image, $isPublished, $id]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO blog_posts (slug, title, excerpt, content, content_markdown, author, image, is_published)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$slug, $title, $excerpt, $content, $contentMarkdown, $author, $image, $isPublished]);
        $id = (int) $pdo->lastInsertId();
    }

    echo json_encode(['success' => true, 'data' => ['id' => $id]]);
} catch (Throwable $e) {
    error_log('Admin blog save error: ' . $e->getMessage());
    http_response_code(500);

    $message = 'Blog yazısı kaydedilemedi';
    if ($e instanceof PDOException) {
        $sqlState = $e->getCode();
        $driverCode = $e->errorInfo[1] ?? null;

        if ($driverCode === 1062) {
            $message = 'Aynı slug ile bir yazı zaten mevcut';
        } elseif ($driverCode === 1146) {
            $message = 'Blog tabloları bulunamadı (migrasyon çalıştırılmalı)';
        } elseif ($driverCode === 1044 || str_contains(strtolower($e->getMessage()), 'denied')) {
            $message = 'Veritabanında tablo oluşturma izni yok. Lütfen create_blog_tables.sql migrasyonunu çalıştırın.';
        } else {
            $message .= " ({$e->getMessage()})";
        }
    } else {
        $message .= ' (' . $e->getMessage() . ')';
    }

    echo json_encode(['success' => false, 'error' => $message]);
}
