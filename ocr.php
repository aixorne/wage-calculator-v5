<?php
declare(strict_types=1);

/* Wage Calculator OCR endpoint: detailed safe errors + Render diagnostics. */
ini_set('display_errors', '0');
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function ocrLog(string $stage, array $details = []): void {
    // Never log API keys, uploaded content, or full OCR responses.
    error_log('[WageCalculator OCR] ' . $stage . ($details ? ' ' . json_encode($details, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) : ''));
}
function jsonResponse(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) return false;
    ocrLog('PHP warning/error', ['severity' => $severity, 'message' => $message, 'file' => basename($file), 'line' => $line]);
    return false;
});
register_shutdown_function(function (): void {
    $last = error_get_last();
    if ($last && in_array($last['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        ocrLog('PHP fatal error', ['type' => $last['type'], 'message' => $last['message'], 'file' => basename($last['file']), 'line' => $last['line']]);
    }
});

ocrLog('request received', ['method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown', 'content_length' => (int)($_SERVER['CONTENT_LENGTH'] ?? 0)]);
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method ไม่ถูกต้อง กรุณาอัปโหลดผ่านหน้าเว็บ', 'code' => 'METHOD_NOT_ALLOWED'], 405);
}
$configPath = __DIR__ . '/config.php';
if (!is_file($configPath)) {
    ocrLog('config missing');
    jsonResponse(['success' => false, 'error' => 'ไม่พบไฟล์ config.php ในโฟลเดอร์เดียวกับ ocr.php', 'code' => 'CONFIG_MISSING'], 500);
}
require_once $configPath;
$apiKey = isset($OCR_API_KEY) ? trim((string)$OCR_API_KEY) : '';
if ($apiKey === '') {
    ocrLog('API key missing');
    jsonResponse(['success' => false, 'error' => 'ไม่พบ API Key: ตรวจ Render → Environment ว่ามี OCR_SPACE_API_KEY แล้วกด Deploy ใหม่', 'code' => 'API_KEY_MISSING'], 500);
}
if (!extension_loaded('curl')) {
    ocrLog('PHP cURL extension missing');
    jsonResponse(['success' => false, 'error' => 'เซิร์ฟเวอร์ไม่มี PHP cURL จึงเชื่อมต่อบริการ OCR ไม่ได้', 'code' => 'CURL_MISSING'], 500);
}
if (!isset($_FILES['image'])) {
    $postMax = ini_get('post_max_size');
    ocrLog('image missing from request', ['post_max_size' => $postMax]);
    jsonResponse(['success' => false, 'error' => 'เซิร์ฟเวอร์ไม่ได้รับไฟล์รูป ตรวจสอบขนาดรูปและค่า post_max_size (' . $postMax . ')', 'code' => 'IMAGE_MISSING'], 400);
}
$file = $_FILES['image'];
if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
    $err = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE => 'ไฟล์ใหญ่เกิน upload_max_filesize ของเซิร์ฟเวอร์',
        UPLOAD_ERR_FORM_SIZE => 'ไฟล์ใหญ่เกินขนาดที่ฟอร์มกำหนด',
        UPLOAD_ERR_PARTIAL => 'อัปโหลดรูปไม่ครบ กรุณาลองใหม่',
        UPLOAD_ERR_NO_FILE => 'ไม่พบไฟล์รูป กรุณาเลือกรูปใหม่',
        UPLOAD_ERR_NO_TMP_DIR => 'เซิร์ฟเวอร์ไม่มีโฟลเดอร์ชั่วคราวสำหรับอัปโหลด',
        UPLOAD_ERR_CANT_WRITE => 'เซิร์ฟเวอร์บันทึกไฟล์อัปโหลดไม่ได้',
        UPLOAD_ERR_EXTENSION => 'ส่วนขยาย PHP หยุดการอัปโหลดไฟล์'
    ];
    ocrLog('upload error', ['code' => $err, 'size' => (int)($file['size'] ?? 0)]);
    jsonResponse(['success' => false, 'error' => $uploadErrors[$err] ?? 'อัปโหลดรูปไม่สำเร็จ (รหัส ' . $err . ')', 'code' => 'UPLOAD_ERROR'], 400);
}
if ((int)($file['size'] ?? 0) > 10 * 1024 * 1024) {
    ocrLog('file exceeds 10MB', ['size' => (int)$file['size']]);
    jsonResponse(['success' => false, 'error' => 'ไฟล์ใหญ่เกิน 10MB กรุณาเลือกรูปที่เล็กลง', 'code' => 'FILE_TOO_LARGE'], 413);
}
$tmp = (string)($file['tmp_name'] ?? '');
if ($tmp === '' || !is_uploaded_file($tmp)) {
    ocrLog('invalid uploaded temp file');
    jsonResponse(['success' => false, 'error' => 'ไฟล์อัปโหลดไม่ถูกต้อง กรุณาลองใหม่', 'code' => 'INVALID_UPLOAD'], 400);
}
$mime = function_exists('mime_content_type') ? (string)mime_content_type($tmp) : '';
$allowed = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($mime, $allowed, true)) {
    ocrLog('unsupported image type', ['mime' => $mime]);
    jsonResponse(['success' => false, 'error' => 'ชนิดรูปไม่รองรับ (' . ($mime ?: 'ไม่ทราบชนิด') . ') รองรับ JPG, PNG และ WebP', 'code' => 'UNSUPPORTED_IMAGE'], 415);
}
if (!class_exists('CURLFile')) {
    ocrLog('CURLFile unavailable');
    jsonResponse(['success' => false, 'error' => 'PHP cURL ใช้งานไม่สมบูรณ์ กรุณาตรวจสอบเซิร์ฟเวอร์', 'code' => 'CURLFILE_MISSING'], 500);
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
ocrLog('sending request to OCR.Space', ['size' => (int)$file['size'], 'mime' => $mime]);
$ch = curl_init('https://api.ocr.space/parse/image');
if ($ch === false) {
    ocrLog('curl_init failed');
    jsonResponse(['success' => false, 'error' => 'เริ่มการเชื่อมต่อ OCR.Space ไม่สำเร็จ', 'code' => 'CURL_INIT_FAILED'], 502);
}
curl_setopt_array($ch, [
    CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postFields,
    CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 90,
    CURLOPT_CONNECTTIMEOUT => 20,
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
    CURLOPT_USERAGENT => 'WageCalculator/1.0'
]);
$response = curl_exec($ch);
$curlErrno = curl_errno($ch);
$curlError = curl_error($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($response === false) {
    ocrLog('cURL request failed', ['errno' => $curlErrno, 'error' => $curlError]);
    $hint = $curlErrno === 28 ? 'หมดเวลารอการตอบกลับ (Timeout) กรุณาลองใหม่' : 'เชื่อมต่อ OCR.Space ไม่ได้ กรุณาลองใหม่';
    jsonResponse(['success' => false, 'error' => $hint . ' รายละเอียด: ' . ($curlError ?: 'ไม่ทราบสาเหตุ'), 'code' => 'CURL_REQUEST_FAILED', 'curlCode' => $curlErrno], 502);
}
$data = json_decode($response, true);
if (!is_array($data)) {
    ocrLog('invalid JSON from OCR.Space', ['http_code' => $httpCode, 'response_length' => strlen((string)$response)]);
    jsonResponse(['success' => false, 'error' => 'OCR.Space ส่งข้อมูลกลับมาไม่ใช่ JSON (HTTP ' . $httpCode . ')', 'code' => 'INVALID_PROVIDER_RESPONSE', 'httpCode' => $httpCode], 502);
}
if ($httpCode < 200 || $httpCode >= 300 || !empty($data['IsErroredOnProcessing'])) {
    $providerMessage = '';
    if (!empty($data['ErrorMessage'])) {
        $providerMessage = is_array($data['ErrorMessage']) ? implode(' ', array_map('strval', $data['ErrorMessage'])) : (string)$data['ErrorMessage'];
    } elseif (!empty($data['ErrorDetails'])) {
        $providerMessage = is_array($data['ErrorDetails']) ? implode(' ', array_map('strval', $data['ErrorDetails'])) : (string)$data['ErrorDetails'];
    }
    // Keep provider message useful but never log/return request credentials.
    $providerMessage = trim(strip_tags($providerMessage));
    ocrLog('OCR.Space returned error', ['http_code' => $httpCode, 'provider_message' => mb_substr($providerMessage, 0, 500)]);
    $lower = strtolower($providerMessage);
    if (str_contains($lower, 'apikey') || str_contains($lower, 'api key') || $httpCode === 401 || $httpCode === 403) {
        $message = 'OCR.Space ปฏิเสธ API Key กรุณาตรวจสอบ OCR_SPACE_API_KEY ใน Render Environment';
    } elseif (str_contains($lower, 'limit') || str_contains($lower, 'quota') || str_contains($lower, 'credits')) {
        $message = 'โควตา OCR.Space อาจหมดหรือถึงขีดจำกัด: ' . $providerMessage;
    } else {
        $message = 'OCR.Space ประมวลผลรูปไม่สำเร็จ' . ($providerMessage !== '' ? ': ' . mb_substr($providerMessage, 0, 350) : ' (HTTP ' . $httpCode . ')');
    }
    jsonResponse(['success' => false, 'error' => $message, 'code' => 'OCR_PROVIDER_ERROR', 'httpCode' => $httpCode], 502);
}
$text = '';
if (!empty($data['ParsedResults']) && is_array($data['ParsedResults'])) {
    foreach ($data['ParsedResults'] as $result) if (isset($result['ParsedText'])) $text .= "\n" . (string)$result['ParsedText'];
}
$text = trim($text);
if ($text === '') {
    $details = '';
    if (!empty($data['ParsedResults'][0]['ErrorMessage'])) $details = (string)$data['ParsedResults'][0]['ErrorMessage'];
    ocrLog('OCR completed but no text extracted', ['http_code' => $httpCode, 'details' => $details]);
    jsonResponse(['success' => false, 'error' => 'OCR ทำงานแล้วแต่ไม่พบข้อความในรูป กรุณาใช้รูปที่คมชัดขึ้น' . ($details ? ' รายละเอียด: ' . $details : ''), 'code' => 'NO_TEXT_FOUND', 'httpCode' => $httpCode], 422);
}
ocrLog('OCR succeeded', ['http_code' => $httpCode, 'text_length' => mb_strlen($text)]);
jsonResponse(['success' => true, 'text' => $text, 'httpCode' => $httpCode]);
