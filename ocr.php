<?php
declare(strict_types=1);

// OCR endpoint for Wage Calculator. API key is read from config.php / Render Environment.
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
    jsonResponse(['success' => false, 'error' => 'กรุณาอัปโหลดรูปผ่านหน้าเว็บ Wage Calculator'], 405);
}

$configPath = __DIR__ . '/config.php';
if (!is_file($configPath)) {
    jsonResponse(['success' => false, 'error' => 'ไม่พบไฟล์ config.php ในโฟลเดอร์เดียวกับ ocr.php'], 500);
}
require_once $configPath;

// Support the project's existing variable and the Render environment variable directly.
$apiKey = '';
if (isset($OCR_API_KEY) && is_string($OCR_API_KEY)) {
    $apiKey = trim($OCR_API_KEY);
}
if ($apiKey === '') {
    $envKey = getenv('OCR_SPACE_API_KEY');
    if (is_string($envKey)) $apiKey = trim($envKey);
}
if ($apiKey === '') {
    jsonResponse(['success' => false, 'error' => 'เซิร์ฟเวอร์อ่านค่า OCR_SPACE_API_KEY ไม่ได้ กรุณาตรวจ Environment ใน Render แล้ว Deploy ใหม่'], 500);
}
if (!extension_loaded('curl')) {
    jsonResponse(['success' => false, 'error' => 'เซิร์ฟเวอร์ยังไม่ได้เปิด PHP cURL'], 500);
}
if (!isset($_FILES['image'])) {
    jsonResponse(['success' => false, 'error' => 'ไม่พบไฟล์รูปภาพในคำขอ (image) กรุณาลองอัปโหลดใหม่'], 400);
}

$file = $_FILES['image'];
$uploadError = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
if ($uploadError !== UPLOAD_ERR_OK) {
    $messages = [
        UPLOAD_ERR_INI_SIZE => 'ไฟล์ใหญ่เกินขนาดที่เซิร์ฟเวอร์อนุญาต',
        UPLOAD_ERR_FORM_SIZE => 'ไฟล์ใหญ่เกินขนาดที่ฟอร์มอนุญาต',
        UPLOAD_ERR_PARTIAL => 'อัปโหลดรูปไม่ครบ กรุณาลองใหม่',
        UPLOAD_ERR_NO_FILE => 'กรุณาเลือกรูปภาพก่อน',
        UPLOAD_ERR_NO_TMP_DIR => 'เซิร์ฟเวอร์ไม่มีโฟลเดอร์ชั่วคราวสำหรับอัปโหลด',
        UPLOAD_ERR_CANT_WRITE => 'เซิร์ฟเวอร์บันทึกไฟล์อัปโหลดไม่ได้',
        UPLOAD_ERR_EXTENSION => 'PHP หยุดการอัปโหลดไฟล์'
    ];
    jsonResponse(['success' => false, 'error' => $messages[$uploadError] ?? 'อัปโหลดรูปไม่สำเร็จ'], 400);
}

$size = (int)($file['size'] ?? 0);
if ($size <= 0) jsonResponse(['success' => false, 'error' => 'ไฟล์รูปภาพว่างเปล่าหรืออ่านไม่ได้'], 400);
if ($size > 10 * 1024 * 1024) jsonResponse(['success' => false, 'error' => 'ไฟล์ใหญ่เกิน 10MB กรุณาย่อรูปก่อนอัปโหลด'], 413);

$tmp = (string)($file['tmp_name'] ?? '');
if ($tmp === '' || !is_uploaded_file($tmp)) {
    jsonResponse(['success' => false, 'error' => 'ไฟล์อัปโหลดไม่ถูกต้อง กรุณาลองใหม่'], 400);
}

$mime = function_exists('mime_content_type') ? (string)mime_content_type($tmp) : '';
$allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/bmp', 'image/tiff'];
if (!in_array($mime, $allowed, true)) {
    jsonResponse(['success' => false, 'error' => 'รองรับไฟล์รูป JPG, PNG, WebP, BMP หรือ TIFF เท่านั้น'], 415);
}

$filename = basename((string)($file['name'] ?? 'upload.jpg'));
$postFields = [
    'language' => 'eng',
    'isOverlayRequired' => 'false',
    'detectOrientation' => 'true',
    'scale' => 'true',
    'OCREngine' => '2',
    'file' => new CURLFile($tmp, $mime, $filename)
];

$ch = curl_init('https://api.ocr.space/parse/image');
if ($ch === false) jsonResponse(['success' => false, 'error' => 'เริ่มการเชื่อมต่อ OCR.Space ไม่ได้'], 502);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 90,
    CURLOPT_CONNECTTIMEOUT => 20,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'apikey: ' . $apiKey
    ]
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    jsonResponse(['success' => false, 'error' => 'เชื่อมต่อ OCR.Space ไม่สำเร็จ กรุณาลองใหม่ภายหลัง (' . ($curlError ?: 'network error') . ')'], 502);
}
$data = json_decode((string)$response, true);
if (!is_array($data)) {
    jsonResponse(['success' => false, 'error' => 'OCR.Space ส่งคำตอบกลับมาไม่ใช่ JSON (HTTP ' . $httpCode . ')'], 502);
}

if ($httpCode < 200 || $httpCode >= 300 || !empty($data['IsErroredOnProcessing'])) {
    $details = [];
    foreach (['ErrorMessage', 'ErrorDetails'] as $field) {
        if (!empty($data[$field])) {
            $value = $data[$field];
            $details[] = is_array($value) ? implode(' ', array_map('strval', $value)) : (string)$value;
        }
    }
    $providerMessage = trim(implode(' ', $details));
    $lower = strtolower($providerMessage);
    if (strpos($lower, 'api key') !== false || strpos($lower, 'apikey') !== false || strpos($lower, 'invalid key') !== false || strpos($lower, 'invalid free') !== false) {
        jsonResponse([
            'success' => false,
            'error' => 'OCR.Space ปฏิเสธ API Key ที่เซิร์ฟเวอร์ส่งไป กรุณาตรวจว่าคีย์ใน Render เป็นคีย์เดียวกับที่ทดสอบสำเร็จ และไม่มีช่องว่าง หากตรงกัน ให้ติดต่อ OCR.Space Support พร้อมรหัส HTTP ' . $httpCode . ' โดยไม่ส่งคีย์',
            'code' => 'OCR_PROVIDER_KEY_REJECTED',
            'httpCode' => $httpCode
        ], 502);
    }
    jsonResponse([
        'success' => false,
        'error' => $providerMessage !== '' ? 'OCR.Space แจ้งข้อผิดพลาด: ' . $providerMessage : 'OCR.Space ประมวลผลไม่สำเร็จ (HTTP ' . $httpCode . ')',
        'code' => 'OCR_PROVIDER_ERROR',
        'httpCode' => $httpCode
    ], 502);
}

$text = '';
if (!empty($data['ParsedResults']) && is_array($data['ParsedResults'])) {
    foreach ($data['ParsedResults'] as $result) {
        if (isset($result['ParsedText'])) $text .= "\n" . (string)$result['ParsedText'];
    }
}
$text = trim($text);
if ($text === '') {
    jsonResponse(['success' => false, 'error' => 'OCR ทำงานแล้ว แต่ไม่พบข้อความในรูป กรุณาใช้รูปที่ชัดขึ้น'], 422);
}
jsonResponse(['success' => true, 'text' => $text, 'httpCode' => $httpCode]);
