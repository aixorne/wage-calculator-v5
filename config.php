<?php

$OCR_API_KEY = getenv('OCR_SPACE_API_KEY');

if (!$OCR_API_KEY) {
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => false,
        'error' => 'ไม่พบ OCR_SPACE_API_KEY ใน Environment ของ Render'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}
