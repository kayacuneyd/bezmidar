<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/blog_tables.php';

header('Content-Type: application/json; charset=utf-8');

$admin = requireAuth(['admin']);
ensureBlogTables($pdo);

try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            slug,
            title,
            excerpt,
            content,
            content_markdown,
            author,
            image,
            likes,
            is_published,
            created_at,
            updated_at
        FROM blog_posts
        ORDER BY created_at DESC
        LIMIT 200
    ");
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $posts]);
} catch (Throwable $e) {
    error_log('Admin blog list error: ' . $e->getMessage());
    http_response_code(500);
    $message = 'Blog yazıları getirilemedi';

    if ($e instanceof PDOException) {
        $driverCode = $e->errorInfo[1] ?? null;
        if ($driverCode === 1146) {
            $message = 'Blog tabloları bulunamadı (migrasyon gerekli)';
        } elseif ($driverCode === 1044 || str_contains(strtolower($e->getMessage()), 'denied')) {
            $message = 'Veritabanında tablo oluşturma izni yok. Lütfen create_blog_tables.sql migrasyonunu çalıştırın.';
        } else {
            $message .= ' (' . $e->getMessage() . ')';
        }
    } else {
        $message .= ' (' . $e->getMessage() . ')';
    }

    echo json_encode(['success' => false, 'error' => $message]);
}
