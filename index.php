<!DOCTYPE html>
<html lang="th">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ระบบคำนวณค่าแรง V5</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>

<div class="container">

    <h1>📷 ระบบคำนวณค่าแรง V5</h1>

    <p class="subtitle">
        อัปโหลดรูปเวลาแสกนนิ้ว
    </p>

    <div class="card">

        <label>
            เลือกรูปภาพ
        </label>

        <input
            type="file"
            id="imageInput"
            accept="image/*"
        >

        <div
            id="previewBox"
            class="preview-box"
        >
            <img
                id="preview"
                src=""
                alt="Preview"
            >
        </div>

        <button
            id="ocrButton"
            onclick="runOCR()"
        >
            🔍 ตรวจสอบรูปภาพ
        </button>

        <div
            id="loading"
            class="loading"
        >
            ⏳ กำลังอ่านข้อมูลจากรูป...
        </div>

    </div>

    <div
        id="resultCard"
        class="card result-card"
    >

        <h2>📝 ผล OCR</h2>

        <textarea
            id="ocrResult"
            readonly
        ></textarea>

        <button
            onclick="copyResult()"
        >
            📋 Copy
        </button>

    </div>

</div>

<script>

const imageInput =
    document.getElementById('imageInput');

const preview =
    document.getElementById('preview');

const previewBox =
    document.getElementById('previewBox');

const resultCard =
    document.getElementById('resultCard');

const ocrResult =
    document.getElementById('ocrResult');

const loading =
    document.getElementById('loading');

const ocrButton =
    document.getElementById('ocrButton');


imageInput.addEventListener(
    'change',
    function () {

        const file = this.files[0];

        if (!file) {
            return;
        }

        const reader =
            new FileReader();

        reader.onload = function (e) {

            preview.src =
                e.target.result;

            previewBox.style.display =
                'block';
        };

        reader.readAsDataURL(file);
    }
);


async function runOCR() {

    const file =
        imageInput.files[0];

    if (!file) {

        alert(
            'กรุณาเลือกรูปภาพก่อน'
        );

        return;
    }

    const formData =
        new FormData();

    formData.append(
        'image',
        file
    );

    loading.style.display =
        'block';

    ocrButton.disabled =
        true;

    resultCard.style.display =
        'none';

    try {

        const response =
            await fetch(
                'ocr.php',
                {
                    method: 'POST',
                    body: formData
                }
            );

        const data =
            await response.json();

        if (!data.success) {

    let message =
        data.error || 'OCR ไม่สำเร็จ';

    if (data.error_code !== undefined) {
        message +=
            '\nUpload Error Code: ' +
            data.error_code;
    }

    if (data.curl_error) {
        message +=
            '\nCURL: ' +
            data.curl_error;
    }

    throw new Error(message);
}

        ocrResult.value =
            data.text || '';

        resultCard.style.display =
            'block';

    } catch (error) {

        alert(
            error.message
        );

    } finally {

        loading.style.display =
            'none';

        ocrButton.disabled =
            false;
    }
}


async function copyResult() {

    try {

        await navigator.clipboard.writeText(
            ocrResult.value
        );

        alert(
            'คัดลอกแล้ว'
        );

    } catch (error) {

        alert(
            'คัดลอกไม่สำเร็จ'
        );
    }
}

</script>

</body>
</html>
