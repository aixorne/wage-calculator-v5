<?php
declare(strict_types=1);

/*
 * OCR endpoint for Wage Calculator.
 * Always returns JSON so the frontend can show a useful error instead of
 * failing with "Unexpected token '<'".
 */
ini_set('display_errors', '0');
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function jsonResponse(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method ไม่ถูกต้อง กรุณาอัปโหลดผ่านหน้าเว็บ'], 405);
}

$configPath = __DIR__ . '/config.php';
if (!is_file($configPath)) {
    jsonResponse([
        'success' => false,
        'error' => 'ไม่พบ config.php กรุณาสร้างไฟล์ config.php และตั้งค่า OCR_SPACE_API_KEY'
    ], 500);
}

require_once $configPath;
$apiKey = isset($OCR_API_KEY) ? trim((string)$OCR_API_KEY) : '';

if ($apiKey === '') {
    jsonResponse([
        'success' => false,
        'error' => 'ยังไม่ได้ตั้งค่า OCR_SPACE_API_KEY ใน config.php'
    ], 500);
}

if (!extension_loaded('curl')) {
    jsonResponse(['success' => false, 'error' => 'เซิร์ฟเวอร์ยังไม่ได้เปิดใช้งาน PHP cURL'], 500);
}

if (!isset($_FILES['image'])) {
    jsonResponse(['success' => false, 'error' => 'ไม่พบไฟล์รูปภาพที่อัปโหลด'], 400);
}

$file = $_FILES['image'];
if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE => 'ไฟล์ใหญ่เกินขนาด upload_max_filesize ของเซิร์ฟเวอร์',
        UPLOAD_ERR_FORM_SIZE => 'ไฟล์ใหญ่เกินขนาดที่ฟอร์มกำหนด',
        UPLOAD_ERR_PARTIAL => 'อัปโหลดรูปภาพไม่ครบ กรุณาลองใหม่',
        UPLOAD_ERR_NO_FILE => 'กรุณาเลือกรูปภาพก่อน',
        UPLOAD_ERR_NO_TMP_DIR => 'เซิร์ฟเวอร์ไม่มีโฟลเดอร์ชั่วคราวสำหรับอัปโหลด',
        UPLOAD_ERR_CANT_WRITE => 'เซิร์ฟเวอร์บันทึกไฟล์อัปโหลดไม่ได้',
        UPLOAD_ERR_EXTENSION => 'ส่วนขยาย PHP หยุดการอัปโหลดไฟล์'
    ];
    $err = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    jsonResponse([
        'success' => false,
        'error' => $uploadErrors[$err] ?? 'อัปโหลดรูปไม่สำเร็จ'
    ], 400);
}

if (($file['size'] ?? 0) > 10 * 1024 * 1024) {
    jsonResponse(['success' => false, 'error' => 'ไฟล์ใหญ่เกิน 10MB'], 413);
}

$tmp = $file['tmp_name'] ?? '';
if ($tmp === '' || !is_uploaded_file($tmp)) {
    jsonResponse(['success' => false, 'error' => 'ไฟล์อัปโหลดไม่ถูกต้อง กรุณาลองใหม่'], 400);
}

$mime = function_exists('mime_content_type') ? mime_content_type($tmp) : '';
$allowed = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($mime, $allowed, true)) {
    jsonResponse(['success' => false, 'error' => 'รองรับเฉพาะรูป JPG, PNG และ WebP'], 415);
}

$filename = basename((string)($file['name'] ?? 'upload.jpg'));
$postFields = [
    'apikey' => $apiKey,
    'language' => 'eng',
    'isOverlayRequired' => 'false',
    'detectOrientation' => 'true',
    'scale' => 'true',
    'OCREngine' => '2',
    'file' => new CURLFile($tmp, $mime, $filename)
];

$ch = curl_init('https://api.ocr.space/parse/image');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 90,
    CURLOPT_CONNECTTIMEOUT => 20,
    CURLOPT_HTTPHEADER => ['Accept: application/json']
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    jsonResponse([
        'success' => false,
        'error' => 'เชื่อมต่อ OCR.Space ไม่สำเร็จ: ' . ($curlError ?: 'ไม่ทราบสาเหตุ')
    ], 502);
}

$data = json_decode($response, true);
if (!is_array($data)) {
    jsonResponse([
        'success' => false,
        'error' => 'OCR.Space ส่งข้อมูลกลับมาไม่ถูกต้อง (HTTP ' . $httpCode . ')'
    ], 502);
}

if ($httpCode < 200 || $httpCode >= 300 || !empty($data['IsErroredOnProcessing'])) {
    $message = 'OCR ไม่สำเร็จ';
    if (!empty($data['ErrorMessage'])) {
        $message = is_array($data['ErrorMessage'])
            ? implode(' ', array_map('strval', $data['ErrorMessage']))
            : (string)$data['ErrorMessage'];
    } elseif (!empty($data['ErrorDetails'])) {
        $message = (string)$data['ErrorDetails'];
    }
    jsonResponse(['success' => false, 'error' => $message, 'httpCode' => $httpCode], 502);
}

$text = '';
if (!empty($data['ParsedResults']) && is_array($data['ParsedResults'])) {
    foreach ($data['ParsedResults'] as $result) {
        if (isset($result['ParsedText'])) {
            $text .= "\n" . (string)$result['ParsedText'];
        }
    }
}
$text = trim($text);

jsonResponse([
    'success' => true,
    'text' => $text,
    'httpCode' => $httpCode
]);
