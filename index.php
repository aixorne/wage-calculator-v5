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
                ฿0.00
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
     ALL PERIODS MODAL
===================================================== -->

<div
    id="periodsMoreModal"
    class="modal"
    onclick="closePeriodsMoreOutside(event)"
>

    <div class="modal-box periods-more-box">

        <div class="modal-header">

            <h3>
                📋 รอบจ่ายทั้งหมด
            </h3>

            <button
                class="modal-close"
                onclick="closePeriodsMore()"
            >
                ×
            </button>

        </div>

        <div
            id="allPeriodsList"
            class="modal-body"
        ></div>

    </div>

</div>


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

                <label>
                    ชื่อพนักงาน
                </label>

                <input
                    id="employeeName"
                    type="text"
                    value="นายจิรวัฒน์ กาญจนบุรางกูร"
                >

            </div>


            <div class="slip-field">

                <label>
                    รหัสพนักงาน
                </label>

                <input
                    id="employeeId"
                    type="text"
                    value="14187"
                >

            </div>

        </div>


        <div class="slip-field">

            <label>
                เลือกรอบจ่าย
            </label>

            <select id="salaryPeriod">

                <option value="">
                    -- เลือกรอบจ่าย --
                </option>

            </select>

        </div>


        <div class="slip-field">

            <label>
                ค่าห้องเย็น
            </label>

            <div class="money-input">

                <input
                    id="roomAllowance"
                    type="number"
                    value="120"
                    min="0"
                    step="0.01"
                >

                <span>
                    บาท
                </span>

            </div>

        </div>


        <div class="slip-field">

            <label>
                ประกันสังคม
            </label>

            <div class="money-input">

                <input
                    id="socialSecurityRate"
                    type="number"
                    value="3"
                    min="0"
                    max="100"
                    step="0.1"
                >

                <span>
                    %
                </span>

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

        <span>
            💾
        </span>

        <h2>
            จัดการข้อมูล
        </h2>

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

        <label for="deleteDate">
            เลือกวันที่ที่ต้องการลบ
        </label>


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

            ลบเฉพาะกะที่เริ่มในวันที่เลือกเท่านั้น
            ข้อมูลวันอื่นจะไม่ถูกลบ

        </div>

    </div>

</section>


</div>


<!-- =====================================================
     DAY MODAL
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


<!-- =====================================================
     SALARY PREVIEW MODAL
===================================================== -->

<div
    id="salaryPreviewModal"
    class="modal"
    onclick="closeSalaryPreviewOutside(event)"
>

    <div class="modal-box">

        <div class="modal-header">

            <h3>
                🧾 ตัวอย่างสลิปเงินเดือน
            </h3>

            <button
                class="modal-close"
                onclick="closeSalaryPreview()"
            >
                ×
            </button>

        </div>


        <div
            id="salaryPreviewBody"
            class="modal-body"
        ></div>

    </div>

</div>


<script>

/* =====================================================
   THAI FONT
===================================================== */

async function loadThaiFont(doc){

    const response =
        await fetch(
            'NotoSansThai-Regular.ttf'
        );


    if(!response.ok){

        throw new Error(
            'ไม่พบไฟล์ NotoSansThai-Regular.ttf'
        );

    }


    const buffer =
        await response.arrayBuffer();


    const bytes =
        new Uint8Array(
            buffer
        );


    let binary = '';


    const chunkSize =
        0x8000;


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


const salaryPeriod =
    document.getElementById(
        'salaryPeriod'
    );


/* =====================================================
   INIT
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
                    ${escapeHTML(file.name)}
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
   REMOVE FILE
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
            let i = 0;
            i < selectedFiles.length;
            i++
        ){

            const file =
                selectedFiles[i];


            loading.textContent =
                `🔍 กำลังอ่านรูป ${i + 1}/${selectedFiles.length}`;


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


            if(
                !response.ok
            ){

                throw new Error(
                    `OCR Server Error ${response.status}`
                );

            }


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
   COMPRESS IMAGE
===================================================== */

function compressImage(
    file
){

    return new Promise(
        (resolve,reject)=>{

            const img =
                new Image();


            const url =
                URL.createObjectURL(
                    file
                );


            img.onload = () => {

                URL.revokeObjectURL(
                    url
                );


                const maxWidth =
                    1800;


                let width =
                    img.width;


                let height =
                    img.height;


                if(
                    width > maxWidth
                ){

                    height =
                        Math.round(
                            height *
                            maxWidth /
                            width
                        );


                    width =
                        maxWidth;

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
                    blob => {

                        if(!blob){

                            reject(
                                new Error(
                                    'ไม่สามารถบีบอัดรูปได้'
                                )
                            );

                            return;

                        }


                        resolve(
                            blob
                        );

                    },
                    'image/jpeg',
                    0.88
                );

            };


            img.onerror = () => {

                URL.revokeObjectURL(
                    url
                );


                reject(
                    new Error(
                        'ไม่สามารถอ่านรูปได้'
                    )
                );

            };


            img.src =
                url;

        }
    );

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


    m =
        text.match(
            /\b(20\d{2})[-\/](\d{1,2})[-\/](\d{1,2})\b/
        );


    if(m){

        let year =
            Number(
                m[1]
            );


        const month =
            Number(
                m[2]
            );


        const day =
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

function createShifts(records){

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
        let i = 0;
        i < events.length;
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
            let j = i + 1;
            j < events.length;
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


    if(
        day === 0
    ){

        return (
            e > s ||
            end.dateKey !==
            start.dateKey
        );

    }


    if(
        s >=
        19 * 60
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


    if(
        s >= 7 * 60 + 30 &&
        s <= 12 * 60
    ){

        return (
            start.dateKey ===
            end.dateKey &&
            e >= 16 * 60
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


    if(
        s >=
        16 * 60 + 30 &&
        s <=
        17 * 60
    ){

        s =
            17 * 60;

    }


    e =
        Math.floor(
            e / 30
        ) * 30;


    if(
        end.dateKey !==
        start.dateKey &&
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


    if(
        s >=
        7 * 60 + 30 &&
        s <=
        8 * 60
    ){

        s =
            8 * 60;

    }


    const normalPay =
        NORMAL_WAGE;


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


    if(
        s >=
        19 * 60 + 30 &&
        s <=
        20 * 60
    ){

        s =
            20 * 60;

    }


    if(
        end.dateKey !==
        start.dateKey &&
        e < s
    ){

        e +=
            24 * 60;

    }


    const normalEnd =
        5 * 60 +
        24 * 60;


    let normalMinutes =
        Math.max(
            0,
            Math.min(
                e,
                normalEnd
            ) -
            s
        );


    const otStart =
        5 * 60 +
        30 +
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


    shifts.forEach(
        shift => {

            map.set(
                shift.id,
                shift
            );

        }
    );


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
   STORAGE
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
        let i = 0;
        i < first;
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
        let day = 1;
        day <= max;
        day++
    ){

        const key =
            `${year}-${pad(month + 1)}-${pad(day)}`;


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
            shift &&
            !shift.incomplete
        ){

            html += `

                <div class="day-time">
                    ${escapeHTML(shift.startTime)}
                    –
                    ${escapeHTML(shift.endTime)}
                </div>

                <div class="day-money">
                    ${money(shift.pay)}
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

    const found =
        shifts.filter(
            s =>
                s.startDateKey === key ||
                s.dateKey === key
        );


    if(
        !found.length
    ){

        return null;

    }


    return found[0];

}


/* =====================================================
   CHANGE MONTH
===================================================== */

function changeMonth(
    amount
){

    viewDate =
        new Date(
            viewDate.getFullYear(),
            viewDate.getMonth() + amount,
            1
        );


    renderCalendar();

}


/* =====================================================
   OPEN DAY
===================================================== */

function openDay(
    key
){

    const list =
        shifts.filter(
            s =>
                s.startDateKey === key ||
                s.dateKey === key
        );


    modalTitle.textContent =
        `รายละเอียด ${formatThaiDate(key)}`;


    if(
        !list.length
    ){

        modalBody.innerHTML = `

            <div class="empty-state">
                ไม่มีข้อมูลการทำงานวันนี้
            </div>

        `;

        dayModal.classList.add(
            'show'
        );

        return;

    }


    modalBody.innerHTML =
        list.map(
            shift => `

                <div class="shift-detail">

                    <div>
                        <strong>
                            ${shift.isNight
                                ? '🌙 กะดึก'
                                : shift.isSunday
                                    ? '☀️ วันอาทิตย์'
                                    : '☀️ กะกลางวัน'}
                        </strong>
                    </div>

                    <div>
                        เวลา:
                        ${escapeHTML(shift.startTime || '-')}
                        –
                        ${escapeHTML(shift.endTime || '-')}
                    </div>

                    ${
                        shift.incomplete
                        ? `
                            <div class="warning">
                                ⚠️ ยังไม่มีเวลาออก
                            </div>
                        `
                        : `
                            <div>
                                ค่าแรง:
                                <strong>
                                    ${money(shift.pay)}
                                </strong>
                            </div>

                            <div>
                                OT:
                                ${Number(shift.otHours || 0).toFixed(1)}
                                ชั่วโมง
                            </div>
                        `
                    }

                </div>

            `
        )
        .join('');


    dayModal.classList.add(
        'show'
    );

}


/* =====================================================
   CLOSE DAY MODAL
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
        event.target === dayModal
    ){

        closeModal();

    }

}


/* =====================================================
   PAY PERIODS
===================================================== */

function getPayPeriods(){

    const year =
        viewDate.getFullYear();


    const month =
        viewDate.getMonth();


    const lastDay =
        new Date(
            year,
            month + 1,
            0
        ).getDate();


    return [

        {
            id:
                `${year}-${pad(month + 1)}-01_15`,

            name:
                `01–15/${pad(month + 1)}`,

            start:
                `${year}-${pad(month + 1)}-01`,

            end:
                `${year}-${pad(month + 1)}-15`

        },

        {
            id:
                `${year}-${pad(month + 1)}-16_${lastDay}`,

            name:
                `16–${lastDay}/${pad(month + 1)}`,

            start:
                `${year}-${pad(month + 1)}-16`,

            end:
                `${year}-${pad(month + 1)}-${pad(lastDay)}`

        }

    ];

}


/* =====================================================
   PERIOD DATA
===================================================== */

function getPeriodData(
    period
){

    const list =
        shifts.filter(
            shift =>
                !shift.incomplete &&
                shift.dateKey >= period.start &&
                shift.dateKey <= period.end
        );


    const wage =
        list.reduce(
            (sum,shift)=>
                sum +
                Number(
                    shift.pay || 0
                ),
            0
        );


    const workDays =
        new Set(
            list.map(
                shift =>
                    shift.dateKey
            )
        ).size;


    const otHours =
        list.reduce(
            (sum,shift)=>
                sum +
                Number(
                    shift.otHours || 0
                ),
            0
        );


    return {

        shifts:list,

        wage,

        workDays,

        otHours

    };

}


/* =====================================================
   RENDER PERIODS
===================================================== */

function renderPeriods(){

    const periodList =
        getPayPeriods();


    const allPeriods =
        periodList.map(
            period => ({

                period,

                data:
                    getPeriodData(
                        period
                    )

            })
        );


    if(
        !shifts.length
    ){

        periods.innerHTML = `

            <div class="empty-state">
                ยังไม่มีข้อมูลรอบจ่าย
            </div>

        `;

        return;

    }


    periods.innerHTML =
        allPeriods.map(
            item => `

                <div class="period-card">

                    <div class="period-title">
                        💵 รอบ ${item.period.name}
                    </div>

                    <div class="period-info">

                        <span>
                            วันทำงาน
                            <strong>
                                ${item.data.workDays}
                            </strong>
                        </span>

                        <span>
                            OT
                            <strong>
                                ${item.data.otHours.toFixed(1)}
                            </strong>
                            ชม.
                        </span>

                        <span>
                            ค่าแรง
                            <strong>
                                ${money(item.data.wage)}
                            </strong>
                        </span>

                    </div>

                </div>

            `
        )
        .join('');

}


/* =====================================================
   RENDER SALARY PERIOD SELECT
===================================================== */

function renderSalaryPeriods(){

    const current =
        salaryPeriod.value;


    const periodList =
        getPayPeriods();


    salaryPeriod.innerHTML = `

        <option value="">
            -- เลือกรอบจ่าย --
        </option>

    `;


    periodList.forEach(
        period => {

            const option =
                document.createElement(
                    'option'
                );


            option.value =
                period.id;


            option.textContent =
                `รอบ ${period.name}`;


            salaryPeriod.appendChild(
                option
            );

        }
    );


    if(
        periodList.some(
            p =>
                p.id === current
        )
    ){

        salaryPeriod.value =
            current;

    }

}


/* =====================================================
   ALL PERIODS
===================================================== */

function openPeriodsMore(){

    const periodList =
        getPayPeriods();


    document.getElementById(
        'allPeriodsList'
    ).innerHTML =
        periodList.map(
            period => {

                const data =
                    getPeriodData(
                        period
                    );


                return `

                    <div class="period-card">

                        <div class="period-title">
                            รอบ ${period.name}
                        </div>

                        <div>
                            วันทำงาน:
                            <strong>
                                ${data.workDays}
                            </strong>
                        </div>

                        <div>
                            OT:
                            <strong>
                                ${data.otHours.toFixed(1)}
                            </strong>
                            ชั่วโมง
                        </div>

                        <div>
                            ค่าแรง:
                            <strong>
                                ${money(data.wage)}
                            </strong>
                        </div>

                    </div>

                `;

            }
        )
        .join('');


    document.getElementById(
        'periodsMoreModal'
    ).classList.add(
        'show'
    );

}


function closePeriodsMore(){

    document.getElementById(
        'periodsMoreModal'
    ).classList.remove(
        'show'
    );

}


function closePeriodsMoreOutside(
    event
){

    const modal =
        document.getElementById(
            'periodsMoreModal'
        );


    if(
        event.target === modal
    ){

        closePeriodsMore();

    }

}


/* =====================================================
   DELETE SELECTED DATE
===================================================== */

function deleteSelectedDate(){

    const input =
        document.getElementById(
            'deleteDate'
        );


    const key =
        input.value;


    if(
        !key
    ){

        alert(
            'กรุณาเลือกวันที่ก่อน'
        );

        return;

    }


    const count =
        shifts.filter(
            s =>
                s.startDateKey === key ||
                s.dateKey === key
        ).length;


    if(
        !count
    ){

        alert(
            'ไม่พบข้อมูลของวันที่เลือก'
        );

        return;

    }


    const ok =
        confirm(
            `ต้องการลบข้อมูลวันที่ ${formatThaiDate(key)} จำนวน ${count} รายการหรือไม่?`
        );


    if(
        !ok
    ){

        return;

    }


    shifts =
        shifts.filter(
            s =>
                s.startDateKey !== key &&
                s.dateKey !== key
        );


    saveData();

    renderAll();


    alert(
        'ลบข้อมูลเรียบร้อยแล้ว'
    );

}


/* =====================================================
   SALARY DATA
===================================================== */

function getSalaryData(){

    const periodId =
        salaryPeriod.value;


    if(
        !periodId
    ){

        throw new Error(
            'กรุณาเลือกรอบจ่ายก่อน'
        );

    }


    const period =
        getPayPeriods().find(
            p =>
                p.id === periodId
        );


    if(
        !period
    ){

        throw new Error(
            'ไม่พบข้อมูลรอบจ่าย'
        );

    }


    const periodData =
        getPeriodData(
            period
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
            ).value || 0
        );


    const rate =
        Number(
            document.getElementById(
                'socialSecurityRate'
            ).value || 0
        );


    const wage =
        Number(
            periodData.wage || 0
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

        name,

        employeeId,

        period,

        workDays:
            periodData.workDays,

        otHours:
            periodData.otHours,

        wage,

        room,

        rate,

        socialSecurity,

        beforeDeduction,

        total

    };

}


/* =====================================================
   SALARY PREVIEW
===================================================== */

function previewSalarySlip(){

    try{

        const data =
            getSalaryData();


        const body =
            document.getElementById(
                'salaryPreviewBody'
            );


        body.innerHTML = `

            <div class="salary-preview">

                <div class="salary-preview-title">
                    สลิปเงินเดือน
                </div>

                <div class="salary-preview-period">
                    รอบ ${escapeHTML(data.period.name)}
                </div>

                <hr>

                <div class="preview-row">
                    <span>ชื่อพนักงาน</span>
                    <strong>
                        ${escapeHTML(data.name || '-')}
                    </strong>
                </div>

                <div class="preview-row">
                    <span>รหัสพนักงาน</span>
                    <strong>
                        ${escapeHTML(data.employeeId || '-')}
                    </strong>
                </div>

                <div class="preview-row">
                    <span>จำนวนวันทำงาน</span>
                    <strong>
                        ${data.workDays} วัน
                    </strong>
                </div>

                <hr>

                <div class="preview-row">
                    <span>ค่าแรง</span>
                    <strong>
                        ${formatNumber(data.wage)} บาท
                    </strong>
                </div>

                <div class="preview-row">
                    <span>ค่าห้องเย็น</span>
                    <strong>
                        ${formatNumber(data.room)} บาท
                    </strong>
                </div>

                <div class="preview-row">
                    <span>
                        ประกันสังคม ${data.rate}%
                    </span>

                    <strong>
                        -${formatNumber(data.socialSecurity)} บาท
                    </strong>
                </div>

                <div class="preview-total">

                    <span>
                        เงินรับสุทธิ
                    </span>

                    <strong>
                        ฿ ${formatNumber(data.total)}
                    </strong>

                </div>

            </div>

        `;


        document.getElementById(
            'salaryPreviewModal'
        ).classList.add(
            'show'
        );

    }catch(error){

        alert(
            error.message
        );

    }

}


function closeSalaryPreview(){

    document.getElementById(
        'salaryPreviewModal'
    ).classList.remove(
        'show'
    );

}


function closeSalaryPreviewOutside(
    event
){

    const modal =
        document.getElementById(
            'salaryPreviewModal'
        );


    if(
        event.target === modal
    ){

        closeSalaryPreview();

    }

}


/* =====================================================
   GENERATE SALARY PDF
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

                orientation:
                    'portrait',

                unit:
                    'mm',

                format:
                    'a4'

            });


        /* =================================================
           LOAD THAI FONT
        ================================================= */

        await loadThaiFont(
            doc
        );


        const pageWidth =
            doc.internal.pageSize.getWidth();


        const pageHeight =
            doc.internal.pageSize.getHeight();


        const margin =
            18;


        const contentWidth =
            pageWidth -
            margin * 2;


        const right =
            pageWidth -
            margin;


        let y =
            17;


        /* =================================================
           HEADER
        ================================================= */

        doc.setFont(
            'NotoSansThai',
            'normal'
        );


        doc.setTextColor(
            15,
            23,
            42
        );


        doc.setFontSize(
            20
        );


        doc.text(
            'สลิปเงินเดือน',
            margin,
            y
        );


        doc.setFontSize(
            9
        );


        doc.setTextColor(
            100,
            116,
            139
        );


        doc.text(
            `รอบจ่าย ${data.period.name}`,
            right,
            y,
            {
                align:'right'
            }
        );


        y +=
            7;


        doc.setDrawColor(
            13,
            52,
            144
        );


        doc.setLineWidth(
            1
        );


        doc.line(
            margin,
            y,
            right,
            y
        );


        y +=
            8;


        /* =================================================
           EMPLOYEE INFO BOX
        ================================================= */

        /*
         * เพิ่มความสูงเพื่อให้ภาษาไทย
         * และวรรณยุกต์ไม่ชิดขอบ
         */

        const infoBoxHeight =
            34;


        const colWidth =
            contentWidth / 2;


        doc.setDrawColor(
            226,
            232,
            240
        );


        doc.setFillColor(
            248,
            250,
            252
        );


        doc.roundedRect(
            margin,
            y,
            contentWidth,
            infoBoxHeight,
            3,
            3,
            'FD'
        );


        /* =================================================
           CENTER DIVIDER
        ================================================= */

        doc.setDrawColor(
            226,
            232,
            240
        );


        doc.line(
            margin + colWidth,
            y + 5,
            margin + colWidth,
            y + infoBoxHeight - 5
        );


        /* =================================================
           COLUMN CENTER
        ================================================= */

        const leftCenter =
            margin +
            colWidth / 2;


        const rightCenter =
            margin +
            colWidth +
            colWidth / 2;


        /*
         * ตำแหน่งแนวตั้ง
         */

        const labelY =
            y + 9;


        const valueY =
            y + 18;


        const bottomLabelY =
            y + 26;


        const bottomValueY =
            y + 31;


        /* =================================================
           LEFT COLUMN
        ================================================= */

        doc.setFontSize(
            8
        );


        doc.setTextColor(
            100,
            116,
            139
        );


        doc.text(
            'ชื่อพนักงาน',
            leftCenter,
            labelY,
            {
                align:'center'
            }
        );


        let nameFontSize =
            10;


        const employeeName =
            String(
                data.name || '-'
            );


        if(
            employeeName.length > 24
        ){

            nameFontSize =
                8;

        }else if(
            employeeName.length > 19
        ){

            nameFontSize =
                8.5;

        }else if(
            employeeName.length > 15
        ){

            nameFontSize =
                9;

        }


        doc.setFontSize(
            nameFontSize
        );


        doc.setTextColor(
            30,
            41,
            59
        );


        doc.text(
            employeeName,
            leftCenter,
            valueY,
            {
                align:'center',
                maxWidth:
                    colWidth - 8
            }
        );


        /* =================================================
           RIGHT COLUMN
        ================================================= */

        doc.setFontSize(
            8
        );


        doc.setTextColor(
            100,
            116,
            139
        );


        doc.text(
            'รหัสพนักงาน',
            rightCenter,
            labelY,
            {
                align:'center'
            }
        );


        doc.setFontSize(
            10
        );


        doc.setTextColor(
            30,
            41,
            59
        );


        doc.text(
            String(
                data.employeeId || '-'
            ),
            rightCenter,
            valueY,
            {
                align:'center'
            }
        );


        /* =================================================
           BOTTOM INFORMATION
        ================================================= */

        doc.setFontSize(
            8
        );


        doc.setTextColor(
            100,
            116,
            139
        );


        doc.text(
            'จำนวนวันทำงาน',
            leftCenter,
            bottomLabelY,
            {
                align:'center'
            }
        );


        doc.text(
            'วันที่ออกเอกสาร',
            rightCenter,
            bottomLabelY,
            {
                align:'center'
            }
        );


        doc.setFontSize(
            9
        );


        doc.setTextColor(
            30,
            41,
            59
        );


        /*
         * FIX:
         * ต้องใช้ template string
         */

        doc.text(
            `${data.workDays} วัน`,
            leftCenter,
            bottomValueY,
            {
                align:'center'
            }
        );


        const today =
            new Date();


        const todayKey =
            `${today.getFullYear()}-${pad(today.getMonth() + 1)}-${pad(today.getDate())}`;


        doc.text(
            formatThaiDate(todayKey),
            rightCenter,
            bottomValueY,
            {
                align:'center'
            }
        );


        /* =================================================
           MOVE TO NEXT SECTION
        ================================================= */

        y +=
            infoBoxHeight +
            9;


        /* =================================================
           EARNINGS
        ================================================= */

        doc.setFontSize(
            11
        );


        doc.setTextColor(
            30,
            41,
            59
        );


        doc.text(
            'รายได้',
            margin,
            y
        );


        y +=
            5;


        doc.autoTable({

            startY:
                y,

            margin:{
                left:
                    margin,

                right:
                    margin
            },

            theme:
                'grid',

            styles:{

                font:
                    'NotoSansThai',

                fontSize:
                    9,

                cellPadding:
                    4,

                lineColor:[
                    226,
                    232,
                    240
                ],

                lineWidth:
                    0.2,

                textColor:[
                    30,
                    41,
                    59
                ],

                valign:
                    'middle'

            },

            headStyles:{

                fillColor:[
                    241,
                    245,
                    249
                ],

                textColor:[
                    30,
                    41,
                    59
                ],

                fontStyle:
                    'normal',

                valign:
                    'middle'

            },

            columnStyles:{

                0:{
                    halign:
                        'left',

                    cellWidth:
                        contentWidth * .65
                },

                1:{
                    halign:
                        'right'
                }

            },

            head:[

                [
                    'รายการ',
                    'จำนวนเงิน'
                ]

            ],

            body:[

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
                ]

            ]

        });


        y =
            doc.lastAutoTable.finalY +
            11;


        /* =================================================
           GROSS
        ================================================= */

        doc.setFillColor(
            248,
            250,
            252
        );


        doc.roundedRect(
            margin,
            y,
            contentWidth,
            14,
            3,
            3,
            'F'
        );


        doc.setFontSize(
            10
        );


        doc.setTextColor(
            71,
            85,
            105
        );


        doc.text(
            'รวมรายได้ก่อนหัก',
            margin + 7,
            y + 9
        );


        doc.setTextColor(
            30,
            41,
            59
        );


        doc.setFontSize(
            11
        );


        doc.text(
            formatNumber(
                data.beforeDeduction
            ) +
            ' บาท',
            right - 7,
            y + 9,
            {
                align:'right'
            }
        );


        y +=
            22;


        /* =================================================
           DEDUCTIONS
        ================================================= */

        doc.setFontSize(
            11
        );


        doc.text(
            'รายการหัก',
            margin,
            y
        );


        y +=
            5;


        doc.autoTable({

            startY:
                y,

            margin:{
                left:
                    margin,

                right:
                    margin
            },

            theme:
                'grid',

            styles:{

                font:
                    'NotoSansThai',

                fontSize:
                    9,

                cellPadding:
                    4,

                lineColor:[
                    226,
                    232,
                    240
                ],

                lineWidth:
                    0.2,

                textColor:[
                    30,
                    41,
                    59
                ],

                valign:
                    'middle'

            },

            columnStyles:{

                0:{
                    halign:
                        'left',

                    cellWidth:
                        contentWidth * .65
                },

                1:{
                    halign:
                        'right'
                }

            },

            body:[

                [
                    `ประกันสังคม ${data.rate}%`,

                    formatNumber(
                        data.socialSecurity
                    ) +
                    ' บาท'
                ]

            ]

        });


        y =
            doc.lastAutoTable.finalY +
            13;


        /* =================================================
           NET PAY
        ================================================= */

        doc.setFillColor(
            13,
            52,
            144
        );


        doc.roundedRect(
            margin,
            y,
            contentWidth,
            30,
            4,
            4,
            'F'
        );


        doc.setTextColor(
            255,
            255,
            255
        );


        doc.setFontSize(
            10
        );


        doc.text(
            'เงินรับสุทธิ',
            margin + 8,
            y + 11
        );


        doc.setFontSize(
            8
        );


        doc.text(
            'NET PAY',
            margin + 8,
            y + 19
        );


        doc.setFontSize(
            17
        );


        doc.text(
            '฿ ' +
            formatNumber(
                data.total
            ),
            right - 8,
            y + 17,
            {
                align:'right'
            }
        );


        y +=
            42;


        /* =================================================
           FOOTER
        ================================================= */

        doc.setDrawColor(
            226,
            232,
            240
        );


        doc.setLineWidth(
            0.2
        );


        doc.line(
            margin,
            pageHeight - 25,
            right,
            pageHeight - 25
        );


        doc.setTextColor(
            100,
            116,
            139
        );


        doc.setFontSize(
            7
        );


        doc.text(
            'เอกสารนี้สร้างโดย AI by Ryzee',
            margin,
            pageHeight - 18
        );


        doc.text(
            'เอกสารอิเล็กทรอนิกส์',
            right,
            pageHeight - 18,
            {
                align:'right'
            }
        );


        /* =================================================
           SAVE
        ================================================= */

        const safePeriodName =
            String(
                data.period.name
            ).replace(
                /[\/\\:*?"<>| ]/g,
                '_'
            );


        const filename =
            'SalarySlip_' +
            safePeriodName +
            '_' +
            (
                data.employeeId ||
                'employee'
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
            minimumFractionDigits:
                2,

            maximumFractionDigits:
                2
        }
    );

}


/* =====================================================
   MONEY
===================================================== */

function money(
    value
){

    return '฿' +
        Number(
            value || 0
        ).toLocaleString(
            'en-US',
            {
                minimumFractionDigits:
                    2,

                maximumFractionDigits:
                    2
            }
        );

}


/* =====================================================
   DATE OBJECT
===================================================== */

function dateObject(
    year,
    month,
    day
){

    const date =
        new Date(
            year,
            month - 1,
            day
        );


    if(
        date.getFullYear() !== year ||
        date.getMonth() !== month - 1 ||
        date.getDate() !== day
    ){

        return null;

    }


    return {

        year,

        month,

        day,

        key:
            `${year}-${pad(month)}-${pad(day)}`

    };

}


/* =====================================================
   PARSE DATE KEY
===================================================== */

function parseDateKey(
    key
){

    const [
        y,
        m,
        d
    ] =
        key
        .split('-')
        .map(
            Number
        );


    return new Date(
        y,
        m - 1,
        d
    );

}


/* =====================================================
   START OF DAY
===================================================== */

function startOfDay(
    date
){

    return new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate()
    );

}


/* =====================================================
   DAY OF WEEK
===================================================== */

function getDay(
    key
){

    return parseDateKey(
        key
    ).getDay();

}


/* =====================================================
   TIMESTAMP
===================================================== */

function timestamp(
    dateKey,
    time
){

    const date =
        parseDateKey(
            dateKey
        );


    const minutes =
        toMinutes(
            time
        );


    date.setHours(
        Math.floor(
            minutes / 60
        ),
        minutes % 60,
        0,
        0
    );


    return date.getTime();

}


/* =====================================================
   TIME TO MINUTES
===================================================== */

function toMinutes(
    time
){

    const [
        h,
        m
    ] =
        time
        .split(':')
        .map(
            Number
        );


    return (
        h * 60
    ) +
    m;

}


/* =====================================================
   MAKE SHIFT ID
===================================================== */

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


/* =====================================================
   PAD
===================================================== */

function pad(
    value
){

    return String(
        value
    ).padStart(
        2,
        '0'
    );

}


/* =====================================================
   THAI MONTH
===================================================== */

function thaiMonth(
    month
){

    const months = [

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

    ];


    return months[
        month
    ];

}


/* =====================================================
   THAI DATE
===================================================== */

function formatThaiDate(
    key
){

    const date =
        parseDateKey(
            key
        );


    const day =
        date.getDate();


    const month =
        thaiMonth(
            date.getMonth()
        );


    const year =
        date.getFullYear() +
        543;


    return `${day} ${month} ${year}`;

}


/* =====================================================
   ESCAPE HTML
===================================================== */

function escapeHTML(
    value
){

    return String(
        value ?? ''
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
   KEYBOARD
===================================================== */

document.addEventListener(
    'keydown',
    event => {

        if(
            event.key ===
            'Escape'
        ){

            closeModal();

            closePeriodsMore();

            closeSalaryPreview();

        }

    }
);

</script>

</body>

</html>
