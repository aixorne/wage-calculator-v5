<!DOCTYPE html>
<html lang="th">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Wage Calculator V1</title>

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
        WAGE CALCULATOR V1
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
        <span>💾</span>
        <h2>จัดการข้อมูล</h2>
    </div>


    <p class="data-description">

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
     DAY DETAIL MODAL
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

    <div class="modal-box salary-preview-modal-box">

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
   START
===================================================== */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        loadData();

        renderAll();

        setupFileInput();

        setupDragDrop();

    }
);


/* =====================================================
   FILE INPUT
===================================================== */

function setupFileInput(){

    imageInput.addEventListener(
        'change',
        function(){

            addFiles(
                Array.from(
                    this.files || []
                )
            );

        }
    );

}


/* =====================================================
   DRAG DROP
===================================================== */

function setupDragDrop(){

    [
        'dragenter',
        'dragover'
    ].forEach(
        eventName => {

            uploadZone.addEventListener(
                eventName,
                event => {

                    event.preventDefault();

                    event.stopPropagation();

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
                event => {

                    event.preventDefault();

                    event.stopPropagation();

                    uploadZone.classList.remove(
                        'dragover'
                    );

                }
            );

        }
    );


    uploadZone.addEventListener(
        'drop',
        event => {

            const files =
                Array.from(
                    event.dataTransfer.files || []
                ).filter(
                    file =>
                        file.type.startsWith(
                            'image/'
                        )
                );


            addFiles(
                files
            );

        }
    );

}


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
                '_' +
                file.size +
                '_' +
                file.lastModified;


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
        (file,index) => {

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
                    type="button"
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
        selectedFiles.length > 0
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
        selectedFiles.length === 0
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
                    `OCR Server Error: ${response.status}`
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


        if(
            newShifts.length === 0
        ){

            throw new Error(
                'ไม่พบวันที่หรือเวลาในรูป'
            );

        }


        mergeShifts(
            newShifts
        );


        selectedFiles =
            [];


        imageInput.value =
            '';


        renderPreviews();


        loading.textContent =
            `✅ บันทึกข้อมูล ${newShifts.length} กะเรียบร้อย`;


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
   IMAGE COMPRESS
===================================================== */

function compressImage(file){

    return new Promise(
        (resolve,reject) => {

            const img =
                new Image();


            const url =
                URL.createObjectURL(
                    file
                );


            img.onload =
                () => {

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

                        const ratio =
                            maxWidth /
                            width;


                        width =
                            maxWidth;


                        height =
                            Math.round(
                                height *
                                ratio
                            );

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

                            if(
                                !blob
                            ){

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


            img.onerror =
                () => {

                    URL.revokeObjectURL(
                        url
                    );


                    reject(
                        new Error(
                            'ไม่สามารถเปิดรูปได้'
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
   DATE PARSER
===================================================== */

function extractDate(text){

    if(
        !text
    ){

        return null;

    }


    let m;


    /* YYYY-MM-DD */

    m =
        text.match(
            /\b(20\d{2}|25\d{2})[-\/](\d{1,2})[-\/](\d{1,2})\b/
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


    /* DD/MM/YYYY */

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


    /* YYYY MM DD */

    m =
        text.match(
            /\b(20\d{2}|25\d{2})\s+(\d{1,2})\s+(\d{1,2})\b/
        );


    if(m){

        let year =
            Number(
                m[1]
            );


        if(
            year > 2400
        ){

            year -= 543;

        }


        return dateObject(
            year,
            Number(m[2]),
            Number(m[3])
        );

    }


    return null;

}


/* =====================================================
   TIME PARSER
===================================================== */

function extractTimes(text){

    const found = [];


    if(
        !text
    ){

        return found;

    }


    const regex =
        /\b([01]?\d|2[0-3])[\.:]([0-5]\d)\b/g;


    let match;


    while(
        (match = regex.exec(text)) !== null
    ){

        const hour =
            String(
                Number(
                    match[1]
                )
            ).padStart(
                2,
                '0'
            );


        const time =
            `${hour}:${match[2]}`;


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
        (a,b) =>
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
                events[pairIndex];


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

function validPair(start,end){

    const day =
        getDay(
            start.dateKey
        );


    const s =
        start.minutes;


    const e =
        end.minutes;


    /* Sunday */

    if(
        day === 0
    ){

        return (
            e > s ||
            end.dateKey !==
            start.dateKey
        );

    }


    /* Night */

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


        return e > s;

    }


    /* Day */

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

function buildShift(start,end){

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

function calculateSunday(start,end){

    let s =
        start.minutes;


    let e =
        end.minutes;


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


    e =
        Math.floor(
            e / 30
        ) * 30;


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
   DAY SHIFT
===================================================== */

function calculateDay(start,end){

    let s =
        start.minutes;


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
   NIGHT SHIFT
===================================================== */

function calculateNight(start,end){

    let s =
        start.minutes;


    let e =
        end.minutes;


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


    if(
        end.dateKey !==
        start.dateKey
        &&
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
        e >=
        otStart
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

function mergeShifts(newShifts){

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
        (a,b) =>
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

    }catch(error){

        console.error(
            error
        );


        shifts =
            [];

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
            shift =>
                !shift.incomplete
        );


    const dates =
        new Set(
            completed.map(
                shift =>
                    shift.dateKey
            )
        );


    const total =
        completed.reduce(
            (sum,shift) =>
                sum +
                Number(
                    shift.pay || 0
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
   CHANGE MONTH
===================================================== */

function changeMonth(delta){

    viewDate =
        new Date(
            viewDate.getFullYear(),
            viewDate.getMonth() + delta,
            1
        );


    renderCalendar();

}


/* =====================================================
   GET SHIFT
===================================================== */

function getShiftForDate(key){

    const list =
        shifts.filter(
            shift =>
                shift.startDateKey === key ||
                shift.dateKey === key
        );


    if(
        list.length === 0
    ){

        return null;

    }


    return list
        .sort(
            (a,b) =>
                String(a.startTime)
                    .localeCompare(
                        String(b.startTime)
                    )
        )[0];

}


/* =====================================================
   DAY STATUS
===================================================== */

function getDayStatus(key,shift){

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

            className:'future',

            label:'ยังไม่ถึง'

        };

    }


    if(
        shift
    ){

        if(
            shift.incomplete
        ){

            return {

                className:'missed',

                label:'ลืมอัป'

            };

        }


        return {

            className:'worked',

            label:'อัปแล้ว'

        };

    }


    return {

        className:'missed',

        label:'ลืมอัป'

    };

}


/* =====================================================
   DAY MODAL
===================================================== */

function openDay(key){

    const dayShifts =
        shifts.filter(
            shift =>
                shift.startDateKey === key ||
                shift.dateKey === key
        );


    modalTitle.textContent =
        `📅 ${formatThaiDate(key)}`;


    if(
        dayShifts.length === 0
    ){

        modalBody.innerHTML = `

            <div class="empty-state">
                ไม่มีข้อมูลการทำงานวันนี้
            </div>

        `;

    }else{

        modalBody.innerHTML =
            dayShifts
                .map(
                    shift =>
                        createShiftHTML(
                            shift
                        )
                )
                .join('');

    }


    dayModal.classList.add(
        'show'
    );

}


function createShiftHTML(shift){

    if(
        shift.incomplete
    ){

        return `

            <div class="shift-detail incomplete-detail">

                <div class="shift-detail-title">
                    ⚠️ พบเวลาเข้า แต่ยังจับคู่เวลาออกไม่ได้
                </div>

                <div>
                    เวลาเข้า:
                    <strong>
                        ${escapeHTML(shift.startTime)}
                    </strong>
                </div>

            </div>

        `;

    }


    return `

        <div class="shift-detail">

            <div class="shift-detail-title">

                ${shift.isSunday
                    ? '🟠 วันอาทิตย์'
                    : shift.isNight
                        ? '🌙 กะกลางคืน'
                        : '☀️ กะกลางวัน'
                }

            </div>

            <div>
                เวลา:
                <strong>
                    ${escapeHTML(shift.startTime)}
                    –
                    ${escapeHTML(shift.endTime)}
                </strong>
            </div>

            <div>
                ค่าแรงปกติ:
                <strong>
                    ${money(shift.normalPay)}
                </strong>
            </div>

            <div>
                OT:
                <strong>
                    ${Number(shift.otHours || 0).toFixed(2)}
                    ชม.
                </strong>
            </div>

            <div>
                ค่า OT:
                <strong>
                    ${money(shift.otPay)}
                </strong>
            </div>

            <div class="shift-total">
                รวม:
                <strong>
                    ${money(shift.pay)}
                </strong>
            </div>

        </div>

    `;

}


function closeModal(){

    dayModal.classList.remove(
        'show'
    );

}


function closeModalOutside(event){

    if(
        event.target === dayModal
    ){

        closeModal();

    }

}


/* =====================================================
   PAY PERIOD SYSTEM
===================================================== */

/*
    รอบ 26–10

    ตัวอย่าง:
    26/07/2026 - 10/08/2026

    รอบ 11–25

    ตัวอย่าง:
    11/08/2026 - 25/08/2026
*/


function getPayPeriod(dateKey){

    const date =
        parseDateKey(
            dateKey
        );


    const year =
        date.getFullYear();


    const month =
        date.getMonth();


    const day =
        date.getDate();


    let start;

    let end;

    let type;


    if(
        day >= 26
    ){

        type =
            '26-10';


        start =
            new Date(
                year,
                month,
                26
            );


        end =
            new Date(
                year,
                month + 1,
                10
            );

    }else if(
        day <= 10
    ){

        type =
            '26-10';


        start =
            new Date(
                year,
                month - 1,
                26
            );


        end =
            new Date(
                year,
                month,
                10
            );

    }else{

        type =
            '11-25';


        start =
            new Date(
                year,
                month,
                11
            );


        end =
            new Date(
                year,
                month,
                25
            );

    }


    const key =
        `${formatDateKey(start)}_${formatDateKey(end)}`;


    return {

        key,

        type,

        startDate:
            formatDateKey(start),

        endDate:
            formatDateKey(end),

        name:
            `${formatThaiDate(formatDateKey(start))} – ${formatThaiDate(formatDateKey(end))}`

    };

}


/* =====================================================
   BUILD ALL PERIODS
===================================================== */

function getAllPayPeriods(){

    const map =
        new Map();


    shifts.forEach(
        shift => {

            const period =
                getPayPeriod(
                    shift.startDateKey ||
                    shift.dateKey
                );


            if(
                !map.has(
                    period.key
                )
            ){

                map.set(
                    period.key,
                    period
                );

            }

        }
    );


    return Array.from(
        map.values()
    ).sort(
        (a,b) =>
            b.startDate.localeCompare(
                a.startDate
            )
    );

}


/* =====================================================
   PERIOD DATA
===================================================== */

function getPeriodShifts(period){

    return shifts.filter(
        shift => {

            const date =
                shift.startDateKey ||
                shift.dateKey;


            return (
                date >= period.startDate &&
                date <= period.endDate
            );

        }
    );

}


function getPeriodSummary(period){

    const periodShifts =
        getPeriodShifts(
            period
        );


    const completed =
        periodShifts.filter(
            shift =>
                !shift.incomplete
        );


    const workDays =
        new Set(
            completed.map(
                shift =>
                    shift.startDateKey ||
                    shift.dateKey
            )
        ).size;


    const wage =
        completed.reduce(
            (sum,shift) =>
                sum +
                Number(
                    shift.normalPay || 0
                ),
            0
        );


    const ot =
        completed.reduce(
            (sum,shift) =>
                sum +
                Number(
                    shift.otPay || 0
                ),
            0
        );


    const total =
        completed.reduce(
            (sum,shift) =>
                sum +
                Number(
                    shift.pay || 0
                ),
            0
        );


    return {

        period,

        shifts:
            periodShifts,

        completed,

        workDays,

        wage,

        ot,

        total

    };

}


/* =====================================================
   RENDER PERIODS
===================================================== */

function renderPeriods(){

    const allPeriods =
        getAllPayPeriods();


    if(
        allPeriods.length === 0
    ){

        periods.innerHTML = `

            <div class="empty-state">
                ยังไม่มีข้อมูลรอบจ่าย
            </div>

        `;

        return;

    }


    const visible =
        allPeriods.slice(
            0,
            2
        );


    let html =
        visible
            .map(
                period =>
                    renderPeriodCard(
                        period
                    )
            )
            .join('');


    if(
        allPeriods.length > 2
    ){

        html += `

            <button
                type="button"
                class="more-periods-btn"
                onclick="openPeriodsMore()"
            >
                📋 ดูเพิ่มเติม
                <span>
                    (${allPeriods.length - 2} รอบ)
                </span>
            </button>

        `;

    }


    periods.innerHTML =
        html;

}


/* =====================================================
   PERIOD CARD
===================================================== */

function renderPeriodCard(period){

    const data =
        getPeriodSummary(
            period
        );


    return `

        <div class="period-card">

            <div class="period-card-header">

                <div>

                    <div class="period-type">
                        รอบ ${period.type}
                    </div>

                    <div class="period-name">
                        ${escapeHTML(period.name)}
                    </div>

                </div>

                <div class="period-total">
                    ${money(data.total)}
                </div>

            </div>


            <div class="period-stats">

                <div>
                    <span>วันทำงาน</span>
                    <strong>
                        ${data.workDays} วัน
                    </strong>
                </div>


                <div>
                    <span>ค่าแรง</span>
                    <strong>
                        ${money(data.wage)}
                    </strong>
                </div>


                <div>
                    <span>OT</span>
                    <strong>
                        ${money(data.ot)}
                    </strong>
                </div>

            </div>

        </div>

    `;

}


/* =====================================================
   ALL PERIODS MODAL
===================================================== */

function openPeriodsMore(){

    const allPeriods =
        getAllPayPeriods();


    const allPeriodsList =
        document.getElementById(
            'allPeriodsList'
        );


    allPeriodsList.innerHTML =
        allPeriods.length
            ? allPeriods
                .map(
                    period =>
                        renderPeriodCard(
                            period
                        )
                )
                .join('')
            : `

                <div class="empty-state">
                    ยังไม่มีรอบจ่าย
                </div>

            `;


    document
        .getElementById(
            'periodsMoreModal'
        )
        .classList.add(
            'show'
        );

}


function closePeriodsMore(){

    document
        .getElementById(
            'periodsMoreModal'
        )
        .classList.remove(
            'show'
        );

}


function closePeriodsMoreOutside(event){

    if(
        event.target.id ===
        'periodsMoreModal'
    ){

        closePeriodsMore();

    }

}


/* =====================================================
   SALARY PERIOD SELECT
===================================================== */

function renderSalaryPeriods(){

    const allPeriods =
        getAllPayPeriods();


    const currentValue =
        salaryPeriod.value;


    salaryPeriod.innerHTML = `

        <option value="">
            -- เลือกรอบจ่าย --
        </option>

    `;


    allPeriods.forEach(
        period => {

            const option =
                document.createElement(
                    'option'
                );


            option.value =
                period.key;


            option.textContent =
                `รอบ ${period.type} • ${period.name}`;


            salaryPeriod.appendChild(
                option
            );

        }
    );


    if(
        allPeriods.some(
            period =>
                period.key ===
                currentValue
        )
    ){

        salaryPeriod.value =
            currentValue;

    }else if(
        allPeriods.length > 0
    ){

        salaryPeriod.value =
            allPeriods[0].key;

    }

}


/* =====================================================
   GET SELECTED SALARY DATA
===================================================== */

function getSelectedSalaryData(){

    const key =
        salaryPeriod.value;


    if(
        !key
    ){

        throw new Error(
            'กรุณาเลือกรอบจ่ายก่อน'
        );

    }


    const period =
        getAllPayPeriods()
            .find(
                item =>
                    item.key ===
                    key
            );


    if(
        !period
    ){

        throw new Error(
            'ไม่พบข้อมูลรอบจ่าย'
        );

    }


    const summary =
        getPeriodSummary(
            period
        );


    const room =
        Math.max(
            0,
            Number(
                document.getElementById(
                    'roomAllowance'
                ).value || 0
            )
        );


    const rate =
        Math.min(
            100,
            Math.max(
                0,
                Number(
                    document.getElementById(
                        'socialSecurityRate'
                    ).value || 0
                )
            )
        );


    const beforeDeduction =
        summary.total +
        room;


    const socialSecurity =
        beforeDeduction *
        rate /
        100;


    const total =
        Math.max(
            0,
            beforeDeduction -
            socialSecurity
        );


    return {

        period,

        name:
            document.getElementById(
                'employeeName'
            ).value.trim() ||
            '-',

        employeeId:
            document.getElementById(
                'employeeId'
            ).value.trim() ||
            '-',

        workDays:
            summary.workDays,

        wage:
            summary.wage,

        ot:
            summary.ot,

        room,

        rate,

        beforeDeduction,

        socialSecurity,

        total

    };

}


/* =====================================================
   SALARY PREVIEW
===================================================== */

function previewSalarySlip(){

    try{

        const data =
            getSelectedSalaryData();


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
                    ${escapeHTML(data.period.name)}
                </div>


                <div class="salary-preview-info">

                    <div>
                        <span>ชื่อพนักงาน</span>
                        <strong>
                            ${escapeHTML(data.name)}
                        </strong>
                    </div>

                    <div>
                        <span>รหัสพนักงาน</span>
                        <strong>
                            ${escapeHTML(data.employeeId)}
                        </strong>
                    </div>

                    <div>
                        <span>วันทำงาน</span>
                        <strong>
                            ${data.workDays} วัน
                        </strong>
                    </div>

                </div>


                <div class="salary-preview-table">

                    <div>
                        <span>ค่าแรง</span>
                        <strong>
                            ${money(data.wage)}
                        </strong>
                    </div>

                    <div>
                        <span>OT</span>
                        <strong>
                            ${money(data.ot)}
                        </strong>
                    </div>

                    <div>
                        <span>ค่าห้องเย็น</span>
                        <strong>
                            ${money(data.room)}
                        </strong>
                    </div>

                    <div class="gross-row">
                        <span>รวมก่อนหัก</span>
                        <strong>
                            ${money(data.beforeDeduction)}
                        </strong>
                    </div>

                    <div>
                        <span>
                            ประกันสังคม ${data.rate}%
                        </span>

                        <strong>
                            - ${money(data.socialSecurity)}
                        </strong>
                    </div>

                </div>


                <div class="salary-net-preview">

                    <span>
                        เงินรับสุทธิ
                    </span>

                    <strong>
                        ฿ ${formatNumber(data.total)}
                    </strong>

                </div>

            </div>

        `;


        document
            .getElementById(
                'salaryPreviewModal'
            )
            .classList.add(
                'show'
            );

    }catch(error){

        alert(
            error.message
        );

    }

}


function closeSalaryPreview(){

    document
        .getElementById(
            'salaryPreviewModal'
        )
        .classList.remove(
            'show'
        );

}


function closeSalaryPreviewOutside(event){

    if(
        event.target.id ===
        'salaryPreviewModal'
    ){

        closeSalaryPreview();

    }

}


/* =====================================================
   PDF FONT
===================================================== */

async function loadThaiFont(doc){

    const response =
        await fetch(
            'NotoSansThai-Regular.ttf'
        );


    if(
        !response.ok
    ){

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


    let binary =
        '';


    const chunkSize =
        0x8000;


    for(
        let i = 0;
        i < bytes.length;
        i += chunkSize
    ){

        binary +=
            String.fromCharCode(
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
        btoa(
            binary
        );


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
   GENERATE SALARY PDF
===================================================== */

async function generateSalaryPDF(){

    try{

        if(
            !window.jspdf
        ){

            throw new Error(
                'ไม่พบ jsPDF'
            );

        }


        const data =
            getSelectedSalaryData();


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


        await loadThaiFont(
            doc
        );


        const pageWidth =
            doc.internal.pageSize.getWidth();


        const pageHeight =
            doc.internal.pageSize.getHeight();


        const margin =
            16;


        const right =
            pageWidth -
            margin;


        const contentWidth =
            pageWidth -
            margin * 2;


        let y =
            18;


        /* =================================================
           HEADER
        ================================================= */

        doc.setFont(
            'NotoSansThai',
            'normal'
        );


        doc.setFontSize(
            20
        );


        doc.setTextColor(
            15,
            23,
            42
        );


        doc.text(
            'สลิปเงินเดือน',
            margin,
            y
        );


        doc.setFontSize(
            8
        );


        doc.setTextColor(
            100,
            116,
            139
        );


        doc.text(
            'SALARY SLIP',
            margin,
            y + 7
        );


        doc.setFontSize(
            9
        );


        doc.text(
            'รอบจ่าย',
            right - 48,
            y,
            {
                align:'left'
            }
        );


        doc.setTextColor(
            30,
            41,
            59
        );


        doc.text(
            data.period.name,
            right,
            y + 6,
            {
                align:'right'
            }
        );


        y +=
            18;


        /* =================================================
           EMPLOYEE INFO BOX
        ================================================= */

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


        /* CENTER DIVIDER */

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


        const leftCenter =
            margin +
            colWidth / 2;


        const rightCenter =
            margin +
            colWidth +
            colWidth / 2;


        const labelY =
            y + 9;


        const valueY =
            y + 18;


        /* LEFT */

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


        if(
            String(
                data.name || ''
            ).length > 22
        ){

            nameFontSize =
                8.5;

        }else if(
            String(
                data.name || ''
            ).length > 17
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
            data.name || '-',
            leftCenter,
            valueY,
            {
                align:'center'
            }
        );


        /* RIGHT */

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
            data.employeeId || '-',
            rightCenter,
            valueY,
            {
                align:'center'
            }
        );


        /* BOTTOM */

        const bottomLabelY =
            y + 26;


        const bottomValueY =
            y + 31;


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
            formatThaiDate(
                todayKey
            ),
            rightCenter,
            bottomValueY,
            {
                align:'center'
            }
        );


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


        y += 5;


        doc.autoTable({

            startY:y,

            margin:{
                left:margin,
                right:margin
            },

            theme:'grid',

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
                ]

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
                    'normal'

            },

            columnStyles:{

                0:{
                    halign:'left',
                    cellWidth:
                        contentWidth * .65
                },

                1:{
                    halign:'right'
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
                    'ค่าล่วงเวลา (OT)',
                    formatNumber(
                        data.ot
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


        y += 22;


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


        y += 5;


        doc.autoTable({

            startY:y,

            margin:{
                left:margin,
                right:margin
            },

            theme:'grid',

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
                ]

            },

            columnStyles:{

                0:{
                    halign:'left',
                    cellWidth:
                        contentWidth * .65
                },

                1:{
                    halign:'right'
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
            y + 16,
            {
                align:'right'
            }
        );


        y += 42;


        /* =================================================
           FOOTER
        ================================================= */

        doc.setDrawColor(
            226,
            232,
            240
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

        const filename =
            'SalarySlip_' +
            data.period.name
                .replace(
                    /[\/\\:*?"<>| ]/g,
                    '_'
                ) +
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
            'สร้าง PDF ไม่สำเร็จ\n\n' +
            error.message
        );

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


    const date =
        input.value;


    if(
        !date
    ){

        alert(
            'กรุณาเลือกวันที่ก่อน'
        );

        return;

    }


    const target =
        shifts.filter(
            shift =>
                (
                    shift.startDateKey ||
                    shift.dateKey
                ) === date
        );


    if(
        target.length === 0
    ){

        alert(
            `ไม่พบข้อมูลของวันที่ ${formatThaiDate(date)}`
        );

        return;

    }


    const ok =
        confirm(
            `ต้องการลบข้อมูลวันที่ ${formatThaiDate(date)} จำนวน ${target.length} กะหรือไม่?`
        );


    if(
        !ok
    ){

        return;

    }


    shifts =
        shifts.filter(
            shift =>
                (
                    shift.startDateKey ||
                    shift.dateKey
                ) !== date
        );


    saveData();

    renderAll();


    alert(
        'ลบข้อมูลเรียบร้อยแล้ว'
    );


    input.value =
        '';

}


/* =====================================================
   UTILITIES
===================================================== */

function pad(number){

    return String(
        number
    ).padStart(
        2,
        '0'
    );

}


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


function parseDateKey(key){

    const [
        year,
        month,
        day
    ] =
        key
            .split('-')
            .map(Number);


    return new Date(
        year,
        month - 1,
        day
    );

}


function formatDateKey(date){

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;

}


function timestamp(
    dateKey,
    time
){

    const date =
        parseDateKey(
            dateKey
        );


    const [
        hour,
        minute
    ] =
        time
            .split(':')
            .map(Number);


    date.setHours(
        hour,
        minute,
        0,
        0
    );


    return date.getTime();

}


function toMinutes(time){

    const [
        hour,
        minute
    ] =
        time
            .split(':')
            .map(Number);


    return (
        hour * 60 +
        minute
    );

}


function getDay(key){

    return parseDateKey(
        key
    ).getDay();

}


function startOfDay(date){

    return new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate()
    );

}


function thaiMonth(month){

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


function formatThaiDate(key){

    const date =
        parseDateKey(
            key
        );


    return `${date.getDate()} ${thaiMonth(date.getMonth())} ${date.getFullYear() + 543}`;

}


function money(value){

    return (
        '฿' +
        Number(
            value || 0
        ).toLocaleString(
            'en-US',
            {
                minimumFractionDigits:2,
                maximumFractionDigits:2
            }
        )
    );

}


function formatNumber(value){

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


function makeId(start,end){

    return [
        start.dateKey,
        start.time,
        end.dateKey,
        end.time
    ].join('_');

}


function escapeHTML(value){

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
   ESC KEY FOR MODALS
===================================================== */

document.addEventListener(
    'keydown',
    event => {

        if(
            event.key !== 'Escape'
        ){

            return;

        }


        closeModal();

        closePeriodsMore();

        closeSalaryPreview();

    }
);

</script>

</body>

</html>
