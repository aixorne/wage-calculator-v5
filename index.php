<!DOCTYPE html>
<html lang="th">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Wage Calculator V6</title>

<link
    rel="stylesheet"
    href="style.css"
>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

</head>


<body>

<div class="app">


<!-- =====================================================
     HERO
===================================================== -->

<section class="hero">

    <h1>
        💰 ระบบคำนวณค่าแรง
    </h1>

    <p>
        OCR • ค่าแรง • OT • ปฏิทิน • รอบจ่าย
    </p>

    <span class="version">
        WAGE CALCULATOR V6
    </span>

</section>


<!-- =====================================================
     SUMMARY
===================================================== -->

<section class="card">

    <div class="card-title">
        <span>📊</span>
        <h2>ภาพรวม</h2>
    </div>

    <div class="summary-grid">

        <div class="summary">

            <div class="summary-label">
                วันที่บันทึก
            </div>

            <div
                class="summary-value"
                id="summaryDays"
            >
                0
            </div>

        </div>


        <div class="summary">

            <div class="summary-label">
                วันทำงาน
            </div>

            <div
                class="summary-value"
                id="summaryWorked"
            >
                0
            </div>

        </div>


        <div class="summary">

            <div class="summary-label">
                ค่าแรงสะสม
            </div>

            <div
                class="summary-value"
                id="summaryMoney"
            >
                ฿0
            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     UPLOAD
===================================================== -->

<section class="card">

    <div class="card-title">
        <span>📷</span>
        <h2>อัปโหลดเวลา</h2>
    </div>


    <div
        class="upload-zone"
        id="uploadZone"
    >

        <div class="upload-icon">
            📸
        </div>

        <div class="upload-title">
            เลือกรูปเวลาเข้า–ออกงาน
        </div>

        <div class="upload-sub">
            แตะเพื่อเลือกหลายรูป หรือวางรูปลงตรงนี้
        </div>


        <input
            id="imageInput"
            class="file-input"
            type="file"
            accept="image/*"
            multiple
        >

    </div>


    <div
        id="previewGrid"
        class="preview-grid"
    ></div>


    <button
        id="ocrButton"
        class="primary-btn"
        onclick="runOCR()"
        disabled
    >
        🔍 วิเคราะห์รูปทั้งหมด
    </button>


    <div
        id="loading"
        class="loading"
    >
        ยังไม่ได้เลือกรูป
    </div>

</section>


<!-- =====================================================
     CALENDAR
===================================================== -->

<section class="card">

    <div class="card-title">
        <span>📅</span>
        <h2>ปฏิทินค่าแรง</h2>
    </div>


    <div class="calendar-top">

        <button
            class="month-btn"
            onclick="changeMonth(-1)"
        >
            ‹
        </button>


        <div
            id="monthTitle"
            class="month-title"
        ></div>


        <button
            class="month-btn"
            onclick="changeMonth(1)"
        >
            ›
        </button>

    </div>


    <div
        id="calendar"
        class="calendar"
    ></div>


    <div class="legend">

        <div class="legend-item">
            <span class="dot blue"></span>
            ยังไม่ถึง
        </div>

        <div class="legend-item">
            <span class="dot green"></span>
            อัปแล้ว
        </div>

        <div class="legend-item">
            <span class="dot orange"></span>
            ลืมอัป
        </div>

        <div class="legend-item">
            <span class="dot red"></span>
            วันหยุด
        </div>

    </div>

</section>


<!-- =====================================================
     PAY PERIOD
===================================================== -->

<section class="card">

    <div class="card-title">
        <span>💵</span>
        <h2>รอบจ่าย</h2>
    </div>


    <div id="periods">

        <div class="empty-state">
            ยังไม่มีข้อมูลรอบจ่าย
        </div>

    </div>

</section>

    <!-- =====================================================
     SALARY SLIP
===================================================== -->

<section class="card salary-slip-card">

    <div class="card-title">
        <span>🧾</span>
        <h2>สลิปเงินเดือน</h2>
    </div>

    <div class="salary-slip-box">

        <div class="slip-info">

            <div class="slip-field">
                <label>ชื่อพนักงาน</label>

                <input
                    id="employeeName"
                    type="text"
                    value="นายจิรวัฒน์ กาญจนบุรางกูร"
                >
            </div>

            <div class="slip-field">
                <label>รหัสพนักงาน</label>

                <input
                    id="employeeId"
                    type="text"
                    value="14187"
                >
            </div>

        </div>


        <div class="slip-field">

            <label>เลือกรอบจ่าย</label>

            <select id="salaryPeriod">
                <option value="">
                    -- เลือกรอบจ่าย --
                </option>
            </select>

        </div>


        <div class="slip-field">

            <label>ค่าห้องเย็น</label>

            <div class="money-input">

                <input
                    id="roomAllowance"
                    type="number"
                    value="120"
                    min="0"
                    step="0.01"
                >

                <span>บาท</span>

            </div>

        </div>


        <div class="slip-field">

            <label>ประกันสังคม</label>

            <div class="money-input">

                <input
                    id="socialSecurityRate"
                    type="number"
                    value="3"
                    min="0"
                    max="100"
                    step="0.1"
                >

                <span>%</span>

            </div>

        </div>


        <button
            class="salary-preview-btn"
            onclick="previewSalarySlip()"
        >
            👁️ ดูตัวอย่างสลิป
        </button>


        <button
            class="salary-pdf-btn"
            onclick="generateSalaryPDF()"
        >
            📄 สร้าง PDF สลิปเงินเดือน
        </button>

    </div>

</section>

<!-- =====================================================
     DATA
===================================================== -->

<section class="card">

    <div class="card-title">
        <span>💾</span>
        <h2>จัดการข้อมูล</h2>
    </div>


    <p
        style="
            color:#778096;
            font-size:13px;
            line-height:1.7;
        "
    >
        ข้อมูลค่าแรงถูกเก็บไว้ใน LocalStorage
        ของเบราว์เซอร์เครื่องนี้
        การเพิ่มข้อมูลวันใหม่จะไม่ลบข้อมูลวันเก่า
    </p>


    <div class="delete-day-box">

        <label for="deleteDate">เลือกวันที่ที่ต้องการลบ</label>

        <div class="delete-day-row">
            <input
                id="deleteDate"
                type="date"
                class="date-delete-input"
            >

            <button
                class="danger-btn"
                onclick="deleteSelectedDate()"
            >
                🗑️ ลบข้อมูลวันนี้
            </button>
        </div>

        <div class="delete-day-hint">
            ลบเฉพาะกะที่เริ่มในวันที่เลือกเท่านั้น ข้อมูลวันอื่นจะไม่ถูกลบ
        </div>

    </div>

</section>


</div>


<!-- =====================================================
     MODAL
===================================================== -->

<div
    id="dayModal"
    class="modal"
    onclick="closeModalOutside(event)"
>

    <div class="modal-box">

        <div class="modal-header">

            <h3 id="modalTitle">
                รายละเอียด
            </h3>

            <button
                class="modal-close"
                onclick="closeModal()"
            >
                ×
            </button>

        </div>


        <div
            id="modalBody"
            class="modal-body"
        ></div>

    </div>

</div>


<script>

async function loadThaiFont(doc){

    const response =
        await fetch(
            'NotoSansThai-Regular.ttf'
        );

    if(!response.ok){
        throw new Error(
            'ไม่พบไฟล์ฟอนต์ภาษาไทย'
        );
    }

    const buffer =
        await response.arrayBuffer();

    const bytes =
        new Uint8Array(buffer);

    let binary = '';

    const chunkSize = 0x8000;

    for(
        let i = 0;
        i < bytes.length;
        i += chunkSize
    ){

        binary += String.fromCharCode(
            ...bytes.subarray(
                i,
                Math.min(
                    i + chunkSize,
                    bytes.length
                )
            )
        );

    }

    const base64 =
        btoa(binary);

    doc.addFileToVFS(
        'NotoSansThai-Regular.ttf',
        base64
    );

    doc.addFont(
        'NotoSansThai-Regular.ttf',
        'NotoSansThai',
        'normal'
    );

    doc.setFont(
        'NotoSansThai',
        'normal'
    );
}

/* =====================================================
   CONFIG
===================================================== */

const STORAGE_KEY =
    'wage_calculator_v6_data';


const NORMAL_WAGE =
    352;


const OT_RATE =
    66;


const SUNDAY_RATE =
    88;


const DAY_START =
    8 * 60;


const NORMAL_END =
    17 * 60;


const OT_START =
    17 * 60 + 30;


/* =====================================================
   STATE
===================================================== */

let shifts = [];

let selectedFiles = [];

let viewDate = new Date();


/* =====================================================
   DOM
===================================================== */

const imageInput =
    document.getElementById(
        'imageInput'
    );


const uploadZone =
    document.getElementById(
        'uploadZone'
    );


const previewGrid =
    document.getElementById(
        'previewGrid'
    );


const ocrButton =
    document.getElementById(
        'ocrButton'
    );


const loading =
    document.getElementById(
        'loading'
    );


const calendar =
    document.getElementById(
        'calendar'
    );


const monthTitle =
    document.getElementById(
        'monthTitle'
    );


const periods =
    document.getElementById(
        'periods'
    );


const dayModal =
    document.getElementById(
        'dayModal'
    );


const modalTitle =
    document.getElementById(
        'modalTitle'
    );


const modalBody =
    document.getElementById(
        'modalBody'
    );


/* =====================================================
   LOAD
===================================================== */

loadData();


/* =====================================================
   FILE INPUT
===================================================== */

imageInput.addEventListener(
    'change',
    function(){

        addFiles(
            Array.from(
                this.files
            )
        );

    }
);


/* =====================================================
   DRAG DROP
===================================================== */

[
    'dragenter',
    'dragover'
].forEach(
    eventName => {

        uploadZone.addEventListener(
            eventName,
            e => {

                e.preventDefault();

                uploadZone.classList.add(
                    'dragover'
                );

            }
        );

    }
);


[
    'dragleave',
    'drop'
].forEach(
    eventName => {

        uploadZone.addEventListener(
            eventName,
            e => {

                e.preventDefault();

                uploadZone.classList.remove(
                    'dragover'
                );

            }
        );

    }
);


uploadZone.addEventListener(
    'drop',
    e => {

        addFiles(
            Array.from(
                e.dataTransfer.files
            ).filter(
                file =>
                    file.type.startsWith(
                        'image/'
                    )
            )
        );

    }
);


/* =====================================================
   ADD FILES
===================================================== */

function addFiles(files){

    selectedFiles = [
        ...selectedFiles,
        ...files
    ];

    /*
     * ป้องกันชื่อซ้ำ
     */

    const map =
        new Map();


    selectedFiles.forEach(
        file => {

            const key =
                file.name +
                file.size;

            map.set(
                key,
                file
            );

        }
    );


    selectedFiles =
        Array.from(
            map.values()
        );


    renderPreviews();
}


/* =====================================================
   PREVIEW
===================================================== */

function renderPreviews(){

    previewGrid.innerHTML =
        '';


    selectedFiles.forEach(
        (file,index)=>{

            const item =
                document.createElement(
                    'div'
                );


            item.className =
                'preview-item';


            const url =
                URL.createObjectURL(
                    file
                );


            item.innerHTML = `

                <img
                    src="${url}"
                    alt=""
                >

                <button
                    class="remove-file"
                    onclick="removeFile(${index})"
                >
                    ×
                </button>

                <div class="preview-name">
                    ${escapeHTML(
                        file.name
                    )}
                </div>

            `;


            previewGrid.appendChild(
                item
            );

        }
    );


    ocrButton.disabled =
        selectedFiles.length === 0;


    loading.textContent =
        selectedFiles.length
        ? `เลือกรูปแล้ว ${selectedFiles.length} รูป`
        : 'ยังไม่ได้เลือกรูป';
}


/* =====================================================
   REMOVE
===================================================== */

function removeFile(index){

    selectedFiles.splice(
        index,
        1
    );

    renderPreviews();
}


/* =====================================================
   OCR
===================================================== */

async function runOCR(){

    if(
        !selectedFiles.length
    ){

        return;
    }


    ocrButton.disabled =
        true;


    const records = [];


    try{

        for(
            let i=0;
            i<selectedFiles.length;
            i++
        ){

            const file =
                selectedFiles[i];


            loading.textContent =
                `🔍 กำลังอ่านรูป ${i+1}/${selectedFiles.length}`;


            const compressed =
                await compressImage(
                    file
                );


            const form =
                new FormData();


            form.append(
                'image',
                compressed,
                file.name
            );


            const response =
                await fetch(
                    'ocr.php',
                    {
                        method:'POST',
                        body:form
                    }
                );


            const data =
                await response.json();


            if(
                !data.success
            ){

                throw new Error(
                    data.error ||
                    'OCR ไม่สำเร็จ'
                );
            }


            const text =
                data.text || '';


            const parsed =
                parseOCR(
                    text
                );


            records.push({

                fileName:
                    file.name,

                text,

                date:
                    parsed.date,

                times:
                    parsed.times

            });

        }


        const newShifts =
            createShifts(
                records
            );


        mergeShifts(
            newShifts
        );


        selectedFiles =
            [];


        imageInput.value =
            '';


        renderPreviews();


        loading.textContent =
            '✅ บันทึกข้อมูลเรียบร้อย';


        renderAll();


    }catch(error){

        console.error(
            error
        );


        loading.textContent =
            '❌ ' +
            error.message;

        alert(
            error.message
        );

    }finally{

        ocrButton.disabled =
            selectedFiles.length === 0;

    }
}


/* =====================================================
   OCR PARSER
===================================================== */

function parseOCR(text){

    return {

        date:
            extractDate(
                text
            ),

        times:
            extractTimes(
                text
            )

    };
}


/* =====================================================
   DATE
===================================================== */

function extractDate(text){

    let m;


    /*
     * YYYY-MM-DD
     */

    m =
        text.match(
            /\b(20\d{2})[-\/](\d{1,2})[-\/](\d{1,2})\b/
        );


    if(m){

        let year =
            Number(
                m[1]
            );


        let month =
            Number(
                m[2]
            );


        let day =
            Number(
                m[3]
            );


        if(
            year > 2400
        ){

            year -= 543;
        }


        return dateObject(
            year,
            month,
            day
        );
    }


    /*
     * DD/MM/YYYY
     */

    m =
        text.match(
            /\b(\d{1,2})[-\/](\d{1,2})[-\/](\d{2,4})\b/
        );


    if(m){

        const day =
            Number(
                m[1]
            );


        const month =
            Number(
                m[2]
            );


        let year =
            Number(
                m[3]
            );


        if(
            year < 100
        ){

            year += 2000;
        }


        if(
            year > 2400
        ){

            year -= 543;
        }


        return dateObject(
            year,
            month,
            day
        );
    }


    /*
     * YYYY MM DD
     */

    m =
        text.match(
            /\b(20\d{2})\s+(\d{1,2})\s+(\d{1,2})\b/
        );


    if(m){

        return dateObject(
            Number(m[1]),
            Number(m[2]),
            Number(m[3])
        );
    }


    return null;
}


/* =====================================================
   TIME
===================================================== */

function extractTimes(text){

    const found = [];


    const regex =
        /\b([01]?\d|2[0-3])[\.:]([0-5]\d)\b/g;


    let m;


    while(
        (m = regex.exec(text)) !== null
    ){

        const h =
            String(
                Number(
                    m[1]
                )
            ).padStart(
                2,
                '0'
            );


        const time =
            `${h}:${m[2]}`;


        if(
            !found.includes(
                time
            )
        ){

            found.push(
                time
            );
        }

    }


    return found;
}


/* =====================================================
   CREATE SHIFTS
===================================================== */

function createShifts(
    records
){

    const events = [];


    records.forEach(
        record => {

            if(
                !record.date
            ){

                return;
            }


            record.times.forEach(
                time => {

                    const minutes =
                        toMinutes(
                            time
                        );


                    events.push({

                        dateKey:
                            record.date.key,

                        date:
                            record.date,

                        time,

                        minutes,

                        timestamp:
                            timestamp(
                                record.date.key,
                                time
                            )

                    });

                }
            );

        }
    );


    events.sort(
        (a,b)=>
            a.timestamp -
            b.timestamp
    );


    const result = [];


    const used =
        new Set();


    for(
        let i=0;
        i<events.length;
        i++
    ){

        if(
            used.has(i)
        ){

            continue;
        }


        const start =
            events[i];


        let pairIndex =
            -1;


        for(
            let j=i+1;
            j<events.length;
            j++
        ){

            if(
                used.has(j)
            ){

                continue;
            }


            const end =
                events[j];


            const diff =
                (
                    end.timestamp -
                    start.timestamp
                ) / 60000;


            if(
                diff <= 0
            ){

                continue;
            }


            if(
                diff >
                16 * 60
            ){

                break;
            }


            if(
                validPair(
                    start,
                    end
                )
            ){

                pairIndex =
                    j;

                break;
            }

        }


        if(
            pairIndex !== -1
        ){

            const end =
                events[
                    pairIndex
                ];


            used.add(
                i
            );


            used.add(
                pairIndex
            );


            result.push(
                buildShift(
                    start,
                    end
                )
            );

        }else{

            /*
             * ถ้าไม่มีเวลาออก
             */

            used.add(
                i
            );


            result.push({

                id:
                    'incomplete_' +
                    start.timestamp,

                dateKey:
                    start.dateKey,

                startDateKey:
                    start.dateKey,

                endDateKey:
                    null,

                startTime:
                    start.time,

                endTime:
                    null,

                incomplete:true,

                pay:0,

                isSunday:
                    getDay(
                        start.dateKey
                    ) === 0,

                isNight:
                    start.minutes >=
                    19 * 60,

                normalHours:0,

                otHours:0,

                normalPay:0,

                otPay:0

            });

        }

    }


    return result;
}


/* =====================================================
   VALID PAIR
===================================================== */

function validPair(
    start,
    end
){

    const day =
        getDay(
            start.dateKey
        );


    const s =
        start.minutes;


    const e =
        end.minutes;


    /*
     * Sunday
     *
     * สามารถทำงานช่วงใดก็ได้
     * แต่ต้องไม่เกิน 16 ชั่วโมง
     */

    if(
        day === 0
    ){

        return (
            e > s ||
            end.dateKey !==
            start.dateKey
        );
    }


    /*
     * Night shift
     *
     * 19:00 เป็นต้นไป
     * สามารถออกวันถัดไปได้
     */

    if(
        s >= 19 * 60
    ){

        if(
            end.dateKey !==
            start.dateKey
        ){

            return (
                e <=
                12 * 60
            );
        }

        return (
            e > s
        );
    }


    /*
     * Day shift
     */

    if(
        s >=
        7 * 60 + 30
        &&
        s <=
        12 * 60
    ){

        return (
            start.dateKey ===
            end.dateKey
            &&
            e >=
            16 * 60
        );
    }


    return false;
}


/* =====================================================
   BUILD SHIFT
===================================================== */

function buildShift(
    start,
    end
){

    const day =
        getDay(
            start.dateKey
        );


    if(
        day === 0
    ){

        return calculateSunday(
            start,
            end
        );
    }


    if(
        start.minutes >=
        19 * 60
    ){

        return calculateNight(
            start,
            end
        );
    }


    return calculateDay(
        start,
        end
    );
}


/* =====================================================
   SUNDAY
===================================================== */

function calculateSunday(
    start,
    end
){

    let s =
        start.minutes;


    let e =
        end.minutes;


    /*
     * 16:53 -> 17:00
     */

    if(
        s >=
        16 * 60 + 30
        &&
        s <=
        17 * 60
    ){

        s =
            17 * 60;
    }


    /*
     * ปัดเวลาออกลง 30 นาที
     */

    e =
        Math.floor(
            e / 30
        ) * 30;


    /*
     * ข้ามวัน
     */

    if(
        end.dateKey !==
        start.dateKey
        &&
        e < s
    ){

        e +=
            24 * 60;
    }


    const minutes =
        Math.max(
            0,
            e - s
        );


    const pay =
        (
            minutes / 60
        ) *
        SUNDAY_RATE;


    return {

        id:
            makeId(
                start,
                end
            ),

        dateKey:
            start.dateKey,

        startDateKey:
            start.dateKey,

        endDateKey:
            end.dateKey,

        startTime:
            start.time,

        endTime:
            end.time,

        incomplete:false,

        isSunday:true,

        isNight:false,

        normalHours:
            minutes / 60,

        otHours:0,

        normalPay:
            pay,

        otPay:0,

        pay

    };
}


/* =====================================================
   DAY
===================================================== */

function calculateDay(
    start,
    end
){

    let s =
        start.minutes;


    /*
     * 07:30 - 08:00
     * = 08:00
     */

    if(
        s >=
        7 * 60 + 30
        &&
        s <=
        8 * 60
    ){

        s =
            8 * 60;
    }


    /*
     * ค่าแรงปกติ = 352
     */

    const normalPay =
        NORMAL_WAGE;


    /*
     * OT
     *
     * เริ่ม 17:30
     */

    let otMinutes =
        0;


    if(
        end.minutes >=
        OT_START
    ){

        const roundedEnd =
            Math.floor(
                end.minutes / 30
            ) * 30;


        otMinutes =
            Math.max(
                0,
                roundedEnd -
                OT_START
            );
    }


    const otPay =
        (
            otMinutes / 60
        ) *
        OT_RATE;


    return {

        id:
            makeId(
                start,
                end
            ),

        dateKey:
            start.dateKey,

        startDateKey:
            start.dateKey,

        endDateKey:
            end.dateKey,

        startTime:
            start.time,

        endTime:
            end.time,

        incomplete:false,

        isSunday:false,

        isNight:false,

        normalHours:8,

        otHours:
            otMinutes / 60,

        normalPay,

        otPay,

        pay:
            normalPay +
            otPay

    };
}


/* =====================================================
   NIGHT
===================================================== */

function calculateNight(
    start,
    end
){

    let s =
        start.minutes;


    let e =
        end.minutes;


    /*
     * 19:30 - 20:00
     * = 20:00
     */

    if(
        s >=
        19 * 60 + 30
        &&
        s <=
        20 * 60
    ){

        s =
            20 * 60;
    }


    /*
     * ออกวันถัดไป
     */

    if(
        end.dateKey !==
        start.dateKey
        &&
        e <
        s
    ){

        e +=
            24 * 60;
    }


    /*
     * กะปกติคิด 8 ชั่วโมง
     *
     * OT เริ่ม 05:30
     */

    const nightNormalEnd =
        5 * 60;


    let normalEnd =
        nightNormalEnd +
        24 * 60;


    /*
     * ปกติ 20:00 -> 05:00
     */

    let normalMinutes =
        Math.max(
            0,
            Math.min(
                e,
                normalEnd
            ) -
            s
        );


    /*
     * OT 05:30 เป็นต้นไป
     */

    const otStart =
        5 * 60 + 30 +
        24 * 60;


    let otMinutes =
        0;


    if(
        e >= otStart
    ){

        const roundedEnd =
            Math.floor(
                e / 30
            ) * 30;


        otMinutes =
            Math.max(
                0,
                roundedEnd -
                otStart
            );
    }


    /*
     * กะดึกปกติไม่เกิน 8 ชั่วโมง
     */

    normalMinutes =
        Math.min(
            normalMinutes,
            8 * 60
        );


    const normalPay =
        NORMAL_WAGE;


    const otPay =
        (
            otMinutes / 60
        ) *
        OT_RATE;


    return {

        id:
            makeId(
                start,
                end
            ),

        dateKey:
            start.dateKey,

        startDateKey:
            start.dateKey,

        endDateKey:
            end.dateKey,

        startTime:
            start.time,

        endTime:
            end.time,

        incomplete:false,

        isSunday:false,

        isNight:true,

        normalHours:
            normalMinutes / 60,

        otHours:
            otMinutes / 60,

        normalPay,

        otPay,

        pay:
            normalPay +
            otPay

    };
}


/* =====================================================
   MERGE
===================================================== */

function mergeShifts(
    newShifts
){

    const map =
        new Map();


    /*
     * เก่าก่อน
     */

    shifts.forEach(
        shift => {

            map.set(
                shift.id,
                shift
            );

        }
    );


    /*
     * ใหม่ทับเฉพาะ ID เดียวกัน
     */

    newShifts.forEach(
        shift => {

            map.set(
                shift.id,
                shift
            );

        }
    );


    shifts =
        Array.from(
            map.values()
        );


    shifts.sort(
        (a,b)=>
            `${a.dateKey} ${a.startTime}`
            .localeCompare(
                `${b.dateKey} ${b.startTime}`
            )
    );


    saveData();
}


/* =====================================================
   LOCAL STORAGE
===================================================== */

function saveData(){

    localStorage.setItem(
        STORAGE_KEY,
        JSON.stringify({
            version:6,
            updatedAt:
                new Date().toISOString(),
            shifts
        })
    );
}


function loadData(){

    try{

        const raw =
            localStorage.getItem(
                STORAGE_KEY
            );


        if(
            !raw
        ){

            shifts =
                [];

            renderAll();

            return;
        }


        const data =
            JSON.parse(
                raw
            );


        shifts =
            Array.isArray(
                data.shifts
            )
            ? data.shifts
            : [];


        renderAll();

    }catch(error){

        console.error(
            error
        );

        shifts =
            [];

        renderAll();
    }
}


/* =====================================================
   RENDER ALL
===================================================== */

function renderAll(){

    renderSummary();

    renderCalendar();

    renderPeriods();

    renderSalaryPeriods();
}


/* =====================================================
   SUMMARY
===================================================== */

function renderSummary(){

    const completed =
        shifts.filter(
            s =>
                !s.incomplete
        );


    const dates =
        new Set(
            completed.map(
                s =>
                    s.dateKey
            )
        );


    const total =
        completed.reduce(
            (sum,s)=>
                sum +
                Number(
                    s.pay || 0
                ),
            0
        );


    document.getElementById(
        'summaryDays'
    ).textContent =
        dates.size;


    document.getElementById(
        'summaryWorked'
    ).textContent =
        completed.length;


    document.getElementById(
        'summaryMoney'
    ).textContent =
        money(
            total
        );
}


/* =====================================================
   CALENDAR
===================================================== */

function renderCalendar(){

    const year =
        viewDate.getFullYear();


    const month =
        viewDate.getMonth();


    monthTitle.textContent =
        `${thaiMonth(month)} ${year + 543}`;


    calendar.innerHTML =
        '';


    const weekdays = [
        'อา',
        'จ',
        'อ',
        'พ',
        'พฤ',
        'ศ',
        'ส'
    ];


    weekdays.forEach(
        name => {

            const div =
                document.createElement(
                    'div'
                );


            div.className =
                'weekday';


            div.textContent =
                name;


            calendar.appendChild(
                div
            );

        }
    );


    const first =
        new Date(
            year,
            month,
            1
        ).getDay();


    const max =
        new Date(
            year,
            month + 1,
            0
        ).getDate();


    for(
        let i=0;
        i<first;
        i++
    ){

        const empty =
            document.createElement(
                'div'
            );


        empty.className =
            'day empty';


        calendar.appendChild(
            empty
        );

    }


    for(
        let day=1;
        day<=max;
        day++
    ){

        const key =
            `${year}-${pad(month+1)}-${pad(day)}`;


        const cell =
            document.createElement(
                'div'
            );


        const shift =
            getShiftForDate(
                key
            );


        const status =
            getDayStatus(
                key,
                shift
            );


        cell.className =
            `day ${status.className}`;


        let html = `

            <div class="day-number">
                ${day}
            </div>

        `;


        if(
            status.label
        ){

            html += `

                <div class="day-status">
                    ${status.label}
                </div>

            `;

        }


        if(
            shift
            &&
            !shift.incomplete
        ){

            html += `

                <div class="day-time">

                    ${shift.startTime}
                    –
                    ${shift.endTime}

                </div>

                <div class="day-money">

                    ${money(
                        shift.pay
                    )}

                </div>

            `;

        }


        cell.innerHTML =
            html;


        cell.onclick =
            () =>
                openDay(
                    key
                );


        calendar.appendChild(
            cell
        );

    }
}


/* =====================================================
   DAY STATUS
===================================================== */

function getDayStatus(
    key,
    shift
){

    const today =
        startOfDay(
            new Date()
        );


    const date =
        parseDateKey(
            key
        );


    /*
     * Future
     */

    if(
        date > today
    ){

        return {

            className:
                'future',

            label:
                'ยังไม่ถึง'

        };
    }


    /*
     * Worked
     */

    if(
        shift
    ){

        if(
            shift.incomplete
        ){

            return {

                className:
                    'missed',

                label:
                    'ลืมอัป'

            };
        }


        return {

            className:
                'worked',

            label:
                'อัปแล้ว'

        };
    }


    /*
     * Sunday = ไม่ทำงาน
     *
     * แต่ไม่ให้ถือเป็นวันหยุด
     * เพราะอาทิตย์สามารถทำงานได้
     *
     * หากไม่มีข้อมูล
     * ถือเป็นลืมอัป
     */

    return {

        className:
            'missed',

        label:
            'ลืมอัป'

    };
}


/* =====================================================
   GET SHIFT
===================================================== */

function getShiftForDate(
    key
){

    /*
     * ใช้กะที่เริ่มวันนี้
     *
     * กะดึก 26 -> 27
     * จะแสดงที่วันที่ 26
     */

    return shifts.find(
        shift =>
            shift.dateKey === key
    );
}


/* =====================================================
   OPEN DAY
===================================================== */

function openDay(
    key
){

    const shift =
        getShiftForDate(
            key
        );


    const period =
        getPayPeriod(
            key
        );


    modalTitle.textContent =
        `${formatThaiDate(key)}`;


    if(
        !shift
    ){

        modalBody.innerHTML = `

            <div
                style="
                    text-align:center;
                    padding:25px 5px;
                "
            >

                <div
                    style="
                        font-size:42px;
                    "
                >
                    ${
                        getDayStatus(
                            key,
                            null
                        ).className ===
                        'future'
                        ? '🔵'
                        : '🟠'
                    }
                </div>


                <h3>
                    ${
                        getDayStatus(
                            key,
                            null
                        ).label
                    }
                </h3>


                <p
                    style="
                        color:#778096;
                    "
                >
                    ยังไม่มีข้อมูลเวลาเข้า–ออก
                </p>

            </div>

        `;

        dayModal.classList.add(
            'show'
        );

        return;
    }


    if(
        shift.incomplete
    ){

        modalBody.innerHTML = `

            <div class="modal-row">

                <span class="modal-label">
                    รอบจ่าย
                </span>

                <span class="modal-value">
                    ${period.name}
                </span>

            </div>


            <div class="modal-row">

                <span class="modal-label">
                    เวลาเข้า
                </span>

                <span class="modal-value">
                    ${shift.startTime}
                </span>

            </div>


            <div
                style="
                    margin-top:15px;
                    padding:15px;
                    border-radius:14px;
                    background:#fff7ed;
                    color:#c2410c;
                    text-align:center;
                "
            >
                ⚠️ ยังไม่พบเวลาออกงาน
            </div>

        `;

        dayModal.classList.add(
            'show'
        );

        return;
    }


    modalBody.innerHTML = `

        <div class="modal-row">

            <span class="modal-label">
                รอบจ่าย
            </span>

            <span class="modal-value">
                ${period.name}
            </span>

        </div>


        <div class="modal-row">

            <span class="modal-label">
                วันที่
            </span>

            <span class="modal-value">
                ${formatThaiDate(key)}
            </span>

        </div>


        <div class="modal-row">

            <span class="modal-label">
                เวลาทำงาน
            </span>

            <span class="modal-value">
                ${shift.startTime}
                –
                ${shift.endTime}
            </span>

        </div>


        <div class="modal-row">

            <span class="modal-label">
                ชั่วโมงทำงานปกติ
            </span>

            <span class="modal-value">

                ${formatHours(
                    shift.normalHours
                )}

                ชั่วโมง

            </span>

        </div>


        <div class="modal-row">

            <span class="modal-label">
                OT
            </span>

            <span class="modal-value">

                ${
                    shift.otHours > 0
                    ? formatHours(
                        shift.otHours
                    ) + ' ชั่วโมง'
                    : '–'
                }

            </span>

        </div>


        <div class="modal-row">

            <span class="modal-label">
                ค่าแรง
            </span>

            <span class="modal-value">

                ${money(
                    shift.pay
                )}

            </span>

        </div>


        <div class="modal-total">

            <span>
                รวมรอบจ่าย
            </span>

            <span>
                ${money(
                    getPeriodTotal(
                        period
                    )
                )}
            </span>

        </div>

    `;


    dayModal.classList.add(
        'show'
    );
}


/* =====================================================
   CLOSE MODAL
===================================================== */

function closeModal(){

    dayModal.classList.remove(
        'show'
    );
}


function closeModalOutside(
    event
){

    if(
        event.target ===
        dayModal
    ){

        closeModal();
    }
}


/* =====================================================
   MONTH
===================================================== */

function changeMonth(
    amount
){

    viewDate.setMonth(
        viewDate.getMonth() +
        amount
    );


    renderCalendar();
}


/* =====================================================
   PAY PERIOD
===================================================== */

function getPayPeriod(
    key
){

    const date =
        parseDateKey(
            key
        );


    const day =
        date.getDate();


    let start;
    let end;
    let name;


    /*
     * 26 - สิ้นเดือน
     * = รอบ 01 เดือนถัดไป
     */

    if(
        day >= 26
    ){

        const next =
            new Date(
                date.getFullYear(),
                date.getMonth() + 1,
                1
            );


        start =
            `${date.getFullYear()}-${pad(date.getMonth()+1)}-26`;


        end =
            `${next.getFullYear()}-${pad(next.getMonth()+1)}-10`;


        name =
            `รอบจ่าย 01/${thaiMonthShort(
                next.getMonth()
            )}`;

    }

    /*
     * 1 - 10
     */

    else if(
        day <= 10
    ){

        const previous =
            new Date(
                date.getFullYear(),
                date.getMonth() - 1,
                26
            );


        start =
            `${previous.getFullYear()}-${pad(previous.getMonth()+1)}-26`;


        end =
            `${date.getFullYear()}-${pad(date.getMonth()+1)}-10`;


        name =
            `รอบจ่าย 01/${thaiMonthShort(
                date.getMonth()
            )}`;

    }

    /*
     * 11 - 25
     */

    else {

        start =
            `${date.getFullYear()}-${pad(date.getMonth()+1)}-11`;


        end =
            `${date.getFullYear()}-${pad(date.getMonth()+1)}-25`;


        name =
            `รอบจ่าย 02/${thaiMonthShort(
                date.getMonth()
            )}`;

    }


    return {

        start,

        end,

        name

    };
}


/* =====================================================
   PERIOD TOTAL
===================================================== */

function getPeriodTotal(
    period
){

    return shifts
        .filter(
            shift =>
                !shift.incomplete &&
                shift.dateKey >=
                period.start &&
                shift.dateKey <=
                period.end
        )
        .reduce(
            (sum,shift)=>
                sum +
                Number(
                    shift.pay || 0
                ),
            0
        );
}


/* =====================================================
   PERIOD RENDER
===================================================== */

function renderPeriods(){

    const completed =
        shifts.filter(
            s =>
                !s.incomplete
        );


    if(
        !completed.length
    ){

        periods.innerHTML = `

            <div class="empty-state">
                ยังไม่มีข้อมูลรอบจ่าย
            </div>

        `;

        return;
    }


    const map =
        new Map();


    completed.forEach(
        shift => {

            const period =
                getPayPeriod(
                    shift.dateKey
                );


            const key =
                period.start +
                '_' +
                period.end;


            if(
                !map.has(
                    key
                )
            ){

                map.set(
                    key,
                    period
                );

            }

        }
    );


    periods.innerHTML =
        '';


    Array.from(
        map.values()
    )
    .sort(
        (a,b)=>
            a.start.localeCompare(
                b.start
            )
    )
    .reverse()
    .forEach(
        period => {

            const total =
                getPeriodTotal(
                    period
                );


            const days =
                completed.filter(
                    shift =>
                        shift.dateKey >=
                        period.start &&
                        shift.dateKey <=
                        period.end
                ).length;


            const div =
                document.createElement(
                    'div'
                );


            div.className =
                'period';


            div.innerHTML = `

                <div class="period-head">

                    <div>

                        <div class="period-name">
                            ${period.name}
                        </div>

                        <div class="period-range">

                            ${formatShortDate(
                                period.start
                            )}

                            –

                            ${formatShortDate(
                                period.end
                            )}

                        </div>

                    </div>


                    <div class="period-money">

                        ${money(
                            total
                        )}

                    </div>

                </div>


                <div class="period-days">

                    ทำงาน ${days} วัน

                </div>

            `;


            periods.appendChild(
                div
            );

        }
    );
}


/* =====================================================
   DELETE SELECTED DATE
===================================================== */

function deleteSelectedDate(){

    const input =
        document.getElementById('deleteDate');

    const key = input.value;

    if(!key){
        alert('กรุณาเลือกวันที่ก่อน');
        return;
    }

    const target =
        shifts.filter(
            shift =>
                shift.dateKey === key
        );

    if(!target.length){
        alert(`ไม่พบข้อมูลของวันที่ ${formatThaiDate(key)}`);
        return;
    }

    const ok = confirm(
        `ต้องการลบข้อมูลวันที่ ${formatThaiDate(key)} ใช่หรือไม่?\n\n` +
        `พบข้อมูล ${target.length} รายการ\n` +
        `ข้อมูลของวันอื่นจะไม่ถูกลบ`
    );

    if(!ok){
        return;
    }

    shifts =
        shifts.filter(
            shift =>
                shift.dateKey !== key
        );

    saveData();
    renderAll();

    alert(
        `ลบข้อมูลวันที่ ${formatThaiDate(key)} แล้ว`
    );
}


/* =====================================================
   IMAGE COMPRESS
===================================================== */

function compressImage(
    file
){

    return new Promise(
        (resolve,reject)=>{

            const reader =
                new FileReader();


            reader.onload =
                event => {

                    const img =
                        new Image();


                    img.onload =
                        () => {

                            let width =
                                img.width;


                            let height =
                                img.height;


                            const max =
                                1600;


                            if(
                                width > max
                            ){

                                height *=
                                    max /
                                    width;

                                width =
                                    max;
                            }


                            if(
                                height > max
                            ){

                                width *=
                                    max /
                                    height;

                                height =
                                    max;
                            }


                            const canvas =
                                document.createElement(
                                    'canvas'
                                );


                            canvas.width =
                                Math.round(
                                    width
                                );


                            canvas.height =
                                Math.round(
                                    height
                                );


                            const ctx =
                                canvas.getContext(
                                    '2d'
                                );


                            ctx.drawImage(
                                img,
                                0,
                                0,
                                canvas.width,
                                canvas.height
                            );


                            canvas.toBlob(
                                blob => {

                                    if(
                                        !blob
                                    ){

                                        reject(
                                            new Error(
                                                'ลดขนาดรูปไม่สำเร็จ'
                                            )
                                        );

                                        return;
                                    }


                                    resolve(
                                        blob
                                    );

                                },
                                'image/jpeg',
                                .78
                            );

                        };


                    img.onerror =
                        () =>
                            reject(
                                new Error(
                                    'อ่านรูปไม่สำเร็จ'
                                )
                            );


                    img.src =
                        event.target.result;

                };


            reader.onerror =
                () =>
                    reject(
                        new Error(
                            'อ่านไฟล์ไม่สำเร็จ'
                        )
                    );


            reader.readAsDataURL(
                file
            );

        }
    );
}


/* =====================================================
   HELPERS
===================================================== */

function dateObject(
    year,
    month,
    day
){

    return {

        year,

        month,

        day,

        key:
            `${year}-${pad(month)}-${pad(day)}`

    };
}


function timestamp(
    key,
    time
){

    return new Date(
        `${key}T${time}:00`
    ).getTime();
}


function toMinutes(
    time
){

    const [h,m] =
        time.split(':')
        .map(Number);


    return (
        h * 60 +
        m
    );
}


function getDay(
    key
){

    return parseDateKey(
        key
    ).getDay();
}


function parseDateKey(
    key
){

    const [
        y,
        m,
        d
    ] =
        key.split('-')
        .map(Number);


    return new Date(
        y,
        m - 1,
        d
    );
}


function startOfDay(
    date
){

    return new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate()
    );
}


function makeId(
    start,
    end
){

    return (
        start.dateKey +
        '_' +
        start.time +
        '_' +
        end.dateKey +
        '_' +
        end.time
    );
}


function pad(
    n
){

    return String(
        n
    ).padStart(
        2,
        '0'
    );
}


function money(
    value
){

    return (
        '฿' +
        Number(
            value || 0
        ).toLocaleString(
            'th-TH',
            {
                minimumFractionDigits:0,
                maximumFractionDigits:2
            }
        )
    );
}


function formatHours(
    hours
){

    if(
        Number.isInteger(
            hours
        )
    ){

        return String(
            hours
        );
    }


    return Number(
        hours
    ).toFixed(1);
}


function thaiMonth(
    month
){

    return [

        'มกราคม',
        'กุมภาพันธ์',
        'มีนาคม',
        'เมษายน',
        'พฤษภาคม',
        'มิถุนายน',
        'กรกฎาคม',
        'สิงหาคม',
        'กันยายน',
        'ตุลาคม',
        'พฤศจิกายน',
        'ธันวาคม'

    ][month];
}


function thaiMonthShort(
    month
){

    return [

        'ม.ค.',
        'ก.พ.',
        'มี.ค.',
        'เม.ย.',
        'พ.ค.',
        'มิ.ย.',
        'ก.ค.',
        'ส.ค.',
        'ก.ย.',
        'ต.ค.',
        'พ.ย.',
        'ธ.ค.'

    ][month];
}


function formatThaiDate(
    key
){

    const d =
        parseDateKey(
            key
        );


    return `${d.getDate()} ${thaiMonth(d.getMonth())} ${d.getFullYear()+543}`;
}


function formatShortDate(
    key
){

    const d =
        parseDateKey(
            key
        );


    return `${d.getDate()} ${thaiMonthShort(d.getMonth())} ${d.getFullYear()+543}`;
}


function escapeHTML(
    value
){

    return String(
        value
    )
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


/* =====================================================
   INITIAL
===================================================== */

(function initDeleteDate(){
    const input = document.getElementById('deleteDate');
    if(input){
        const now = new Date();
        input.value =
            `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;
    }
})();

renderAll();

    /* =====================================================
   SALARY SLIP
===================================================== */

const salaryPeriod =
    document.getElementById(
        'salaryPeriod'
    );


/* =====================================================
   RENDER SALARY PERIODS
===================================================== */

function renderSalaryPeriods(){

    if(!salaryPeriod){
        return;
    }

    const completed =
        shifts.filter(
            shift =>
                !shift.incomplete
        );


    const map =
        new Map();


    completed.forEach(
        shift => {

            const period =
                getPayPeriod(
                    shift.dateKey
                );


            const key =
                period.start +
                '_' +
                period.end;


            if(
                !map.has(key)
            ){

                map.set(
                    key,
                    period
                );

            }

        }
    );


    salaryPeriod.innerHTML = `

        <option value="">
            -- เลือกรอบจ่าย --
        </option>

    `;


    Array.from(
        map.values()
    )
    .sort(
        (a,b)=>
            b.start.localeCompare(
                a.start
            )
    )
    .forEach(
        period => {

            const option =
                document.createElement(
                    'option'
                );


            option.value =
                JSON.stringify(
                    period
                );


            option.textContent =
                `${period.name} (${formatShortDate(period.start)} – ${formatShortDate(period.end)})`;


            salaryPeriod.appendChild(
                option
            );

        }
    );
}


/* =====================================================
   SALARY DATA
===================================================== */

function getSalaryData(){

    if(
        !salaryPeriod ||
        !salaryPeriod.value
    ){

        throw new Error(
            'กรุณาเลือกรอบจ่ายก่อน'
        );
    }


    const period =
        JSON.parse(
            salaryPeriod.value
        );


    const name =
        document.getElementById(
            'employeeName'
        ).value.trim();


    const employeeId =
        document.getElementById(
            'employeeId'
        ).value.trim();


    const room =
        Number(
            document.getElementById(
                'roomAllowance'
            ).value
        ) || 0;


    const rate =
        Number(
            document.getElementById(
                'socialSecurityRate'
            ).value
        ) || 0;


    const workShifts =
        shifts.filter(
            shift =>

                !shift.incomplete &&

                shift.dateKey >=
                    period.start &&

                shift.dateKey <=
                    period.end
        );


    const workDays =
        workShifts.length;


    const wage =
        workShifts.reduce(
            (sum,shift)=>
                sum +
                Number(
                    shift.pay || 0
                ),
            0
        );


    const beforeDeduction =
        wage +
        room;


    const socialSecurity =
        beforeDeduction *
        rate /
        100;


    const total =
        beforeDeduction -
        socialSecurity;


    return {

        period,

        name,

        employeeId,

        workDays,

        wage,

        room,

        beforeDeduction,

        rate,

        socialSecurity,

        total

    };
}


/* =====================================================
   PREVIEW
===================================================== */

function previewSalarySlip(){

    try{

        const data =
            getSalaryData();


        let old =
            document.getElementById(
                'salaryPreview'
            );


        if(
            old
        ){

            old.remove();

        }


        const preview =
            document.createElement(
                'div'
            );


        preview.id =
            'salaryPreview';


        preview.className =
            'salary-preview';


        preview.innerHTML = `

            <div class="salary-preview-header">

                <h3>
                    SLIP เงินเดือน
                </h3>

                <p>
                    ${escapeHTML(
                        data.period.name
                    )}
                </p>

            </div>


            <div class="salary-employee">

                <strong>
                    ${escapeHTML(
                        data.name
                    )}
                </strong>

                <span>
                    รหัสพนักงาน
                    ${escapeHTML(
                        data.employeeId
                    )}
                </span>

            </div>


            <div class="salary-line">

                <span>
                    วันที่ทำงาน
                </span>

                <strong>
                    ${data.workDays} วัน
                </strong>

            </div>


            <div class="salary-line">

                <span>
                    ค่าแรง
                </span>

                <strong>
                    ${formatBaht(
                        data.wage
                    )}
                </strong>

            </div>


            <div class="salary-line">

                <span>
                    ค่าห้องเย็น
                </span>

                <strong>
                    ${formatBaht(
                        data.room
                    )}
                </strong>

            </div>


            <div class="salary-line salary-subtotal">

                <span>
                    รวมก่อนหัก
                </span>

                <strong>
                    ${formatBaht(
                        data.beforeDeduction
                    )}
                </strong>

            </div>


            <div class="salary-line">

                <span>
                    หักประกันสังคม ${data.rate}%
                </span>

                <strong>
                    ${formatBaht(
                        data.socialSecurity
                    )}
                </strong>

            </div>


            <div class="salary-total">

                <span>
                    รวมทั้งหมด
                </span>

                <span>
                    ${formatBaht(
                        data.total
                    )}
                </span>

            </div>

        `;


        document
            .querySelector(
                '.salary-slip-box'
            )
            .appendChild(
                preview
            );


    }catch(error){

        alert(
            error.message
        );

    }
}


/* =====================================================
   FORMAT BAHT
===================================================== */

function formatBaht(
    value
){

    return (
        Number(
            value || 0
        ).toLocaleString(
            'th-TH',
            {
                minimumFractionDigits:2,
                maximumFractionDigits:2
            }
        ) +
        ' บาท'
    );
}


/* =====================================================
   GENERATE PDF
===================================================== */

async function generateSalaryPDF(){

    try{

        const data =
            getSalaryData();


        const {
            jsPDF
        } =
            window.jspdf;


        const doc =
            new jsPDF({
                orientation:'portrait',
                unit:'mm',
                format:'a4'
            });
            
            await loadThaiFont(doc);

doc.setFont(
    'NotoSansThai',
    'normal'
);


        /*
         * ฟอนต์ไทย
         *
         * jsPDF ปกติไม่รองรับภาษาไทย
         * ดังนั้นใช้ข้อความอังกฤษ/ตัวเลข
         * ใน PDF หากยังไม่ได้ฝังฟอนต์ไทย
         *
         * ด้านล่างเตรียม layout ให้ตรง
         * กับสลิปที่ต้องการ
         */


        const pageWidth =
            doc.internal.pageSize.getWidth();


        const center =
            pageWidth / 2;


        let y =
            25;


        doc.setFontSize(
            22
        );


        doc.setFont(
            'NotoSansThai',
            'normal'
        );


        doc.text(
            'SALARY SLIP',
            center,
            y,
            {
                align:'center'
            }
        );


        y += 9;


        doc.setFontSize(
            12
        );


        doc.setFont(
            'NotoSansThai',
            'normal'
        );


        doc.text(
            String(
                data.period.name
            ),
            center,
            y,
            {
                align:'center'
            }
        );


        y += 14;


        doc.line(
            20,
            y,
            pageWidth - 20,
            y
        );


        y += 10;


        doc.setFontSize(
            12
        );


        doc.setFont(
            'NotoSansThai',
            'normal'
        );


        doc.text(
            data.name || '-',
            20,
            y
        );


        y += 7;


        doc.setFont(
            'NotoSansThai',
            'normal'
        );


        doc.text(
            'Employee ID: ' +
            (data.employeeId || '-'),
            20,
            y
        );


        y += 12;


        doc.line(
            20,
            y,
            pageWidth - 20,
            y
        );


        y += 12;


        /*
         * ตาราง
         */

        doc.autoTable({

            startY:y,

            theme:'plain',

            margin:{
                left:20,
                right:20
            },

            styles:{
                font:'NotoSansThai',
                fontSize:12,
                cellPadding:4
            },

            columnStyles:{
                0:{
                    halign:'left'
                },
                1:{
                    halign:'right'
                }
            },

            body:[

                [
                    'วันที่ทำงาน',
                    data.workDays +
                    ' วัน'
                ],

                [
                    'ค่าแรง',
                    formatNumber(
                        data.wage
                    ) +
                    ' บาท'
                ],

                [
                    'ค่าห้องเย็น',
                    formatNumber(
                        data.room
                    ) +
                    ' บาท'
                ],

                [
                    'รวมก่อนหัก',
                    formatNumber(
                        data.beforeDeduction
                    ) +
                    ' บาท'
                ],

                [
                    'หักประกันสังคม ' +
                    data.rate +
                    '%',

                    formatNumber(
                        data.socialSecurity
                    ) +
                    ' บาท'
                ]

            ]

        });


        y =
            doc.lastAutoTable.finalY +
            10;


        doc.line(
            20,
            y,
            pageWidth - 20,
            y
        );


        y += 12;


        doc.setFontSize(
            16
        );


        doc.setFont(
            'NotoSansThai',
            'normal'
        );


        doc.text(
            'รวมทั้งหมด',
            20,
            y
        );


        doc.text(
            'THB ' +
            formatNumber(
                data.total
            ),
            pageWidth - 20,
            y,
            {
                align:'right'
            }
        );


        y += 8;


        doc.line(
            20,
            y,
            pageWidth - 20,
            y
        );


        /*
         * ชื่อไฟล์
         */

        const filename =
            'SalarySlip_' +
            data.period.name
                .replace(
                    /[\/\\:*?"<>| ]/g,
                    '_'
                ) +
            '.pdf';


        doc.save(
            filename
        );


    }catch(error){

        console.error(
            error
        );


        alert(
            error.message
        );

    }
}


/* =====================================================
   NUMBER FORMAT FOR PDF
===================================================== */

function formatNumber(
    value
){

    return Number(
        value || 0
    ).toLocaleString(
        'en-US',
        {
            minimumFractionDigits:2,
            maximumFractionDigits:2
        }
    );
}

</script>

</body>

</html>
