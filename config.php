<?php

// อ่าน API Key จาก Environment ของ Render
$OCR_API_KEY = getenv('OCR_SPACE_API_KEY');

if (!$OCR_API_KEY) {
    die("ไม่พบ OCR_SPACE_API_KEY ใน Environment");
}
