<!DOCTYPE html>
<html lang="th">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ระบบคำนวณค่าแรง V5.2</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>

<div class="container">

    <h1>💰 ระบบคำนวณค่าแรง V5.2</h1>

    <p class="subtitle">
        อัปโหลดรูปเวลาเข้า-ออกงาน แล้วระบบคำนวณให้อัตโนมัติ
    </p>


    <!-- =========================
         UPLOAD
    ========================== -->

    <div class="card">

        <label for="imageInput">
            📷 เลือกรูปเวลาเข้า-ออกงาน
        </label>

        <input
            type="file"
            id="imageInput"
            accept="image/*"
            multiple
        >

        <div
            id="fileList"
            style="margin-top:15px;"
        ></div>

        <button
            id="ocrButton"
            onclick="runAllOCR()"
        >
            🔍 วิเคราะห์รูปทั้งหมด
        </button>

        <div
            id="loading"
            class="loading"
        >
            ⏳ กำลังเตรียมข้อมูล...
        </div>

    </div>


    <!-- =========================
         DAILY RESULTS
    ========================== -->

    <div
        id="summaryCard"
        class="card"
        style="display:none;"
    >

        <h2>📊 สรุปค่าแรง</h2>

        <div id="summary"></div>

    </div>


    <!-- =========================
         OCR RESULTS
    ========================== -->

    <div
        id="resultsCard"
        class="card"
        style="display:none;"
    >

        <h2>📝 ผล OCR แต่ละรูป</h2>

        <div id="results"></div>

    </div>

</div>


<script>

/* =========================================================
   ELEMENTS
========================================================= */

const imageInput =
    document.getElementById('imageInput');

const fileList =
    document.getElementById('fileList');

const ocrButton =
    document.getElementById('ocrButton');

const loading =
    document.getElementById('loading');

const resultsCard =
    document.getElementById('resultsCard');

const results =
    document.getElementById('results');

const summaryCard =
    document.getElementById('summaryCard');

const summary =
    document.getElementById('summary');


/* =========================================================
   SETTINGS
========================================================= */

const NORMAL_DAILY_WAGE = 352;

const OT_HOURLY_WAGE = 66;

const SUNDAY_HOURLY_WAGE = 88;

const NORMAL_START = 8 * 60;      // 08:00

const NORMAL_END = 17 * 60;       // 17:00

const OT_START = 17 * 60 + 30;   // 17:30


/* =========================================================
   FILE LIST
========================================================= */

imageInput.addEventListener(
    'change',
    function () {

        fileList.innerHTML = '';

        const files =
            Array.from(this.files);

        if (!files.length) {
            return;
        }

        files.forEach(
            (file, index) => {

                const div =
                    document.createElement(
                        'div'
                    );

                div.style.padding =
                    '7px 0';

                div.innerHTML =
                    `
                    📷 ${index + 1}.
                    ${escapeHTML(file.name)}
                    <small>
                        (${formatBytes(file.size)})
                    </small>
                    `;

                fileList.appendChild(div);
            }
        );
    }
);


/* =========================================================
   MAIN OCR PROCESS
========================================================= */

async function runAllOCR() {

    const files =
        Array.from(imageInput.files);

    if (!files.length) {

        alert(
            'กรุณาเลือกรูปอย่างน้อย 1 รูป'
        );

        return;
    }

    ocrButton.disabled = true;

    loading.style.display = 'block';

    resultsCard.style.display = 'none';

    summaryCard.style.display = 'none';

    results.innerHTML = '';

    summary.innerHTML = '';

    const records = [];

    try {

        /*
         * OCR ทีละรูป
         */

        for (
            let i = 0;
            i < files.length;
            i++
        ) {

            const file = files[i];

            loading.textContent =
                `⏳ กำลังอ่านรูป ${i + 1} / ${files.length}`;


            /*
             * บีบอัดรูป
             */

            const compressed =
                await compressImage(file);


            /*
             * ส่งไป OCR
             */

            const formData =
                new FormData();

            formData.append(
                'image',
                compressed,
                `ocr-${i + 1}.jpg`
            );


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

                let error =
                    data.error ||
                    'OCR ไม่สำเร็จ';

                if (
                    data.error_code !==
                    undefined
                ) {

                    error +=
                        '\nUpload Error Code: ' +
                        data.error_code;
                }

                if (data.curl_error) {

                    error +=
                        '\nCURL: ' +
                        data.curl_error;
                }

                throw new Error(
                    `รูปที่ ${i + 1}: ${error}`
                );
            }


            const text =
                data.text || '';


            /*
             * ดึงเวลา
             */

            const times =
                extractTimes(text);


            /*
             * ดึงวันที่
             */

            const dateInfo =
                extractDate(text);


            records.push({

                index: i,

                fileName:
                    file.name,

                text:
                    text,

                times:
                    times,

                date:
                    dateInfo.date,

                dateKey:
                    dateInfo.key
            });
        }


        /*
         * แสดง OCR
         */

        showOCRResults(records);


        /*
         * จับคู่รายวัน + คำนวณ
         */

        const days =
            buildDailyRecords(records);


        showSummary(days);

    }

    catch (error) {

        console.error(error);

        alert(
            error.message
        );

    }

    finally {

        loading.style.display = 'none';

        loading.textContent =
            '⏳ กำลังเตรียมข้อมูล...';

        ocrButton.disabled = false;
    }
}


/* =========================================================
   EXTRACT TIMES
========================================================= */

function extractTimes(text) {

    /*
     * รองรับ:
     * 08:00
     * 8:00
     * 08.00
     * 8.00
     */

    const regex =
        /\b([01]?\d|2[0-3])[\.:]([0-5]\d)\b/g;

    const result = [];

    let match;

    while (
        (match = regex.exec(text)) !== null
    ) {

        const hour =
            String(
                parseInt(match[1], 10)
            ).padStart(2, '0');

        const minute =
            match[2];

        result.push(
            `${hour}:${minute}`
        );
    }


    /*
     * ลบเวลาซ้ำ
     */

    return [
        ...new Set(result)
    ];
}


/* =========================================================
   EXTRACT DATE
========================================================= */

function extractDate(text) {

    /*
     * รองรับ:
     *
     * 11/08/2026
     * 11-08-2026
     * 11/08/26
     *
     * รวมถึงวันที่ไม่มีปี
     * 11/08
     */

    const regex =
        /\b(0?[1-9]|[12]\d|3[01])[\-\/](0?[1-9]|1[0-2])(?:[\-\/](\d{2,4}))?\b/;

    const match =
        text.match(regex);


    if (!match) {

        return {
            date: null,
            key: null
        };
    }


    let day =
        parseInt(match[1], 10);

    let month =
        parseInt(match[2], 10);

    let year =
        match[3]
            ? parseInt(match[3], 10)
            : new Date().getFullYear();


    /*
     * ถ้าเป็น พ.ศ.
     */

    if (year < 100) {

        year += 2000;
    }

    if (year > 2400) {

        year -= 543;
    }


    const key =
        `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;


    return {

        date:
            `${String(day).padStart(2, '0')}/${String(month).padStart(2, '0')}/${year}`,

        key:
            key
    };
}


/* =========================================================
   SHOW OCR RESULTS
========================================================= */

function showOCRResults(records) {

    results.innerHTML = '';

    records.forEach(
        (record, index) => {

            const box =
                document.createElement(
                    'div'
                );

            box.style.border =
                '1px solid #ddd';

            box.style.borderRadius =
                '12px';

            box.style.padding =
                '15px';

            box.style.marginBottom =
                '15px';


            const dateText =
                record.date ||
                'ไม่พบวันที่';


            const timeText =
                record.times.length
                    ? record.times.join(', ')
                    : 'ไม่พบเวลา';


            box.innerHTML = `

                <div>
                    <b>
                        📷 รูปที่ ${index + 1}
                    </b>
                </div>

                <div style="margin-top:7px;">
                    📁
                    ${escapeHTML(record.fileName)}
                </div>

                <div style="margin-top:7px;">
                    📅 วันที่:
                    ${escapeHTML(dateText)}
                </div>

                <div style="margin-top:7px;">
                    ⏰ เวลา:
                    ${escapeHTML(timeText)}
                </div>

                <details style="margin-top:10px;">

                    <summary>
                        ดูข้อความ OCR
                    </summary>

                    <textarea
                        style="
                            width:100%;
                            min-height:150px;
                            margin-top:10px;
                        "
                        readonly
                    >${escapeHTML(record.text)}</textarea>

                </details>

            `;

            results.appendChild(box);
        }
    );

    resultsCard.style.display =
        'block';
}


/* =========================================================
   BUILD DAILY RECORDS
========================================================= */

function buildDailyRecords(records) {

    const groups = {};


    /*
     * 1. จัดกลุ่มตามวันที่
     */

    records.forEach(
        record => {

            let key =
                record.dateKey;


            /*
             * ถ้าหาวันที่ไม่ได้
             * ใช้กลุ่ม unknown
             */

            if (!key) {

                key =
                    'unknown-' +
                    record.index;
            }


            if (!groups[key]) {

                groups[key] = [];
            }


            groups[key].push(
                record
            );
        }
    );


    /*
     * 2. สร้างข้อมูลแต่ละวัน
     */

    const days = [];


    Object.keys(groups)
        .sort()
        .forEach(
            key => {

                const group =
                    groups[key];


                /*
                 * รวมเวลาจากทุกรูปของวันนั้น
                 */

                let allTimes = [];


                group.forEach(
                    record => {

                        record.times.forEach(
                            time => {

                                allTimes.push({

                                    time:
                                        time,

                                    fileName:
                                        record.fileName,

                                    recordIndex:
                                        record.index
                                });

                            }
                        );
                    }
                );


                /*
                 * แปลงเป็นนาที
                 */

                allTimes =
                    allTimes.map(
                        item => ({

                            ...item,

                            minutes:
                                timeToMinutes(
                                    item.time
                                )
                        })
                    );


                /*
                 * เรียงตามเวลา
                 */

                allTimes.sort(
                    (a, b) =>
                        a.minutes -
                        b.minutes
                );


                /*
                 * ลบเวลาเหมือนกัน
                 */

                const uniqueTimes = [];

                allTimes.forEach(
                    item => {

                        const exists =
                            uniqueTimes.some(
                                x =>
                                    x.minutes ===
                                    item.minutes
                            );

                        if (!exists) {

                            uniqueTimes.push(
                                item
                            );
                        }
                    }
                );


                /*
                 * หาเวลาเข้าและออก
                 */

                let checkIn = null;

                let checkOut = null;


                if (
                    uniqueTimes.length >= 2
                ) {

                    checkIn =
                        uniqueTimes[0];

                    checkOut =
                        uniqueTimes[
                            uniqueTimes.length - 1
                        ];
                }


                /*
                 * คำนวณ
                 */

                let calculation =
                    null;


                if (
                    checkIn &&
                    checkOut
                ) {

                    calculation =
                        calculateWage(
                            key,
                            checkIn.minutes,
                            checkOut.minutes
                        );
                }


                days.push({

                    dateKey:
                        key,

                    date:
                        getDisplayDate(
                            key
                        ),

                    times:
                        uniqueTimes,

                    checkIn:
                        checkIn,

                    checkOut:
                        checkOut,

                    calculation:
                        calculation,

                    records:
                        group
                });
            }
        );


    return days;
}


/* =========================================================
   CALCULATE WAGE
========================================================= */

function calculateWage(
    dateKey,
    rawCheckIn,
    rawCheckOut
) {

    /*
     * วันในสัปดาห์
     *
     * 0 = อาทิตย์
     * 1 = จันทร์
     * ...
     * 6 = เสาร์
     */

    const date =
        new Date(
            dateKey + 'T12:00:00'
        );


    const dayOfWeek =
        date.getDay();


    const isSunday =
        dayOfWeek === 0;


    /*
     * ปรับเวลาเข้า
     *
     * 07:30 - 08:00
     * ให้เป็น 08:00
     */

    let checkIn =
        rawCheckIn;


    if (
        checkIn >= 450 &&
        checkIn <= 480
    ) {

        checkIn = 480;
    }


    /*
     * ถ้าเลิกก่อน 17:30
     * ไม่มี OT
     */

    let normalMinutes = 0;

    let otMinutes = 0;


    /*
     * เวลาปกติ
     *
     * เริ่มจากเวลาเข้า
     * จนถึง 17:00
     */

    if (
        checkIn < NORMAL_END
    ) {

        normalMinutes =
            NORMAL_END -
            checkIn;

    }


    /*
     * ถ้าเลิกตั้งแต่ 17:30
     * เริ่ม OT
     */

    if (
        rawCheckOut >= OT_START
    ) {

        /*
         * ปัดเวลาลงทุก 30 นาที
         *
         * 20:01 - 20:29
         * → 20:00
         *
         * 20:30 - 20:59
         * → 20:30
         */

        const roundedOut =
            Math.floor(
                rawCheckOut / 30
            ) * 30;


        /*
         * OT เริ่ม 17:30
         */

        otMinutes =
            Math.max(
                0,
                roundedOut -
                OT_START
            );
    }


    /*
     * จันทร์ - เสาร์
     */

    if (!isSunday) {

        const normalPay =
            NORMAL_DAILY_WAGE;


        const otPay =
            (
                otMinutes / 60
            ) *
            OT_HOURLY_WAGE;


        return {

            dayName:
                getThaiDay(dayOfWeek),

            isSunday:
                false,

            adjustedCheckIn:
                minutesToTime(checkIn),

            rawCheckOut:
                minutesToTime(rawCheckOut),

            normalMinutes:
                normalMinutes,

            normalPay:
                normalPay,

            otMinutes:
                otMinutes,

            otPay:
                otPay,

            totalPay:
                normalPay +
                otPay
        };
    }


    /*
     * วันอาทิตย์
     *
     * 88 บาท / ชั่วโมง
     */

    const normalPay =
        (
            normalMinutes / 60
        ) *
        SUNDAY_HOURLY_WAGE;


    const otPay =
        (
            otMinutes / 60
        ) *
        SUNDAY_HOURLY_WAGE;


    return {

        dayName:
            'อาทิตย์',

        isSunday:
            true,

        adjustedCheckIn:
            minutesToTime(checkIn),

        rawCheckOut:
            minutesToTime(rawCheckOut),

        normalMinutes:
            normalMinutes,

        normalPay:
            normalPay,

        otMinutes:
            otMinutes,

        otPay:
            otPay,

        totalPay:
            normalPay +
            otPay
    };
}


/* =========================================================
   SHOW SUMMARY
========================================================= */

function showSummary(days) {

    summary.innerHTML = '';

    let totalPay = 0;

    let totalOT = 0;


    days.forEach(
        day => {

            const box =
                document.createElement(
                    'div'
                );


            box.style.padding =
                '18px';

            box.style.marginBottom =
                '15px';

            box.style.borderRadius =
                '12px';

            box.style.background =
                '#f7f9fc';

            box.style.border =
                '1px solid #e2e6ea';


            /*
             * ไม่มีเวลาเข้า/ออก
             */

            if (
                !day.calculation
            ) {

                box.innerHTML = `

                    <h3>
                        📅
                        ${escapeHTML(day.date)}
                    </h3>

                    <div>
                        ⚠️
                        ไม่พบเวลาเข้าและออกครบ 2 เวลา
                    </div>

                    <div style="margin-top:8px;">
                        เวลาที่พบ:
                        ${
                            day.times.length
                                ? day.times
                                    .map(
                                        x =>
                                            x.time
                                    )
                                    .join(', ')
                                : '-'
                        }
                    </div>

                `;

                summary.appendChild(box);

                return;
            }


            const c =
                day.calculation;


            totalPay +=
                c.totalPay;


            totalOT +=
                c.otMinutes;


            const normalText =
                formatMoney(
                    c.normalPay
                );


            const otText =
                formatMoney(
                    c.otPay
                );


            const totalText =
                formatMoney(
                    c.totalPay
                );


            box.innerHTML = `

                <h3>
                    📅
                    ${escapeHTML(day.date)}
                    <small>
                        (${c.dayName})
                    </small>
                </h3>


                <div style="margin-top:10px;">

                    🟢 เวลาเข้า:
                    <b>
                        ${c.adjustedCheckIn}
                    </b>

                </div>


                <div style="margin-top:7px;">

                    🔴 เวลาออก:
                    <b>
                        ${c.rawCheckOut}
                    </b>

                </div>


                <hr>


                <div>

                    💵 ค่าแรงปกติ:
                    <b>
                        ฿${normalText}
                    </b>

                </div>


                <div style="margin-top:7px;">

                    ⏱️ OT:

                    ${
                        c.otMinutes > 0
                            ? formatDuration(
                                c.otMinutes
                            )
                            : 'ไม่มี'
                    }

                    ${
                        c.otMinutes > 0
                            ? `
                                → ฿${otText}
                              `
                            : ''
                    }

                </div>


                <div
                    style="
                        margin-top:15px;
                        padding-top:12px;
                        border-top:
                        1px solid #ddd;
                        font-size:20px;
                        font-weight:bold;
                    "
                >

                    💰 ค่าแรงวันนี้:
                    ฿${totalText}

                </div>

            `;


            summary.appendChild(box);
        }
    );


    /*
     * ยอดรวม
     */

    const totalBox =
        document.createElement(
            'div'
        );


    totalBox.style.padding =
        '20px';

    totalBox.style.marginTop =
        '20px';

    totalBox.style.borderRadius =
        '15px';

    totalBox.style.background =
        '#eaf3ff';


    totalBox.innerHTML = `

        <h2>
            💰 รวมทั้งหมด
        </h2>

        <div>
            ⏱️ OT รวม:
            <b>
                ${formatDuration(totalOT)}
            </b>
        </div>

        <div
            style="
                margin-top:10px;
                font-size:28px;
                font-weight:bold;
            "
        >
            ฿${formatMoney(totalPay)}
        </div>

    `;


    summary.appendChild(totalBox);


    summaryCard.style.display =
        'block';
}


/* =========================================================
   TIME FUNCTIONS
========================================================= */

function timeToMinutes(time) {

    const parts =
        time.split(':');


    return (
        parseInt(parts[0], 10) *
        60
        +
        parseInt(parts[1], 10)
    );
}


function minutesToTime(minutes) {

    minutes =
        Math.max(
            0,
            Math.min(
                1439,
                minutes
            )
        );


    const hour =
        Math.floor(
            minutes / 60
        );


    const minute =
        minutes % 60;


    return (
        String(hour).padStart(2, '0') +
        ':' +
        String(minute).padStart(2, '0')
    );
}


/* =========================================================
   DATE FUNCTIONS
========================================================= */

function getDisplayDate(key) {

    if (
        !key ||
        key.startsWith('unknown-')
    ) {

        return 'ไม่ทราบวันที่';
    }


    const parts =
        key.split('-');


    return (
        parts[2] +
        '/' +
        parts[1] +
        '/' +
        parts[0]
    );
}


function getThaiDay(day) {

    const names = [

        'อาทิตย์',
        'จันทร์',
        'อังคาร',
        'พุธ',
        'พฤหัสบดี',
        'ศุกร์',
        'เสาร์'

    ];


    return names[day];
}


/* =========================================================
   FORMAT
========================================================= */

function formatMoney(value) {

    return Number(value)
        .toLocaleString(
            'th-TH',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
}


function formatDuration(minutes) {

    const hours =
        Math.floor(
            minutes / 60
        );


    const mins =
        minutes % 60;


    if (
        hours === 0
    ) {

        return `${mins} นาที`;
    }


    if (
        mins === 0
    ) {

        return `${hours} ชั่วโมง`;
    }


    return (
        `${hours} ชั่วโมง ` +
        `${mins} นาที`
    );
}


/* =========================================================
   COMPRESS IMAGE
========================================================= */

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

                            const maxWidth =
                                1600;

                            const maxHeight =
                                1600;


                            let width =
                                img.width;

                            let height =
                                img.height;


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


/* =========================================================
   UTILS
========================================================= */

function formatBytes(bytes) {

    if (!bytes) {
        return '0 Bytes';
    }


    const units = [
        'Bytes',
        'KB',
        'MB',
        'GB'
    ];


    const i =
        Math.floor(
            Math.log(bytes) /
            Math.log(1024)
        );


    return (
        parseFloat(
            (
                bytes /
                Math.pow(1024, i)
            ).toFixed(2)
        )
        +
        ' '
        +
        units[i]
    );
}


function escapeHTML(value) {

    return String(value)
        .replace(
            /&/g,
            '&amp;'
        )
        .replace(
            /</g,
            '&lt;'
        )
        .replace(
            />/g,
            '&gt;'
        )
        .replace(
            /"/g,
            '&quot;'
        )
        .replace(
            /'/g,
            '&#039;'
        );
}

</script>

</body>

</html>
