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


    <!-- Upload Card -->
    <div class="card">

        <label for="imageInput">
            เลือกรูปภาพ
        </label>

        <input
            type="file"
            id="imageInput"
            accept="image/*"
        >


        <!-- Preview -->
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


        <!-- OCR Button -->
        <button
            id="ocrButton"
            onclick="runOCR()"
        >
            🔍 ตรวจสอบรูปภาพ
        </button>


        <!-- Loading -->
        <div
            id="loading"
            class="loading"
        >
            ⏳ กำลังบีบอัดรูปและอ่านข้อมูล...
        </div>

    </div>


    <!-- Result -->
    <div
        id="resultCard"
        class="card result-card"
    >

        <h2>
            📝 ผล OCR
        </h2>

        <textarea
            id="ocrResult"
            readonly
        ></textarea>

        <button
            id="copyButton"
            onclick="copyResult()"
        >
            📋 Copy
        </button>

    </div>

</div>


<script>

/* =========================================
   ELEMENTS
========================================= */

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

const copyButton =
    document.getElementById('copyButton');


/* =========================================
   IMAGE PREVIEW
========================================= */

imageInput.addEventListener(
    'change',
    function () {

        const file =
            this.files[0];

        if (!file) {

            previewBox.style.display =
                'none';

            return;
        }


        const reader =
            new FileReader();


        reader.onload =
            function (event) {

                preview.src =
                    event.target.result;

                previewBox.style.display =
                    'block';
            };


        reader.readAsDataURL(file);
    }
);


/* =========================================
   RUN OCR
========================================= */

async function runOCR() {

    const file =
        imageInput.files[0];


    if (!file) {

        alert(
            'กรุณาเลือกรูปภาพก่อน'
        );

        return;
    }


    loading.style.display =
        'block';

    ocrButton.disabled =
        true;

    resultCard.style.display =
        'none';


    try {

        /*
         * บีบอัดรูปก่อนส่ง
         */
        const compressedFile =
            await compressImage(file);


        /*
         * สร้าง FormData
         */
        const formData =
            new FormData();


        formData.append(
            'image',
            compressedFile,
            'ocr-image.jpg'
        );


        /*
         * ส่งไป PHP
         */
        const response =
            await fetch(
                'ocr.php',
                {
                    method: 'POST',
                    body: formData
                }
            );


        /*
         * อ่าน JSON
         */
        const data =
            await response.json();


        /*
         * ตรวจสอบ Error
         */
        if (!data.success) {

            let message =
                data.error ||
                'OCR ไม่สำเร็จ';


            if (
                data.error_code !==
                undefined
            ) {

                message +=
                    '\nUpload Error Code: ' +
                    data.error_code;
            }


            if (data.curl_error) {

                message +=
                    '\nCURL: ' +
                    data.curl_error;
            }


            throw new Error(
                message
            );
        }


        /*
         * แสดงผล OCR
         */
        ocrResult.value =
            data.text || '';


        resultCard.style.display =
            'block';


        /*
         * ถ้าไม่พบข้อความ
         */
        if (!data.text) {

            alert(
                'OCR ทำงานแล้ว แต่ไม่พบข้อความในรูป'
            );
        }

    }

    catch (error) {

        console.error(error);

        alert(
            error.message
        );

    }

    finally {

        loading.style.display =
            'none';

        ocrButton.disabled =
            false;
    }
}


/* =========================================
   COMPRESS IMAGE
========================================= */

function compressImage(file) {

    return new Promise(
        (resolve, reject) => {

            const reader =
                new FileReader();


            reader.onload =
                function (event) {

                    const img =
                        new Image();


                    img.onload =
                        function () {

                            /*
                             * ขนาดสูงสุด
                             */
                            const maxWidth =
                                1600;

                            const maxHeight =
                                1600;


                            let width =
                                img.width;

                            let height =
                                img.height;


                            /*
                             * ลดความกว้าง
                             */
                            if (
                                width >
                                maxWidth
                            ) {

                                height =
                                    height *
                                    maxWidth /
                                    width;

                                width =
                                    maxWidth;
                            }


                            /*
                             * ลดความสูง
                             */
                            if (
                                height >
                                maxHeight
                            ) {

                                width =
                                    width *
                                    maxHeight /
                                    height;

                                height =
                                    maxHeight;
                            }


                            /*
                             * Canvas
                             */
                            const canvas =
                                document.createElement(
                                    'canvas'
                                );


                            canvas.width =
                                width;

                            canvas.height =
                                height;


                            const ctx =
                                canvas.getContext(
                                    '2d'
                                );


                            ctx.drawImage(
                                img,
                                0,
                                0,
                                width,
                                height
                            );


                            /*
                             * JPEG 80%
                             */
                            canvas.toBlob(
                                function (blob) {

                                    if (!blob) {

                                        reject(
                                            new Error(
                                                'บีบอัดรูปไม่สำเร็จ'
                                            )
                                        );

                                        return;
                                    }


                                    resolve(
                                        blob
                                    );

                                },
                                'image/jpeg',
                                0.80
                            );
                        };


                    img.onerror =
                        function () {

                            reject(
                                new Error(
                                    'ไม่สามารถอ่านรูปภาพได้'
                                )
                            );
                        };


                    img.src =
                        event.target.result;
                };


            reader.onerror =
                function () {

                    reject(
                        new Error(
                            'อ่านไฟล์รูปไม่สำเร็จ'
                        )
                    );
                };


            reader.readAsDataURL(file);
        }
    );
}


/* =========================================
   COPY RESULT
========================================= */

async function copyResult() {

    try {

        await navigator.clipboard.writeText(
            ocrResult.value
        );


        const oldText =
            copyButton.textContent;


        copyButton.textContent =
            '✅ คัดลอกแล้ว';


        setTimeout(
            function () {

                copyButton.textContent =
                    oldText;

            },
            1500
        );


    }

    catch (error) {

        /*
         * สำรองกรณี Browser
         * ไม่อนุญาต Clipboard API
         */

        ocrResult.select();

        document.execCommand(
            'copy'
        );


        alert(
            'คัดลอกแล้ว'
        );
    }
}

</script>

</body>

</html>
