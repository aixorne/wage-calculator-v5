<?php

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

function responseJSON($data, $status = 200)
{
    http_response_code($status);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    responseJSON([
        'success' => false,
        'error' => 'ต้องใช้ POST เท่านั้น'
    ], 405);
}


if (!isset($_FILES['image'])) {

    responseJSON([
        'success' => false,
        'error' => 'ไม่พบไฟล์ image ที่ส่งมา'
    ], 400);
}


$file = $_FILES['image'];


// ตรวจสอบ Upload Error
if ($file['error'] !== UPLOAD_ERR_OK) {

    $errors = [
        UPLOAD_ERR_INI_SIZE =>
            'ไฟล์ใหญ่เกิน upload_max_filesize ของ PHP',

        UPLOAD_ERR_FORM_SIZE =>
            'ไฟล์ใหญ่เกิน MAX_FILE_SIZE',

        UPLOAD_ERR_PARTIAL =>
            'อัปโหลดไฟล์มาไม่ครบ',

        UPLOAD_ERR_NO_FILE =>
            'ไม่ได้เลือกไฟล์',

        UPLOAD_ERR_NO_TMP_DIR =>
            'ไม่พบ Temporary Folder',

        UPLOAD_ERR_CANT_WRITE =>
            'PHP ไม่สามารถเขียนไฟล์ลง Disk',

        UPLOAD_ERR_EXTENSION =>
            'PHP Extension หยุดการอัปโหลด'
    ];

    responseJSON([
        'success' => false,
        'error' =>
            $errors[$file['error']]
            ?? 'Upload Error ไม่ทราบสาเหตุ',

        'error_code' => $file['error']
    ], 400);
}


// ตรวจสอบว่าเป็นไฟล์จริง
if (!is_uploaded_file($file['tmp_name'])) {

    responseJSON([
        'success' => false,
        'error' => 'ไฟล์ที่ได้รับไม่ใช่ไฟล์ Upload ที่ถูกต้อง'
    ], 400);
}


// จำกัดชนิดไฟล์
$allowedTypes = [
    'image/jpeg',
    'image/png',
    'image/webp'
];

if (!in_array($file['type'], $allowedTypes)) {

    responseJSON([
        'success' => false,
        'error' => 'รองรับเฉพาะ JPG, PNG และ WEBP'
    ], 400);
}


// จำกัดขนาด 10 MB
if ($file['size'] > 10 * 1024 * 1024) {

    responseJSON([
        'success' => false,
        'error' => 'รูปใหญ่เกิน 10 MB'
    ], 400);
}


// สร้าง CURL File
$cfile = new CURLFile(
    $file['tmp_name'],
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

    CURLOPT_URL =>
        'https://api.ocr.space/parse/image',

    CURLOPT_POST => true,

    CURLOPT_POSTFIELDS => $postData,

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_TIMEOUT => 60,

    CURLOPT_CONNECTTIMEOUT => 15,

    CURLOPT_SSL_VERIFYPEER => true

]);


$response = curl_exec($ch);


// ตรวจสอบ CURL Error
if ($response === false) {

    $curlError =
        curl_error($ch);

    $curlErrorNo =
        curl_errno($ch);

    curl_close($ch);

    responseJSON([
        'success' => false,
        'error' => 'เชื่อมต่อ OCR.Space ไม่สำเร็จ',
        'curl_error' => $curlError,
        'curl_error_code' => $curlErrorNo
    ], 500);
}


$httpCode =
    curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

curl_close($ch);


$data =
    json_decode(
        $response,
        true
    );


if ($data === null) {

    responseJSON([
        'success' => false,
        'error' => 'OCR.Space ไม่ได้ส่ง JSON กลับมา',
        'http_code' => $httpCode,
        'response' => substr(
            $response,
            0,
            1000
        )
    ], 500);
}


if (
    isset($data['IsErroredOnProcessing']) &&
    $data['IsErroredOnProcessing'] === true
) {

    $message =
        'OCR ประมวลผลไม่สำเร็จ';

    if (isset($data['ErrorMessage'])) {

        if (is_array($data['ErrorMessage'])) {

            $message =
                implode(
                    ', ',
                    $data['ErrorMessage']
                );

        } else {

            $message =
                $data['ErrorMessage'];
        }
    }

    responseJSON([
        'success' => false,
        'error' => $message,
        'http_code' => $httpCode
    ], 400);
}


$text = '';


if (isset($data['ParsedResults'])) {

    foreach (
        $data['ParsedResults']
        as $result
    ) {

        if (
            isset($result['ParsedText'])
        ) {

            $text .=
                $result['ParsedText']
                . "\n";
        }
    }
}


$text = trim($text);


responseJSON([
    'success' => true,
    'text' => $text,
    'http_code' => $httpCode
]);
