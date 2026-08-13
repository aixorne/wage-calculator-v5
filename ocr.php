<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Method ไม่ถูกต้อง'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$OCR_API_KEY) {
    echo json_encode([
        'success' => false,
        'error' => 'ไม่พบ OCR_SPACE_API_KEY ใน Environment'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_FILES['image'])) {
    echo json_encode([
        'success' => false,
        'error' => 'ไม่พบไฟล์รูปภาพ'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$file = $_FILES['image'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'success' => false,
        'error' => 'อัปโหลดรูปไม่สำเร็จ'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($file['size'] > 10 * 1024 * 1024) {
    echo json_encode([
        'success' => false,
        'error' => 'ไฟล์ใหญ่เกิน 10MB'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$tmp = $file['tmp_name'];

$mime = mime_content_type($tmp);

$allowed = [
    'image/jpeg',
    'image/png',
    'image/webp',
    'image/jpg'
];

if (!in_array($mime, $allowed, true)) {
    echo json_encode([
        'success' => false,
        'error' => 'รองรับเฉพาะ JPG, PNG และ WebP'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$ch = curl_init();

$postFields = [
    'apikey' => $OCR_API_KEY,
    'language' => 'eng',
    'isOverlayRequired' => 'false',
    'detectOrientation' => 'true',
    'scale' => 'true',
    'OCREngine' => '2',
    'file' => new CURLFile(
        $tmp,
        $mime,
        $file['name']
    )
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.ocr.space/parse/image',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 90,
    CURLOPT_CONNECTTIMEOUT => 20,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json'
    ]
]);

$response = curl_exec($ch);

$curlError = curl_error($ch);

$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);

curl_close($ch);

if ($response === false) {
    echo json_encode([
        'success' => false,
        'error' => 'เชื่อมต่อ OCR.Space ไม่สำเร็จ: ' . $curlError
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode($response, true);

if (!is_array($data)) {
    echo json_encode([
        'success' => false,
        'error' => 'OCR.Space ส่งข้อมูลไม่ถูกต้อง'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!empty($data['IsErroredOnProcessing'])) {

    $message = 'OCR ไม่สำเร็จ';

    if (!empty($data['ErrorMessage'])) {

        if (is_array($data['ErrorMessage'])) {
            $message = implode(
                ' ',
                $data['ErrorMessage']
            );
        } else {
            $message = $data['ErrorMessage'];
        }
    }

    echo json_encode([
        'success' => false,
        'error' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$text = '';

if (!empty($data['ParsedResults'])) {

    foreach ($data['ParsedResults'] as $result) {

        if (isset($result['ParsedText'])) {
            $text .= "\n" . $result['ParsedText'];
        }
    }
}

$text = trim($text);

echo json_encode([
    'success' => true,
    'text' => $text,
    'httpCode' => $httpCode
], JSON_UNESCAPED_UNICODE);
