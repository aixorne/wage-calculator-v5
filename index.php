ให้แก้ 3 ฟังก์ชัน ใน "index.php" เดิมดังนี้

① ทับ "extractDate()" เดิม

function extractDate(text) {

    /*
     * รองรับ:
     *
     * 2026-07-26
     * 26/07/2026
     * 26-07-2026
     * 26/07/26
     *
     */

    let match =
        text.match(
            /\b(20\d{2})[-\/](0?[1-9]|1[0-2])[-\/](0?[1-9]|[12]\d|3[01])\b/
        );


    if (match) {

        let year =
            parseInt(
                match[1],
                10
            );

        let month =
            parseInt(
                match[2],
                10
            );

        let day =
            parseInt(
                match[3],
                10
            );


        return {

            year: year,

            month: month,

            day: day,

            key:
                `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`,

            display:
                `${String(day).padStart(2,'0')}/${String(month).padStart(2,'0')}/${year}`
        };
    }


    /*
     * รองรับวันที่แบบ DD/MM/YYYY
     */

    match =
        text.match(
            /\b(0?[1-9]|[12]\d|3[01])[-\/](0?[1-9]|1[0-2])[-\/](\d{2,4})\b/
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
        parseInt(
            match[3],
            10
        );


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

        year: year,

        month: month,

        day: day,

        key:
            `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`,

        display:
            `${String(day).padStart(2,'0')}/${String(month).padStart(2,'0')}/${year}`
    };
}

---

② ทับ "calculateShift()" เดิม

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
     * =================================================
     * วันอาทิตย์
     *
     * คิดเป็นรายชั่วโมง 88 บาท
     *
     * ตัวอย่าง:
     *
     * 16:53 -> 17:00
     * 18:08 -> 18:00
     *
     * = 1 ชั่วโมง
     * = 88 บาท
     * =================================================
     */

    if (isSunday) {

        let adjustedStart =
            startMinutes;

        let adjustedEnd =
            endMinutes;


        /*
         * ถ้าเริ่มก่อน 17:00
         * แต่เป็นช่วง 16:30-16:59
         * ให้เริ่ม 17:00
         */

        if (
            adjustedStart >=
            16 * 60 + 30 &&
            adjustedStart <=
            17 * 60
        ) {

            adjustedStart =
                17 * 60;
        }


        /*
         * ปัดเวลาออกลงทุก 30 นาที
         *
         * 18:08 -> 18:00
         * 18:29 -> 18:00
         * 18:30 -> 18:30
         */

        adjustedEnd =
            Math.floor(
                adjustedEnd / 30
            ) * 30;


        /*
         * ป้องกันเวลาติดลบ
         */

        const totalMinutes =
            Math.max(
                0,
                adjustedEnd -
                adjustedStart
            );


        /*
         * ค่าแรงวันอาทิตย์
         */

        const pay =
            (
                totalMinutes /
                60
            ) *
            SUNDAY_RATE;


        return {

            dayName:
                getThaiDay(
                    dayOfWeek
                ),

            isSunday: true,

            isNight: false,

            adjustedStart:
                adjustedStart,

            adjustedEnd:
                adjustedEnd,

            normalMinutes:
                totalMinutes,

            otMinutes: 0,

            normalPay:
                pay,

            otPay: 0,

            pay: pay
        };
    }


    /*
     * =================================================
     * จันทร์-เสาร์
     * =================================================
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


    /*
     * =================================================
     * กะดึก
     * =================================================
     */

    if (isNight) {

        /*
         * 19:30-20:00
         * ให้นับเป็น 20:00
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


        let endContinuous =
            endMinutes;


        /*
         * เวลาออกหลังเที่ยงคืน
         */

        if (
            endContinuous <
            12 * 60
        ) {

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

    }


    /*
     * =================================================
     * กะกลางวัน
     * =================================================
     */

    else {

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
         * ปกติถึง 17:00
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
     * ค่าแรงปกติ
     */

    normalPay =
        NORMAL_DAILY_WAGE;


    /*
     * OT
     */

    otPay =
        (
            otMinutes /
            60
        ) *
        OT_RATE;


    return {

        dayName:
            getThaiDay(
                dayOfWeek
            ),

        isSunday: false,

        isNight: isNight,

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

---

③ ทับ "renderCalendar()" เดิม

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


        /*
         * หา shift ของวันนี้
         */

        const shift =
            shifts.find(
                s =>
                    s.start.dateKey ===
                    key
            );


        let html = `

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


        /*
         * ทำให้กดได้
         */

        cell.innerHTML =
            html;


        cell.style.cursor =
            'pointer';


        cell.addEventListener(
            'click',
            function() {

                showDayDetail(
                    key
                );

            }
        );


        calendar.appendChild(
            cell
        );
    }
}

---

④ เพิ่มฟังก์ชันนี้ต่อท้าย "index.php"

function showDayDetail(
    dateKey
) {

    const shift =
        shifts.find(
            s =>
                s.start.dateKey ===
                dateKey
        );


    /*
     * เลื่อนไปส่วนรายละเอียด
     */

    detailsCard.scrollIntoView({
        behavior:'smooth',
        block:'start'
    });


    if (!shift) {

        details.innerHTML = `

            <div class="shift-card">

                <h3>
                    📅
                    ${formatDateKey(
                        dateKey
                    )}
                </h3>

                <p>
                    ไม่มีข้อมูลการทำงานวันนี้
                </p>

            </div>

        `;

        return;
    }


    if (
        shift.incomplete
    ) {

        details.innerHTML = `

            <div class="shift-card">

                <h3>
                    📅
                    ${formatDateKey(
                        dateKey
                    )}
                </h3>

                <div>
                    🟢 เข้า:
                    <b>
                        ${shift.start.time}
                    </b>
                </div>

                <div
                    class="error-box"
                    style="margin-top:12px;"
                >
                    ⚠️
                    ยังไม่พบรูปเวลาออกงาน
                </div>

            </div>

        `;

        return;
    }


    const dayName =
        getThaiDay(
            new Date(
                dateKey +
                'T12:00:00'
            ).getDay()
        );


    let extra = '';


    if (
        shift.isSunday
    ) {

        extra = `

            <div style="margin-top:8px;">
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

            <div style="margin-top:8px;">
                💵 88 บาท ×
                ${
                    (
                        shift.normalMinutes /
                        60
                    ).toFixed(1)
                }
                ชั่วโมง
            </div>

        `;

    } else {

        extra = `

            <div style="margin-top:8px;">
                💵 ค่าแรงปกติ:
                ฿${formatMoney(
                    shift.normalPay
                )}
            </div>

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

                ${formatDateKey(
                    dateKey
                )}

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
                                (
                                ${formatDateKey(
                                    shift.end.dateKey
                                )}
                                )
                            </small>
                          `
                        : ''
                }

            </div>


            ${extra}


            <div
                class="shift-total"
            >

                💰
                ค่าแรงวันนี้:
                ฿${formatMoney(
                    shift.pay
                )}

            </div>

        </div>

    `;
}


function formatMinutes(
    minutes
) {

    /*
     * สำหรับกะดึกที่เกิน 24 ชั่วโมง
     */

    minutes =
        minutes % (
            24 * 60
        );


    const h =
        Math.floor(
            minutes / 60
        );


    const m =
        minutes % 60;


    return (
        String(h).padStart(
            2,
            '0'
        )
        +
        ':'
        +
        String(m).padStart(
            2,
            '0'
        )
    );
}
