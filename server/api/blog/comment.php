<?php
require_once __DIR__ . '/../config/cors.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
    exit;
}

// Şimdilik yorumu sadece başarılı kabul ediyoruz.
// İleride veritabanına kayıt eklemek istersen burada yapabilirsin.

echo json_encode([
    'success' => true,
    'message' => 'Yorum eklendi'
]);

