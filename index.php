<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>ระบบคำนวณค่าแรง V5.3</title>

<link rel="stylesheet" href="style.css">

<style>
.calendar-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.calendar-header button{
    width:auto;
    min-width:50px;
    padding:8px 14px;
}

.calendar-title{
    font-size:20px;
    font-weight:bold;
}

.calendar{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:5px;
}

.calendar-day-name{
    text-align:center;
    font-weight:bold;
    padding:8px 2px;
    font-size:13px;
}

.calendar-cell{
    min-height:85px;
    border:1px solid #ddd;
    border-radius:10px;
    padding:7px;
    background:#fff;
    overflow:hidden;
    cursor:pointer;
    transition:.15s;
}

.calendar-cell:hover{
    transform:scale(1.02);
    border-color:#2196f3;
}

.calendar-cell.empty{
    background:#f5f5f5;
    border:none;
    cursor:default;
}

.calendar-cell.today{
    border:2px solid #2196f3;
}

.calendar-date{
    font-weight:bold;
    font-size:14px;
}

.calendar-money{
    margin-top:7px;
    font-size:13px;
    font-weight:bold;
}

.calendar-time{
    font-size:11px;
    margin-top:4px;
    color:#555;
}

.shift-card{
    border:1px solid #ddd;
    border-radius:12px;
    padding:15px;
    margin-bottom:12px;
    background:#fff;
}

.shift-total{
    font-size:20px;
    font-weight:bold;
    margin-top:12px;
}

.pay-period{
    border:1px solid #ddd;
    border-radius:14px;
    padding:17px;
    margin-bottom:15px;
    background:#fff;
}

.pay-period h3{
    margin-top:0;
}

.pay-period-total{
    font-size:24px;
    font-weight:bold;
    margin-top:10px;
}

.error-box{
    padding:12px;
    border-radius:10px;
    background:#fff0f0;
    border:1px solid #ffcccc;
    margin-top:10px;
}

.loading{
    margin-top:10px;
}
</style>
</head>

<body>

<div class="container">

<h1>💰 ระบบคำนวณค่าแรง V5.3</h1>

<p class="subtitle">
OCR + กะกลางวัน + กะดึก + ปฏิทิน + รอบจ่าย
</p>


<!-- =========================
     UPLOAD
========================= -->

<div class="card">

<h2>📷 อัปโหลดรูป</h2>

<p>
เลือกภาพเวลาเข้าและออกงานทั้งหมด
</p>

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
⏳ พร้อมทำงาน
</div>

</div>


<!-- =========================
     กฎค่าแรง
========================= -->

<div class="card">

<h2>⚙️ กฎค่าแรง</h2>

<div>
☀️ กะกลางวัน:
<b>08:00–17:00</b>
</div>

<div style="margin-top:7px;">
🌙 กะดึก:
<b>20:00–05:00</b>
</div>

<div style="margin-top:7px;">
จันทร์–เสาร์:
<b>352 บาท/วัน</b>
+
<b>OT 66 บาท/ชม.</b>
</div>

<div style="margin-top:7px;">
อาทิตย์:
<b>88 บาท/ชม.</b>
</div>

<div style="margin-top:7px;">
⏱️ OT กลางวันเริ่ม 17:30
</div>

<div style="margin-top:7px;">
⏱️ OT กะดึกเริ่ม 05:30
</div>

<div style="margin-top:7px;">
🔢 เวลาปัดลงทุก 30 นาที
</div>

</div>


<!-- =========================
     CALENDAR
========================= -->

<div class="card">

<h2>📅 ปฏิทินค่าแรง</h2>

<div class="calendar-header">

<button onclick="changeMonth(-1)">
◀
</button>

<div
    id="calendarTitle"
    class="calendar-title"
>
</div>

<button onclick="changeMonth(1)">
▶
</button>

</div>

<div
    id="calendar"
    class="calendar"
></div>

<p style="font-size:12px;color:#777;">
👆 กดวันที่เพื่อดูรายละเอียดค่าแรง
</p>

</div>


<!-- =========================
     รอบจ่าย
========================= -->

<div class="card">

<h2>💰 รอบจ่าย</h2>

<div id="periods">
ยังไม่มีข้อมูล
</div>

</div>


<!-- =========================
     รายละเอียด
========================= -->

<div class="card">

<h2>📋 รายละเอียดงาน</h2>

<div id="details">
ยังไม่มีข้อมูล
</div>

</div>


<!-- =========================
     OCR
========================= -->

<div
    id="resultsCard"
    class="card"
    style="display:none;"
>

<h2>📝 ผล OCR</h2>

<div id="results"></div>

</div>

</div>


<script>

/* =====================================================
   ค่าแรง
===================================================== */

const NORMAL_DAILY_WAGE = 352;

const OT_RATE = 66;

const SUNDAY_RATE = 88;


/* =====================================================
   เวลา
===================================================== */

const DAY_START = 8 * 60;

const DAY_END = 17 * 60;

const DAY_OT_START = 17 * 60 + 30;

const NIGHT_START = 20 * 60;

const NIGHT_END = 5 * 60;

const NIGHT_OT_START = 5 * 60 + 30;


/* =====================================================
   DATA
===================================================== */

let shifts = [];

let ocrRecords = [];

let calendarDate = new Date();


/* =====================================================
   ELEMENTS
===================================================== */

const imageInput =
    document.getElementById('imageInput');

const fileList =
    document.getElementById('fileList');

const loading =
    document.getElementById('loading');

const ocrButton =
    document.getElementById('ocrButton');

const results =
    document.getElementById('results');

const resultsCard =
    document.getElementById('resultsCard');

const calendar =
    document.getElementById('calendar');

const calendarTitle =
    document.getElementById('calendarTitle');

const periods =
    document.getElementById('periods');

const details =
    document.getElementById('details');


/* =====================================================
   รายชื่อไฟล์
===================================================== */

imageInput.addEventListener(
    'change',
    function(){

        fileList.innerHTML = '';

        const files =
            Array.from(this.files);

        files.forEach(
            (file,index)=>{

                const div =
                    document.createElement('div');

                div.style.padding = '6px 0';

                div.innerHTML =
                    `📷 ${index+1}.
                    ${escapeHTML(file.name)}
                    <small>
                    (${formatBytes(file.size)})
                    </small>`;

                fileList.appendChild(div);
            }
        );
    }
);


/* =====================================================
   OCR
===================================================== */

async function runAllOCR(){

    const files =
        Array.from(imageInput.files);

    if(!files.length){

        alert('กรุณาเลือกรูปก่อน');

        return;
    }


    ocrButton.disabled = true;

    loading.style.display = 'block';

    results.innerHTML = '';

    resultsCard.style.display = 'none';

    ocrRecords = [];


    try{

        for(
            let i=0;
            i<files.length;
            i++
        ){

            loading.textContent =
                `⏳ OCR รูป ${i+1} / ${files.length}`;


            const compressed =
                await compressImage(files[i]);


            const formData =
                new FormData();


            formData.append(
                'image',
                compressed,
                `ocr-${i+1}.jpg`
            );


            const response =
                await fetch(
                    'ocr.php',
                    {
                        method:'POST',
                        body:formData
                    }
                );


            const data =
                await response.json();


            if(!data.success){

                throw new Error(
                    `รูปที่ ${i+1}: ${
                        data.error ||
                        'OCR ไม่สำเร็จ'
                    }`
                );
            }


            const text =
                data.text || '';


            const times =
                extractTimes(text);


            const date =
                extractDate(text);


            ocrRecords.push({

                index:i,

                fileName:files[i].name,

                text:text,

                times:times,

                date:date
            });
        }


        showOCR();

        buildShifts();

        renderCalendar();

        renderPeriods();

        renderDetails();


    }catch(error){

        console.error(error);

        alert(error.message);

    }finally{

        loading.style.display = 'none';

        loading.textContent =
            '⏳ พร้อมทำงาน';

        ocrButton.disabled = false;
    }
}


/* =====================================================
   อ่านเวลา
===================================================== */

function extractTimes(text){

    const regex =
        /\b([01]?\d|2[0-3])[\.:]([0-5]\d)\b/g;

    const result = [];

    let match;


    while(
        (match = regex.exec(text)) !== null
    ){

        const hour =
            String(
                parseInt(match[1],10)
            ).padStart(2,'0');


        result.push(
            `${hour}:${match[2]}`
        );
    }


    return [...new Set(result)];
}


/* =====================================================
   อ่านวันที่
   รองรับ YYYY-MM-DD
   และ DD/MM/YYYY
===================================================== */

function extractDate(text){

    let match;


    /* YYYY-MM-DD */

    match =
        text.match(
            /\b(20\d{2})[-\/](0?[1-9]|1[0-2])[-\/](0?[1-9]|[12]\d|3[01])\b/
        );


    if(match){

        const year =
            parseInt(match[1],10);

        const month =
            parseInt(match[2],10);

        const day =
            parseInt(match[3],10);


        return makeDateObject(
            year,
            month,
            day
        );
    }


    /* DD/MM/YYYY */

    match =
        text.match(
            /\b(0?[1-9]|[12]\d|3[01])[-\/](0?[1-9]|1[0-2])[-\/](\d{2,4})\b/
        );


    if(!match){

        return null;
    }


    const day =
        parseInt(match[1],10);

    const month =
        parseInt(match[2],10);

    let year =
        parseInt(match[3],10);


    if(year < 100){

        year += 2000;
    }


    if(year > 2400){

        year -= 543;
    }


    return makeDateObject(
        year,
        month,
        day
    );
}


/* =====================================================
   สร้าง Object วันที่
===================================================== */

function makeDateObject(
    year,
    month,
    day
){

    return {

        year:year,

        month:month,

        day:day,

        key:
            `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`,

        display:
            `${String(day).padStart(2,'0')}/${String(month).padStart(2,'0')}/${year}`
    };
}


/* =====================================================
   แสดง OCR
===================================================== */

function showOCR(){

    results.innerHTML = '';


    ocrRecords.forEach(
        (record,index)=>{

            const div =
                document.createElement('div');

            div.className =
                'shift-card';


            div.innerHTML = `

                <b>📷 รูป ${index+1}</b>

                <div>
                    📁
                    ${escapeHTML(record.fileName)}
                </div>

                <div>
                    📅
                    ${
                        record.date
                        ? record.date.display
                        : 'ไม่พบวันที่'
                    }
                </div>

                <div>
                    ⏰
                    ${
                        record.times.length
                        ? record.times.join(', ')
                        : 'ไม่พบเวลา'
                    }
                </div>

                <details>

                    <summary>
                        ดู OCR
                    </summary>

                    <textarea
                        readonly
                        style="
                        width:100%;
                        min-height:120px;
                        margin-top:10px;
                        "
                    >${escapeHTML(record.text)}</textarea>

                </details>
            `;


            results.appendChild(div);
        }
    );


    resultsCard.style.display = 'block';
}


/* =====================================================
   สร้าง Event
===================================================== */

function buildShifts(){

    const events = [];


    ocrRecords.forEach(
        record=>{

            if(!record.date){

                return;
            }


            record.times.forEach(
                time=>{

                    const minutes =
                        timeToMinutes(time);


                    events.push({

                        dateKey:
                            record.date.key,

                        year:
                            record.date.year,

                        month:
                            record.date.month,

                        day:
                            record.date.day,

                        time:time,

                        minutes:minutes,

                        fileName:
                            record.fileName
                    });
                }
            );
        }
    );


    events.forEach(
        event=>{

            event.timestamp =
                new Date(
                    `${event.dateKey}T${event.time}:00`
                ).getTime();
        }
    );


    events.sort(
        (a,b)=>
            a.timestamp -
            b.timestamp
    );


    shifts = [];


    let i = 0;


    while(i < events.length){

        const start =
            events[i];


        let end = null;


        if(i + 1 < events.length){

            const next =
                events[i+1];


            const diff =
                (
                    next.timestamp -
                    start.timestamp
                ) / 60000;


            if(
                diff > 0 &&
                diff <= 16 * 60 &&
                isValidShiftPair(
                    start,
                    next
                )
            ){

                end = next;

                i += 2;

            }else{

                i += 1;
            }

        }else{

            i += 1;
        }


        if(!end){

            shifts.push({

                start:start,

                end:null,

                incomplete:true,

                pay:0
            });

            continue;
        }


        const calculation =
            calculateShift(
                start,
                end
            );


        shifts.push({

            start:start,

            end:end,

            incomplete:false,

            ...calculation
        });
    }


    shifts.sort(
        (a,b)=>
            a.start.timestamp -
            b.start.timestamp
    );
}


/* =====================================================
   ตรวจคู่เข้าออก
===================================================== */

function isValidShiftPair(start, end){

    const s = start.minutes;
    const e = end.minutes;

    const date = new Date(
        `${start.dateKey}T12:00:00`
    );

    const dayOfWeek = date.getDay();


    /*
     * ==========================================
     * วันอาทิตย์
     *
     * วันอาทิตย์เป็นรายชั่วโมง
     * สามารถเริ่มงานช่วงบ่าย/เย็นได้
     *
     * ตัวอย่าง:
     * 16:53 -> 18:08
     * ==========================================
     */

    if(dayOfWeek === 0){

        return (
            start.dateKey === end.dateKey &&
            e > s &&
            (e - s) <= 16 * 60
        );
    }


    /*
     * ==========================================
     * กะกลางวัน จันทร์-เสาร์
     *
     * 07:30-08:00 -> 08:00
     * ==========================================
     */

    if(
        s >= 7 * 60 + 30 &&
        s <= 12 * 60
    ){

        return (
            start.dateKey === end.dateKey &&
            e >= 16 * 60
        );
    }


    /*
     * ==========================================
     * กะดึก จันทร์-เสาร์
     *
     * เช่น
     *
     * 08/08 20:00
     * 09/08 08:00
     *
     * ==========================================
     */

    if(s >= 19 * 60){

        /*
         * ออกวันถัดไป
         */

        if(
            start.dateKey !==
            end.dateKey
        ){

            return (
                e <= 12 * 60
            );
        }


        /*
         * กันกรณี OCR อ่านวันที่ออกผิด
         * แต่เวลาออกยังเป็นเช้าวันถัดไป
         */

        return false;
    }


    return false;
}


/* =====================================================
   คำนวณค่าแรง
===================================================== */

function calculateShift(
    start,
    end
){

    const startMinutes =
        start.minutes;

    const endMinutes =
        end.minutes;


    const date =
        new Date(
            `${start.dateKey}T12:00:00`
        );


    const dayOfWeek =
        date.getDay();


    const isSunday =
        dayOfWeek === 0;


    /*
     * ==========================================
     * วันอาทิตย์
     *
     * 88 บาท / ชั่วโมง
     * ==========================================
     */

    if(isSunday){

        let adjustedStart =
            startMinutes;


        let adjustedEnd =
            endMinutes;


        /*
         * เวลาเข้า 16:53
         * -> 17:00
         *
         * รวมช่วง 16:30-17:00
         */

        if(
            adjustedStart >=
            16 * 60 + 30 &&
            adjustedStart <=
            17 * 60
        ){

            adjustedStart =
                17 * 60;
        }


        /*
         * ปัดเวลาออกลง 30 นาที
         *
         * 18:08 -> 18:00
         * 18:29 -> 18:00
         * 18:30 -> 18:30
         */

        adjustedEnd =
            Math.floor(
                adjustedEnd / 30
            ) * 30;


        const totalMinutes =
            Math.max(
                0,
                adjustedEnd -
                adjustedStart
            );


        const pay =
            (
                totalMinutes / 60
            ) * SUNDAY_RATE;


        return {

            dayName:
                getThaiDay(dayOfWeek),

            isSunday:true,

            isNight:false,

            adjustedStart:
                adjustedStart,

            adjustedEnd:
                adjustedEnd,

            normalMinutes:
                totalMinutes,

            otMinutes:0,

            normalPay:pay,

            otPay:0,

            pay:pay
        };
    }


    /*
     * ==========================================
     * จันทร์-เสาร์
     * ==========================================
     */

    const isNight =
        startMinutes >=
        19 * 60;


    let adjustedStart =
        startMinutes;

    let normalMinutes = 0;

    let otMinutes = 0;


    /*
     * กะดึก
     */

    if(isNight){

        /*
         * 19:30-20:00
         * -> 20:00
         */

        if(
            adjustedStart >=
            19 * 60 + 30 &&
            adjustedStart <=
            20 * 60
        ){

            adjustedStart =
                20 * 60;
        }


        let endContinuous =
            endMinutes;


        /*
         * 01:00-12:00
         * ให้เป็นวันถัดไป
         */

        if(
            endContinuous <
            12 * 60
        ){

            endContinuous +=
                24 * 60;
        }


        /*
         * ปกติถึง 05:00
         */

        const normalEnd =
            29 * 60;


        normalMinutes =
            Math.max(
                0,
                Math.min(
                    endContinuous,
                    normalEnd
                ) -
                adjustedStart
            );


        /*
         * OT 05:30
         */

        const otStart =
            29 * 60 + 30;


        if(
            endContinuous >=
            otStart
        ){

            const roundedOut =
                Math.floor(
                    endContinuous / 30
                ) * 30;


            otMinutes =
                Math.max(
                    0,
                    roundedOut -
                    otStart
                );
        }

    }else{

        /*
         * กะกลางวัน
         *
         * 07:30-08:00
         * -> 08:00
         */

        if(
            adjustedStart >=
            7 * 60 + 30 &&
            adjustedStart <=
            8 * 60
        ){

            adjustedStart =
                8 * 60;
        }


        /*
         * ปกติ 08:00-17:00
         */

        normalMinutes =
            Math.max(
                0,
                DAY_END -
                adjustedStart
            );


        /*
         * OT เริ่ม 17:30
         */

        if(
            endMinutes >=
            DAY_OT_START
        ){

            const roundedOut =
                Math.floor(
                    endMinutes / 30
                ) * 30;


            otMinutes =
                Math.max(
                    0,
                    roundedOut -
                    DAY_OT_START
                );
        }
    }


    const normalPay =
        NORMAL_DAILY_WAGE;


    const otPay =
        (
            otMinutes / 60
        ) * OT_RATE;


    return {

        dayName:
            getThaiDay(dayOfWeek),

        isSunday:false,

        isNight:isNight,

        adjustedStart:
            adjustedStart,

        normalMinutes:
            normalMinutes,

        otMinutes:
            otMinutes,

        normalPay:
            normalPay,

        otPay:
            otPay,

        pay:
            normalPay +
            otPay
    };
}


/* =====================================================
   ปฏิทิน
===================================================== */

function renderCalendar(){

    const year =
        calendarDate.getFullYear();

    const month =
        calendarDate.getMonth();


    calendarTitle.textContent =
        `${getThaiMonth(month)} ${year + 543}`;


    calendar.innerHTML = '';


    const names = [
        'อา',
        'จ',
        'อ',
        'พ',
        'พฤ',
        'ศ',
        'ส'
    ];


    names.forEach(
        name=>{

            const div =
                document.createElement('div');

            div.className =
                'calendar-day-name';

            div.textContent =
                name;

            calendar.appendChild(div);
        }
    );


    const firstDay =
        new Date(
            year,
            month,
            1
        ).getDay();


    const daysInMonth =
        new Date(
            year,
            month + 1,
            0
        ).getDate();


    for(
        let i=0;
        i<firstDay;
        i++
    ){

        const empty =
            document.createElement('div');

        empty.className =
            'calendar-cell empty';

        calendar.appendChild(empty);
    }


    for(
        let day=1;
        day<=daysInMonth;
        day++
    ){

        const cell =
            document.createElement('div');


        cell.className =
            'calendar-cell';


        const key =
            `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;


        const today =
            new Date();


        if(
            today.getFullYear() === year &&
            today.getMonth() === month &&
            today.getDate() === day
        ){

            cell.classList.add('today');
        }


        const shift =
            shifts.find(
                s =>
                    s.start.dateKey === key
            );


        let html = `

            <div class="calendar-date">
                ${day}
            </div>

        `;


        if(shift){

            if(shift.incomplete){

                html += `

                    <div class="calendar-time">
                        ⚠️ รูปไม่ครบ
                    </div>

                `;

            }else{

                html += `

                    <div class="calendar-time">

                        ${
                            shift.isNight
                            ? '🌙'
                            : '☀️'
                        }

                        ${shift.start.time}
                        →
                        ${shift.end.time}

                    </div>

                    <div class="calendar-money">

                        ฿${formatMoney(
                            shift.pay
                        )}

                    </div>

                `;
            }
        }


        cell.innerHTML = html;


        /*
         * กดวันที่
         */

        cell.addEventListener(
            'click',
            ()=>{
                showDayDetail(key);
            }
        );


        calendar.appendChild(cell);
    }
}


/* =====================================================
   กดเปลี่ยนเดือน
===================================================== */

function changeMonth(amount){

    calendarDate.setMonth(
        calendarDate.getMonth() +
        amount
    );

    renderCalendar();
}


/* =====================================================
   แสดงรายละเอียดวันที่กด
===================================================== */

function showDayDetail(
    dateKey
){

    const shift =
        shifts.find(
            s =>
                s.start.dateKey ===
                dateKey
        );


    details.scrollIntoView({
        behavior:'smooth',
        block:'start'
    });


    /*
     * ไม่มีงาน
     */

    if(!shift){

        details.innerHTML = `

            <div class="shift-card">

                <h3>
                    📅
                    ${formatDateKey(dateKey)}
                </h3>

                <p>
                    ไม่มีข้อมูลการทำงานวันนี้
                </p>

            </div>

        `;

        return;
    }


    /*
     * รูปไม่ครบ
     */

    if(shift.incomplete){

        details.innerHTML = `

            <div class="shift-card">

                <h3>
                    📅
                    ${formatDateKey(dateKey)}
                </h3>

                <div>
                    🟢 เข้า:
                    <b>
                        ${shift.start.time}
                    </b>
                </div>

                <div class="error-box">
                    ⚠️
                    ยังไม่พบเวลาออกงาน
                </div>

            </div>

        `;

        return;
    }


    const date =
        new Date(
            `${dateKey}T12:00:00`
        );


    const dayName =
        getThaiDay(
            date.getDay()
        );


    let content = '';


    /*
     * วันอาทิตย์
     */

    if(shift.isSunday){

        content = `

            <div style="margin-top:10px;">
                ⏱️ เวลาที่คิดเงิน:
                <b>
                    ${formatMinutes(
                        shift.adjustedStart
                    )}
                    →
                    ${formatMinutes(
                        shift.adjustedEnd
                    )}
                </b>
            </div>

            <div style="margin-top:10px;">
                💵
                88 บาท ×
                ${
                    (
                        shift.normalMinutes /
                        60
                    ).toFixed(1)
                }
                ชั่วโมง
            </div>

        `;

    }else{

        content = `

            <div style="margin-top:10px;">
                💵 ค่าแรงปกติ:
                <b>
                    ฿${formatMoney(
                        shift.normalPay
                    )}
                </b>
            </div>

            <div style="margin-top:10px;">
                ⏱️ OT:
                ${
                    shift.otMinutes > 0
                    ? formatDuration(
                        shift.otMinutes
                    )
                    : 'ไม่มี'
                }

                ${
                    shift.otMinutes > 0
                    ? `
                        =
                        ฿${formatMoney(
                            shift.otPay
                        )}
                      `
                    : ''
                }
            </div>

        `;
    }


    details.innerHTML = `

        <div class="shift-card">

            <h3>

                ${
                    shift.isNight
                    ? '🌙'
                    : '☀️'
                }

                ${formatDateKey(dateKey)}

                (${dayName})

            </h3>


            <div>
                🟢 เข้า:
                <b>
                    ${shift.start.time}
                </b>
            </div>


            <div style="margin-top:8px;">

                🔴 ออก:
                <b>
                    ${shift.end.time}
                </b>

                ${
                    shift.start.dateKey !==
                    shift.end.dateKey
                    ? `
                        <small>
                            ออกวันที่
                            ${formatDateKey(
                                shift.end.dateKey
                            )}
                        </small>
                      `
                    : ''
                }

            </div>


            ${content}


            <div class="shift-total">

                💰 ค่าแรงวันนี้:
                ฿${formatMoney(
                    shift.pay
                )}

            </div>

        </div>

    `;
}


/* =====================================================
   รอบจ่าย
===================================================== */

function getPayPeriod(
    dateKey
){

    const date =
        new Date(
            `${dateKey}T12:00:00`
        );


    const day =
        date.getDate();


    /*
     * 26-สิ้นเดือน
     * -> รอบ 01 เดือนถัดไป
     */

    if(day >= 26){

        const next =
            new Date(
                date.getFullYear(),
                date.getMonth()+1,
                1
            );


        return {

            round:1,

            year:
                next.getFullYear(),

            month:
                next.getMonth(),

            label:
                `รอบ 01 / ${getThaiMonth(next.getMonth())}`,

            start:
                `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-26`,

            end:
                `${next.getFullYear()}-${String(next.getMonth()+1).padStart(2,'0')}-10`
        };
    }


    /*
     * 1-10
     * -> รอบ 01 เดือนนั้น
     */

    if(day <= 10){

        return {

            round:1,

            year:
                date.getFullYear(),

            month:
                date.getMonth(),

            label:
                `รอบ 01 / ${getThaiMonth(date.getMonth())}`,

            start:
                `${date.getFullYear()}-${String(date.getMonth()).padStart(2,'0')}-26`,

            end:
                dateKey
        };
    }


    /*
     * 11-25
     */

    return {

        round:2,

        year:
            date.getFullYear(),

        month:
            date.getMonth(),

        label:
            `รอบ 02 / ${getThaiMonth(date.getMonth())}`,

        start:
            `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-11`,

        end:
            `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-25`
    };
}


/* =====================================================
   แสดงรอบจ่าย
===================================================== */

function renderPeriods(){

    if(!shifts.length){

        periods.innerHTML =
            'ยังไม่มีข้อมูล';

        return;
    }


    const groups = {};


    shifts.forEach(
        shift=>{

            if(shift.incomplete){

                return;
            }


            const period =
                getPayPeriod(
                    shift.start.dateKey
                );


            const key =
                `${period.year}-${period.month}-${period.round}`;


            if(!groups[key]){

                groups[key] = {

                    period:period,

                    shifts:[],

                    total:0
                };
            }


            groups[key]
                .shifts
                .push(shift);


            groups[key].total +=
                shift.pay;
        }
    );


    periods.innerHTML = '';


    Object.values(groups)
        .sort(
            (a,b)=>
                a.period.year -
                b.period.year ||
                a.period.month -
                b.period.month ||
                a.period.round -
                b.period.round
        )
        .forEach(
            group=>{

                const div =
                    document.createElement('div');


                div.className =
                    'pay-period';


                div.innerHTML = `

                    <h3>
                        💰
                        ${group.period.label}
                    </h3>

                    <div>
                        วันที่
                        ${formatPeriodDate(
                            group.period
                        )}
                    </div>

                    <div style="margin-top:8px;">
                        จำนวนกะ:
                        <b>
                            ${group.shifts.length}
                        </b>
                    </div>

                    <div class="pay-period-total">
                        ฿${formatMoney(
                            group.total
                        )}
                    </div>

                `;


                periods.appendChild(div);
            }
        );
}


/* =====================================================
   รายละเอียดทั้งหมด
===================================================== */

function renderDetails(){

    details.innerHTML = '';


    if(!shifts.length){

        details.innerHTML =
            'ยังไม่มีข้อมูล';

        return;
    }


    shifts.forEach(
        shift=>{

            const div =
                document.createElement('div');


            div.className =
                'shift-card';


            if(shift.incomplete){

                div.innerHTML = `

                    <h3>
                        📅
                        ${formatDateKey(
                            shift.start.dateKey
                        )}
                    </h3>

                    <div>
                        🟢 เข้า:
                        ${shift.start.time}
                    </div>

                    <div class="error-box">
                        ⚠️
                        รูปเวลาออกยังไม่ครบ
                    </div>

                `;

            }else{

                div.innerHTML = `

                    <h3>

                        ${
                            shift.isNight
                            ? '🌙'
                            : '☀️'
                        }

                        ${formatDateKey(
                            shift.start.dateKey
                        )}

                    </h3>

                    <div>
                        🟢 เข้า:
                        <b>
                            ${shift.start.time}
                        </b>
                    </div>

                    <div style="margin-top:8px;">
                        🔴 ออก:
                        <b>
                            ${shift.end.time}
                        </b>

                        ${
                            shift.start.dateKey !==
                            shift.end.dateKey
                            ? `
                                <small>
                                (${formatDateKey(
                                    shift.end.dateKey
                                )})
                                </small>
                              `
                            : ''
                        }
                    </div>

                    <div style="margin-top:8px;">
                        💵 ปกติ:
                        ฿${formatMoney(
                            shift.normalPay
                        )}
                    </div>

                    ${
                        !shift.isSunday
                        ? `
                            <div style="margin-top:8px;">
                                ⏱️ OT:
                                ${
                                    shift.otMinutes
                                    ? formatDuration(
                                        shift.otMinutes
                                    )
                                    : 'ไม่มี'
                                }

                                ${
                                    shift.otMinutes
                                    ? `
                                        =
                                        ฿${formatMoney(
                                            shift.otPay
                                        )}
                                      `
                                    : ''
                                }
                            </div>
                          `
                        : `
                            <div style="margin-top:8px;">
                                ⏱️
                                ${
                                    formatMinutes(
                                        shift.adjustedStart
                                    )
                                }
                                →
                                ${
                                    formatMinutes(
                                        shift.adjustedEnd
                                    )
                                }
                            </div>
                          `
                    }

                    <div class="shift-total">
                        💰
                        ฿${formatMoney(
                            shift.pay
                        )}
                    </div>

                `;
            }


            details.appendChild(div);
        }
    );
}


/* =====================================================
   Utilities
===================================================== */

function timeToMinutes(time){

    const parts =
        time.split(':');


    return (
        parseInt(parts[0],10) * 60 +
        parseInt(parts[1],10)
    );
}


function formatMoney(value){

    return Number(value)
        .toLocaleString(
            'th-TH',
            {
                minimumFractionDigits:2,
                maximumFractionDigits:2
            }
        );
}


function formatDuration(minutes){

    const h =
        Math.floor(minutes / 60);


    const m =
        minutes % 60;


    if(!h){

        return `${m} นาที`;
    }


    if(!m){

        return `${h} ชั่วโมง`;
    }


    return `${h} ชั่วโมง ${m} นาที`;
}


function formatMinutes(minutes){

    /*
     * รองรับ 29:30
     */

    minutes =
        minutes % (24 * 60);


    const h =
        Math.floor(minutes / 60);


    const m =
        minutes % 60;


    return (
        String(h).padStart(2,'0') +
        ':' +
        String(m).padStart(2,'0')
    );
}


function formatDateKey(key){

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


function formatPeriodDate(period){

    const start =
        period.start
            .split('-')
            .reverse()
            .join('/');


    const end =
        period.end
            .split('-')
            .reverse()
            .join('/');


    return `${start} - ${end}`;
}


function getThaiDay(day){

    return [
        'อาทิตย์',
        'จันทร์',
        'อังคาร',
        'พุธ',
        'พฤหัสบดี',
        'ศุกร์',
        'เสาร์'
    ][day];
}


function getThaiMonth(month){

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


function formatBytes(bytes){

    if(!bytes){

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
                Math.pow(1024,i)
            ).toFixed(2)
        )
        +
        ' ' +
        units[i]
    );
}


function escapeHTML(value){

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


/* =====================================================
   บีบอัดรูปก่อนส่ง OCR
===================================================== */

function compressImage(file){

    return new Promise(
        (resolve,reject)=>{

            const reader =
                new FileReader();


            reader.onload =
                function(event){

                    const img =
                        new Image();


                    img.onload =
                        function(){

                            const maxWidth = 1600;

                            const maxHeight = 1600;


                            let width =
                                img.width;

                            let height =
                                img.height;


                            if(
                                width >
                                maxWidth
                            ){

                                height =
                                    height *
                                    maxWidth /
                                    width;

                                width =
                                    maxWidth;
                            }


                            if(
                                height >
                                maxHeight
                            ){

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
                                blob=>{

                                    if(!blob){

                                        reject(
                                            new Error(
                                                'บีบอัดรูปไม่สำเร็จ'
                                            )
                                        );

                                        return;
                                    }


                                    resolve(blob);

                                },
                                'image/jpeg',
                                0.80
                            );
                        };


                    img.onerror =
                        ()=>{

                            reject(
                                new Error(
                                    'อ่านรูปไม่สำเร็จ'
                                )
                            );
                        };


                    img.src =
                        event.target.result;
                };


            reader.onerror =
                ()=>{

                    reject(
                        new Error(
                            'อ่านไฟล์ไม่สำเร็จ'
                        )
                    );
                };


            reader.readAsDataURL(file);
        }
    );
}


/* =====================================================
   เริ่มต้น
===================================================== */

renderCalendar();

</script>

</body>
</html>
