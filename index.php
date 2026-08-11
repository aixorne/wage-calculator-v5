<!DOCTYPE html>
<html lang="th">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>ระบบคำนวณค่าแรง V5.3</title>

<link
    rel="stylesheet"
    href="style.css"
>

<style>

/* =====================================================
   V5.3 CALENDAR / UI
===================================================== */

.calendar-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:10px;
    margin-bottom:15px;
}

.calendar-header button {
    width:auto;
    min-width:50px;
    padding:8px 14px;
}

.calendar-title {
    font-size:20px;
    font-weight:bold;
}

.calendar {
    display:grid;
    grid-template-columns:
        repeat(7, 1fr);
    gap:5px;
}

.calendar-day-name {
    text-align:center;
    font-weight:bold;
    padding:8px 2px;
    font-size:13px;
}

.calendar-cell {
    min-height:85px;
    border:1px solid #ddd;
    border-radius:10px;
    padding:7px;
    background:#fff;
    overflow:hidden;
}

.calendar-cell.empty {
    background:#f5f5f5;
    border:none;
}

.calendar-cell.today {
    border:2px solid #2196f3;
}

.calendar-date {
    font-weight:bold;
    font-size:14px;
}

.calendar-money {
    margin-top:7px;
    font-size:13px;
    font-weight:bold;
}

.calendar-time {
    font-size:11px;
    margin-top:4px;
    color:#555;
}

.shift-card {
    border:1px solid #ddd;
    border-radius:12px;
    padding:15px;
    margin-bottom:12px;
    background:#fff;
}

.shift-total {
    font-size:20px;
    font-weight:bold;
    margin-top:12px;
}

.pay-period {
    border:1px solid #ddd;
    border-radius:14px;
    padding:17px;
    margin-bottom:15px;
    background:#fff;
}

.pay-period h3 {
    margin-top:0;
}

.pay-period-total {
    font-size:24px;
    font-weight:bold;
    margin-top:10px;
}

.badge {
    display:inline-block;
    padding:4px 8px;
    border-radius:7px;
    font-size:12px;
    background:#eee;
    margin-left:5px;
}

.night {
    background:#e9e3ff;
}

.day {
    background:#fff3cd;
}

.error-box {
    padding:12px;
    border-radius:10px;
    background:#fff0f0;
    border:1px solid #ffcccc;
    margin-bottom:10px;
}

</style>

</head>


<body>

<div class="container">

<h1>💰 ระบบคำนวณค่าแรง V5.3</h1>

<p class="subtitle">
OCR + กะกลางวัน + กะดึก + ปฏิทิน + รอบจ่าย
</p>


<!-- =====================================================
     UPLOAD
===================================================== -->

<div class="card">

<h2>📷 อัปโหลดรูป</h2>

<p>
เลือกภาพเวลาเข้าและออกงานทั้งหมดที่ต้องการตรวจสอบ
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


<!-- =====================================================
     PAY RULE
===================================================== -->

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
<b>352 บาท/กะ</b>
+
<b>OT 66 บาท/ชม.</b>
</div>

<div style="margin-top:7px;">
อาทิตย์:
<b>88 บาท/ชม.</b>
</div>

<div style="margin-top:7px;">
⏱️ OT เริ่ม 17:30 หรือ 05:30
</div>

<div style="margin-top:7px;">
🔢 OT ปัดลงทุก 30 นาที
</div>

</div>


<!-- =====================================================
     CALENDAR
===================================================== -->

<div
    id="calendarCard"
    class="card"
>

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

</div>


<!-- =====================================================
     PAY PERIODS
===================================================== -->

<div
    id="periodCard"
    class="card"
>

<h2>💰 รอบจ่าย</h2>

<div id="periods">
ยังไม่มีข้อมูล
</div>

</div>


<!-- =====================================================
     SHIFT DETAILS
===================================================== -->

<div
    id="detailsCard"
    class="card"
>

<h2>📋 รายละเอียดงาน</h2>

<div id="details">
ยังไม่มีข้อมูล
</div>

</div>


<!-- =====================================================
     OCR
===================================================== -->

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
   CONSTANTS
===================================================== */

const NORMAL_DAILY_WAGE = 352;

const OT_RATE = 66;

const SUNDAY_RATE = 88;


/*
 * กลางวัน
 */
const DAY_START = 8 * 60;

const DAY_END = 17 * 60;

const DAY_OT_START =
    17 * 60 + 30;


/*
 * กลางคืน
 */
const NIGHT_START = 20 * 60;

const NIGHT_END = 5 * 60;

const NIGHT_OT_START =
    5 * 60 + 30;


/* =====================================================
   GLOBAL DATA
===================================================== */

let shifts = [];

let ocrRecords = [];

let calendarDate =
    new Date();


/* =====================================================
   ELEMENTS
===================================================== */

const imageInput =
    document.getElementById(
        'imageInput'
    );

const fileList =
    document.getElementById(
        'fileList'
    );

const loading =
    document.getElementById(
        'loading'
    );

const ocrButton =
    document.getElementById(
        'ocrButton'
    );

const results =
    document.getElementById(
        'results'
    );

const resultsCard =
    document.getElementById(
        'resultsCard'
    );

const calendar =
    document.getElementById(
        'calendar'
    );

const calendarTitle =
    document.getElementById(
        'calendarTitle'
    );

const periods =
    document.getElementById(
        'periods'
    );

const details =
    document.getElementById(
        'details'
    );


/* =====================================================
   FILE LIST
===================================================== */

imageInput.addEventListener(
    'change',
    function () {

        fileList.innerHTML = '';

        const files =
            Array.from(
                this.files
            );

        files.forEach(
            (file, index) => {

                const div =
                    document.createElement(
                        'div'
                    );

                div.style.padding =
                    '6px 0';

                div.innerHTML =
                    `
                    📷 ${index + 1}.
                    ${escapeHTML(file.name)}
                    <small>
                    (${formatBytes(file.size)})
                    </small>
                    `;

                fileList.appendChild(
                    div
                );
            }
        );
    }
);


/* =====================================================
   OCR ALL
===================================================== */

async function runAllOCR() {

    const files =
        Array.from(
            imageInput.files
        );


    if (!files.length) {

        alert(
            'กรุณาเลือกรูปก่อน'
        );

        return;
    }


    ocrButton.disabled =
        true;

    loading.style.display =
        'block';

    results.innerHTML =
        '';

    resultsCard.style.display =
        'none';


    ocrRecords = [];


    try {

        for (
            let i = 0;
            i < files.length;
            i++
        ) {

            loading.textContent =
                `⏳ OCR รูป ${i + 1} / ${files.length}`;


            const compressed =
                await compressImage(
                    files[i]
                );


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
                        method:'POST',
                        body:formData
                    }
                );


            const data =
                await response.json();


            if (!data.success) {

                throw new Error(
                    `รูปที่ ${i + 1}: ` +
                    (
                        data.error ||
                        'OCR ไม่สำเร็จ'
                    )
                );
            }


            const text =
                data.text || '';


            const times =
                extractTimes(
                    text
                );


            const date =
                extractDate(
                    text
                );


            ocrRecords.push({

                index:i,

                fileName:
                    files[i].name,

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


    } catch(error) {

        console.error(error);

        alert(
            error.message
        );

    } finally {

        loading.style.display =
            'none';

        loading.textContent =
            '⏳ พร้อมทำงาน';

        ocrButton.disabled =
            false;
    }
}


/* =====================================================
   EXTRACT TIMES
===================================================== */

function extractTimes(text) {

    const regex =
        /\b([01]?\d|2[0-3])[\.:]([0-5]\d)\b/g;


    const result = [];

    let match;


    while (
        (match =
            regex.exec(text)) !== null
    ) {

        const hour =
            String(
                parseInt(
                    match[1],
                    10
                )
            ).padStart(
                2,
                '0'
            );


        result.push(
            `${hour}:${match[2]}`
        );
    }


    return [
        ...new Set(result)
    ];
}


/* =====================================================
   EXTRACT DATE
===================================================== */

function extractDate(text) {

    const regex =
        /\b(0?[1-9]|[12]\d|3[01])[\-\/](0?[1-9]|1[0-2])(?:[\-\/](\d{2,4}))?\b/;


    const match =
        text.match(
            regex
        );


    if (!match) {

        return null;
    }


    let day =
        parseInt(
            match[1],
            10
        );


    let month =
        parseInt(
            match[2],
            10
        );


    let year =
        match[3]
            ? parseInt(
                match[3],
                10
            )
            : new Date().getFullYear();


    if (year < 100) {

        year += 2000;
    }


    /*
     * พ.ศ.
     */

    if (year > 2400) {

        year -= 543;
    }


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
   SHOW OCR
===================================================== */

function showOCR() {

    results.innerHTML = '';


    ocrRecords.forEach(
        (record,index) => {

            const div =
                document.createElement(
                    'div'
                );


            div.className =
                'shift-card';


            div.innerHTML = `

                <b>📷 รูป ${index + 1}</b>

                <div>
                    📁
                    ${escapeHTML(
                        record.fileName
                    )}
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
                            ? record.times.join(
                                ', '
                            )
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
                    >${escapeHTML(
                        record.text
                    )}</textarea>

                </details>
            `;


            results.appendChild(
                div
            );
        }
    );


    resultsCard.style.display =
        'block';
}


/* =====================================================
   BUILD SHIFTS
===================================================== */

function buildShifts() {

    const events = [];


    /*
     * แต่ละรูปอาจมีเวลาเดียว
     */

    ocrRecords.forEach(
        record => {

            if (
                !record.date
            ) {

                return;
            }


            record.times.forEach(
                time => {

                    const minutes =
                        timeToMinutes(
                            time
                        );


                    events.push({

                        dateKey:
                            record.date.key,

                        year:
                            record.date.year,

                        month:
                            record.date.month,

                        day:
                            record.date.day,

                        time:
                            time,

                        minutes:
                            minutes,

                        fileName:
                            record.fileName
                    });
                }
            );
        }
    );


    /*
     * สร้าง timestamp
     */

    events.forEach(
        event => {

            event.timestamp =
                new Date(
                    event.dateKey +
                    'T' +
                    event.time +
                    ':00'
                ).getTime();
        }
    );


    /*
     * เรียงเวลา
     */

    events.sort(
        (a,b) =>
            a.timestamp -
            b.timestamp
    );


    shifts = [];


    let i = 0;


    while (
        i < events.length
    ) {

        const start =
            events[i];


        let end = null;


        /*
         * หา event ถัดไป
         */

        if (
            i + 1 <
            events.length
        ) {

            const next =
                events[i + 1];


            const diff =
                (
                    next.timestamp -
                    start.timestamp
                ) / 60000;


            /*
             * งานหนึ่งกะ
             * ไม่ควรเกิน 16 ชั่วโมง
             */

            if (
                diff > 0 &&
                diff <= 16 * 60
            ) {

                /*
                 * ตรวจว่าเป็น
                 * คู่ที่สมเหตุสมผล
                 */

                if (
                    isValidShiftPair(
                        start,
                        next
                    )
                ) {

                    end =
                        next;

                    i += 2;

                } else {

                    i += 1;
                }

            } else {

                i += 1;
            }

        } else {

            i += 1;
        }


        if (!end) {

            /*
             * รูปเข้าไม่มีรูปออก
             */

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


    /*
     * เรียงตามวันที่เริ่มงาน
     */

    shifts.sort(
        (a,b) =>
            a.start.timestamp -
            b.start.timestamp
    );
}


/* =====================================================
   VALID SHIFT PAIR
===================================================== */

function isValidShiftPair(
    start,
    end
) {

    const s =
        start.minutes;

    const e =
        end.minutes;


    /*
     * กะกลางวัน
     *
     * 07:30-08:00
     * -> 17:00+
     */

    if (
        s >= 7 * 60 + 30 &&
        s <= 12 * 60
    ) {

        if (
            end.dateKey ===
            start.dateKey
        ) {

            return e >= 16 * 60;
        }
    }


    /*
     * กะดึก
     *
     * 20:00+
     * -> วันถัดไป
     */

    if (
        s >= 19 * 60 &&
        end.dateKey !==
        start.dateKey
    ) {

        return e <= 12 * 60;
    }


    /*
     * เผื่อ OCR วันที่ผิด
     * แต่เวลาเป็นกะดึก
     */

    if (
        s >= 19 * 60 &&
        e <= 12 * 60
    ) {

        return true;
    }


    return false;
}


/* =====================================================
   CALCULATE SHIFT
===================================================== */

function calculateShift(
    start,
    end
) {

    const startMinutes =
        start.minutes;


    const endMinutes =
        end.minutes;


    const startDate =
        new Date(
            start.dateKey +
            'T12:00:00'
        );


    const dayOfWeek =
        startDate.getDay();


    const isSunday =
        dayOfWeek === 0;


    /*
     * กะดึก
     */

    const isNight =
        startMinutes >=
        19 * 60;


    let normalMinutes = 0;

    let otMinutes = 0;

    let adjustedStart =
        startMinutes;

    let normalPay = 0;

    let otPay = 0;


    if (isNight) {

        /*
         * กะดึก
         *
         * 20:00 -> 05:00
         *
         * เข้าเร็ว 19:30-20:00
         * ให้นับ 20:00
         */

        if (
            adjustedStart >=
            19 * 60 + 30 &&
            adjustedStart <=
            20 * 60
        ) {

            adjustedStart =
                20 * 60;
        }


        /*
         * เวลาออก
         *
         * เพราะเป็นวันถัดไป
         * ให้แปลงเป็นนาทีต่อเนื่อง
         */

        let endContinuous =
            endMinutes;


        if (
            endContinuous <
            12 * 60
        ) {

            endContinuous +=
                24 * 60;
        }


        const normalEnd =
            29 * 60;


        /*
         * 05:00
         */

        if (
            endContinuous >=
            normalEnd
        ) {

            normalMinutes =
                normalEnd -
                adjustedStart;

        } else {

            normalMinutes =
                Math.max(
                    0,
                    endContinuous -
                    adjustedStart
                );
        }


        /*
         * OT เริ่ม 05:30
         */

        const otStart =
            29 * 60 + 30;


        if (
            endContinuous >=
            otStart
        ) {

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


    } else {

        /*
         * กะกลางวัน
         */

        /*
         * 07:30-08:00
         * -> 08:00
         */

        if (
            adjustedStart >=
            7 * 60 + 30 &&
            adjustedStart <=
            8 * 60
        ) {

            adjustedStart =
                8 * 60;
        }


        /*
         * ค่าแรงปกติถึง 17:00
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

        if (
            endMinutes >=
            DAY_OT_START
        ) {

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


    /*
     * ค่าแรง
     */

    if (isSunday) {

        /*
         * อาทิตย์
         * 88 บาท / ชั่วโมง
         */

        normalPay =
            (
                normalMinutes /
                60
            ) *
            SUNDAY_RATE;


        otPay =
            (
                otMinutes /
                60
            ) *
            SUNDAY_RATE;

    } else {

        /*
         * จันทร์-เสาร์
         */

        normalPay =
            NORMAL_DAILY_WAGE;


        otPay =
            (
                otMinutes /
                60
            ) *
            OT_RATE;
    }


    return {

        dayName:
            getThaiDay(
                dayOfWeek
            ),

        isSunday:
            isSunday,

        isNight:
            isNight,

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
   CALENDAR
===================================================== */

function renderCalendar() {

    const year =
        calendarDate.getFullYear();


    const month =
        calendarDate.getMonth();


    calendarTitle.textContent =
        `${getThaiMonth(month)} ${year + 543}`;


    calendar.innerHTML =
        '';


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
        name => {

            const div =
                document.createElement(
                    'div'
                );

            div.className =
                'calendar-day-name';

            div.textContent =
                name;

            calendar.appendChild(
                div
            );
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


    for (
        let i = 0;
        i < firstDay;
        i++
    ) {

        const empty =
            document.createElement(
                'div'
            );

        empty.className =
            'calendar-cell empty';

        calendar.appendChild(
            empty
        );
    }


    for (
        let day = 1;
        day <= daysInMonth;
        day++
    ) {

        const cell =
            document.createElement(
                'div'
            );


        cell.className =
            'calendar-cell';


        const key =
            `${year}-${String(month + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;


        const today =
            new Date();


        if (
            today.getFullYear() === year &&
            today.getMonth() === month &&
            today.getDate() === day
        ) {

            cell.classList.add(
                'today'
            );
        }


        const shift =
            shifts.find(
                s =>
                    s.start.dateKey ===
                    key
            );


        let html =
            `
            <div class="calendar-date">
                ${day}
            </div>
            `;


        if (shift) {

            if (
                shift.incomplete
            ) {

                html += `
                    <div class="calendar-time">
                        ⚠️ รูปไม่ครบ
                    </div>
                `;

            } else {

                html += `
                    <div class="calendar-time">
                        ${
                            shift.isNight
                                ? '🌙'
                                : '☀️'
                        }

                        ${shift.start.time}
                        →
                        ${
                            shift.end.time
                        }

                    </div>

                    <div class="calendar-money">
                        ฿${formatMoney(
                            shift.pay
                        )}
                    </div>
                `;
            }
        }


        cell.innerHTML =
            html;


        calendar.appendChild(
            cell
        );
    }
}


/* =====================================================
   CHANGE MONTH
===================================================== */

function changeMonth(
    amount
) {

    calendarDate.setMonth(
        calendarDate.getMonth() +
        amount
    );


    renderCalendar();
}


/* =====================================================
   PAY PERIOD
===================================================== */

function getPayPeriod(
    dateKey
) {

    const date =
        new Date(
            dateKey +
            'T12:00:00'
        );


    const day =
        date.getDate();


    /*
     * 26-31
     * อยู่รอบ 01 ของเดือนถัดไป
     */

    if (
        day >= 26
    ) {

        const next =
            new Date(
                date.getFullYear(),
                date.getMonth() + 1,
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
     * อยู่รอบ 01 ของเดือนนั้น
     */

    if (
        day <= 10
    ) {

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
   RENDER PERIODS
===================================================== */

function renderPeriods() {

    if (
        !shifts.length
    ) {

        periods.innerHTML =
            'ยังไม่มีข้อมูล';

        return;
    }


    const groups = {};


    shifts.forEach(
        shift => {

            if (
                shift.incomplete
            ) {

                return;
            }


            const period =
                getPayPeriod(
                    shift.start.dateKey
                );


            const key =
                `${period.year}-${period.month}-${period.round}`;


            if (
                !groups[key]
            ) {

                groups[key] = {

                    period:
                        period,

                    shifts:[],

                    total:0
                };
            }


            groups[key]
                .shifts
                .push(
                    shift
                );


            groups[key].total +=
                shift.pay;
        }
    );


    periods.innerHTML =
        '';


    Object.values(groups)
        .sort(
            (a,b) =>
                a.period.year -
                b.period.year ||
                a.period.month -
                b.period.month ||
                a.period.round -
                b.period.round
        )
        .forEach(
            group => {

                const div =
                    document.createElement(
                        'div'
                    );


                div.className =
                    'pay-period';


                const period =
                    group.period;


                div.innerHTML = `

                    <h3>
                        💰
                        ${period.label}
                    </h3>

                    <div>
                        วันที่
                        ${formatPeriodDate(
                            period
                        )}
                    </div>

                    <div style="margin-top:8px;">
                        จำนวนกะ:
                        <b>
                            ${group.shifts.length}
                        </b>
                    </div>

                    <div
                        class="pay-period-total"
                    >
                        ฿${formatMoney(
                            group.total
                        )}
                    </div>

                `;


                periods.appendChild(
                    div
                );
            }
        );
}


/* =====================================================
   DETAILS
===================================================== */

function renderDetails() {

    details.innerHTML =
        '';


    if (
        !shifts.length
    ) {

        details.innerHTML =
            'ยังไม่มีข้อมูล';

        return;
    }


    shifts.forEach(
        shift => {

            const div =
                document.createElement(
                    'div'
                );


            div.className =
                'shift-card';


            const startDate =
                shift.start.dateKey;


            const dayName =
                getThaiDay(
                    new Date(
                        startDate +
                        'T12:00:00'
                    ).getDay()
                );


            if (
                shift.incomplete
            ) {

                div.innerHTML = `

                    <h3>
                        📅
                        ${formatDateKey(
                            startDate
                        )}
                        (${dayName})
                    </h3>

                    <div>
                        ${
                            shift.start.time
                        }
                    </div>

                    <div
                        class="error-box"
                        style="margin-top:10px;"
                    >
                        ⚠️
                        ยังไม่พบเวลาออกงาน
                    </div>

                `;

            } else {

                div.innerHTML = `

                    <h3>
                        ${
                            shift.isNight
                                ? '🌙'
                                : '☀️'
                        }

                        ${formatDateKey(
                            startDate
                        )}

                        (${dayName})
                    </h3>


                    <div>
                        🟢 เข้า:
                        <b>
                            ${shift.start.time}
                        </b>
                    </div>


                    <div>
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


                    <hr>


                    <div>
                        💵 ปกติ:
                        ฿${formatMoney(
                            shift.normalPay
                        )}
                    </div>


                    <div style="margin-top:6px;">
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


                    <div
                        class="shift-total"
                    >
                        💰
                        ฿${formatMoney(
                            shift.pay
                        )}
                    </div>

                `;
            }


            details.appendChild(
                div
            );
        }
    );
}


/* =====================================================
   HELPERS
===================================================== */

function timeToMinutes(
    time
) {

    const parts =
        time.split(':');


    return (
        parseInt(
            parts[0],
            10
        ) * 60
        +
        parseInt(
            parts[1],
            10
        )
    );
}


function formatMoney(
    value
) {

    return Number(
        value
    ).toLocaleString(
        'th-TH',
        {
            minimumFractionDigits:2,
            maximumFractionDigits:2
        }
    );
}


function formatDuration(
    minutes
) {

    const h =
        Math.floor(
            minutes / 60
        );


    const m =
        minutes % 60;


    if (!h) {

        return `${m} นาที`;
    }


    if (!m) {

        return `${h} ชั่วโมง`;
    }


    return `${h} ชั่วโมง ${m} นาที`;
}


function formatDateKey(
    key
) {

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


function formatPeriodDate(
    period
) {

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


function getThaiDay(
    day
) {

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


function getThaiMonth(
    month
) {

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


function formatBytes(
    bytes
) {

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
                Math.pow(
                    1024,
                    i
                )
            ).toFixed(2)
        )
        +
        ' ' +
        units[i]
    );
}


function escapeHTML(
    value
) {

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
   IMAGE COMPRESS
===================================================== */

function compressImage(
    file
) {

    return new Promise(
        (
            resolve,
            reject
        ) => {

            const reader =
                new FileReader();


            reader.onload =
                function(event) {

                    const img =
                        new Image();


                    img.onload =
                        function() {

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
                                function(blob) {

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
                        function() {

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
                function() {

                    reject(
                        new Error(
                            'อ่านไฟล์ไม่สำเร็จ'
                        )
                    );
                };


            reader.readAsDataURL(
                file
            );
        }
    );
}


/* =====================================================
   INITIAL CALENDAR
===================================================== */

renderCalendar();

</script>

</body>

</html>
