<?php

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Method ไม่ถูกต้อง'
    ]);
    exit;
}

if (!isset($_FILES['image'])) {
    echo json_encode([
        'success' => false,
        'error' => 'ไม่พบไฟล์รูปภาพ'
    ]);
    exit;
}

$file = $_FILES['image'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'success' => false,
        'error' => 'อัปโหลดรูปไม่สำเร็จ'
    ]);
    exit;
}

$tmpFile = $file['tmp_name'];

$cfile = new CURLFile(
    $tmpFile,
    $file['type'],
    $file['name']
);

$postData = [
    'apikey' => $OCR_API_KEY,
    'language' => 'eng',
    'isOverlayRequired' => 'false',
    'detectOrientation' => 'true',
    'scale' => 'true',
    'OCREngine' => '2',
    'file' => $cfile
];

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.ocr.space/parse/image',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 60
]);

$response = curl_exec($ch);

if ($response === false) {

    $error = curl_error($ch);

    curl_close($ch);

    echo json_encode([
        'success' => false,
        'error' => 'เชื่อมต่อ OCR.Space ไม่สำเร็จ',
        'details' => $error
    ]);

    exit;
}

curl_close($ch);

$data = json_decode($response, true);

if (!$data) {

    echo json_encode([
        'success' => false,
        'error' => 'OCR.Space ส่งข้อมูลกลับมาไม่ถูกต้อง'
    ]);

    exit;
}

if (
    isset($data['IsErroredOnProcessing']) &&
    $data['IsErroredOnProcessing'] === true
) {

    $errorMessage = 'OCR ประมวลผลไม่สำเร็จ';

    if (isset($data['ErrorMessage'])) {
        $errorMessage = is_array($data['ErrorMessage'])
            ? implode(', ', $data['ErrorMessage'])
            : $data['ErrorMessage'];
    }

    echo json_encode([
        'success' => false,
        'error' => $errorMessage,
        'raw' => $data
    ]);

    exit;
}

$text = '';

if (isset($data['ParsedResults'])) {

    foreach ($data['ParsedResults'] as $result) {

        if (isset($result['ParsedText'])) {
            $text .= $result['ParsedText'] . "\n";
        }
    }
}

$text = trim($text);

echo json_encode([
    'success' => true,
    'text' => $text,
    'raw' => $data
], JSON_UNESCAPED_UNICODE);
