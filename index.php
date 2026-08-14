<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wage Calculator V1</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
</head>
<body>

  <div class="app">

    <!-- HERO -->
    <section class="hero">
      <h1>💰 ระบบคำนวณค่าแรง</h1>
      <p>OCR • ค่าแรง • OT • ปฏิทิน • รอบจ่าย</p>
      <span class="version">WAGE CALCULATOR V1</span>
    </section>

    <!-- SUMMARY -->
    <section class="card">
      <div class="card-title">
        <span>📊</span>
        <h2>ภาพรวม</h2>
      </div>
      <div class="summary-grid">
        <div class="summary">
          <div class="summary-label">วันที่บันทึก</div>
          <div class="summary-value" id="summaryDays">0</div>
        </div>
        <div class="summary">
          <div class="summary-label">วันทำงาน</div>
          <div class="summary-value" id="summaryWorked">0</div>
        </div>
        <div class="summary">
          <div class="summary-label">ค่าแรงสะสม</div>
          <div class="summary-value" id="summaryMoney">฿0.00</div>
        </div>
      </div>
    </section>

    <!-- UPLOAD -->
    <section class="card">
      <div class="card-title">
        <span>📷</span>
        <h2>อัปโหลดเวลา</h2>
      </div>
      <div class="upload-zone" id="uploadZone">
        <div class="upload-icon">📸</div>
        <div class="upload-title">เลือกรูปเวลาเข้า–ออกงาน</div>
        <div class="upload-sub">แตะเพื่อเลือกหลายรูป หรือวางรูปลงตรงนี้</div>
        <input id="imageInput" class="file-input" type="file" accept="image/*" multiple>
      </div>

      <div id="previewGrid" class="preview-grid"></div>

      <button id="ocrButton" class="primary-btn" onclick="runOCR()" disabled>
        🔍 วิเคราะห์รูปทั้งหมด
      </button>
      <div id="loading" class="loading">ยังไม่ได้เลือกรูป</div>
    </section>

    <!-- CALENDAR -->
    <section class="card">
      <div class="card-title">
        <span>📅</span>
        <h2>ปฏิทินค่าแรง</h2>
      </div>
      <div class="calendar-top">
        <button class="month-btn" onclick="changeMonth(-1)">‹</button>
        <div id="monthTitle" class="month-title"></div>
        <button class="month-btn" onclick="changeMonth(1)">›</button>
      </div>
      <div id="calendar" class="calendar"></div>
      <div class="legend">
        <div class="legend-item"><span class="dot blue"></span>ยังไม่ถึง</div>
        <div class="legend-item"><span class="dot green"></span>อัปแล้ว</div>
        <div class="legend-item"><span class="dot orange"></span>ลืมอัป</div>
      </div>
    </section>

    <!-- PAY PERIOD -->
    <section class="card pay-period-card">
      <div class="card-title pay-period-title">
        <span>💵</span>
        <div>
          <h2>รอบจ่าย</h2>
          <p>สรุปค่าแรงตามรอบการจ่ายเงิน</p>
        </div>
      </div>
      <div id="periods" class="periods-container">
        <div class="empty-state">
          <div class="empty-icon">💰</div>
          <div class="empty-title">ยังไม่มีข้อมูลรอบจ่าย</div>
          <div class="empty-sub">เมื่อมีข้อมูลค่าแรง รอบจ่ายจะแสดงที่นี่</div>
        </div>
      </div>
    </section>

    <!-- SALARY SLIP -->
    <section class="card salary-slip-card">
      <div class="card-title">
        <span>🧾</span>
        <h2>สลิปเงินเดือน</h2>
      </div>
      <div class="salary-slip-box">
        <div class="slip-info">
          <div class="slip-field">
            <label>ชื่อพนักงาน</label>
            <input id="employeeName" type="text" value="นายจิรวัฒน์ กาญจนบุรางกูร">
          </div>
          <div class="slip-field">
            <label>รหัสพนักงาน</label>
            <input id="employeeId" type="text" value="14187">
          </div>
        </div>

        <div class="slip-field">
          <label>เลือกรอบจ่าย</label>
          <select id="salaryPeriod">
            <option value="">-- เลือกรอบจ่าย --</option>
          </select>
        </div>

        <div class="slip-field">
          <label>ค่าห้องเย็น</label>
          <div class="money-input">
            <input id="roomAllowance" type="number" value="120" min="0" step="0.01">
            <span>บาท</span>
          </div>
        </div>

        <div class="slip-field">
          <label>ประกันสังคม</label>
          <div class="money-input">
            <input id="socialSecurityRate" type="number" value="3" min="0" max="100" step="0.1">
            <span>%</span>
          </div>
        </div>

        <button class="salary-preview-btn" onclick="previewSalarySlip()">
          👁️ ดูตัวอย่างสลิป
        </button>
        <button class="salary-pdf-btn" onclick="generateSalaryPDF()">
          📄 สร้าง PDF สลิปเงินเดือน
        </button>
      </div>
    </section>

    <!-- DATA MANAGEMENT -->
    <section class="card">
      <div class="card-title">
        <span>💾</span>
        <h2>จัดการข้อมูล</h2>
      </div>
      <p class="data-description">
        ข้อมูลค่าแรงถูกเก็บไว้ใน LocalStorage ของเบราว์เซอร์เครื่องนี้ การเพิ่มข้อมูลวันใหม่จะไม่ลบข้อมูลวันเก่า
      </p>

      <div class="delete-day-box">
        <label for="deleteDate">เลือกวันที่ที่ต้องการลบ</label>
        <div class="delete-day-row">
          <input id="deleteDate" type="date" class="date-delete-input">
          <button class="danger-btn" onclick="deleteSelectedDate()">
            🗑️ ลบข้อมูลวันนี้
          </button>
        </div>
        <div class="delete-day-hint">
          ลบเฉพาะกะที่เริ่มในวันที่เลือกเท่านั้น ข้อมูลวันอื่นจะไม่ถูกลบ
        </div>
      </div>
    </section>

  </div>

  <!-- DAY DETAIL MODAL -->
  <div id="dayModal" class="modal" onclick="closeModalOutside(event)">
    <div class="modal-box">
      <div class="modal-header">
        <h3 id="modalTitle">รายละเอียด</h3>
        <button class="modal-close" onclick="closeModal()">×</button>
      </div>
      <div id="modalBody" class="modal-body"></div>
    </div>
  </div>

  <!-- ALL PAY PERIODS MODAL -->
  <div id="periodsMoreModal" class="modal" onclick="closePeriodsMoreOutside(event)">
    <div class="modal-box">
      <div class="modal-header">
        <div class="modal-header-title">
          <div class="modal-header-icon">💵</div>
          <div>
            <h3>รอบจ่ายทั้งหมด</h3>
            <span>ประวัติรอบจ่ายของคุณ</span>
          </div>
        </div>
        <button class="modal-close" onclick="closePeriodsMore()" aria-label="ปิด">×</button>
      </div>
      <div id="allPeriodsList" class="modal-body"></div>
    </div>
  </div>

  <!-- SALARY PREVIEW MODAL -->
  <div id="salaryPreviewModal" class="modal" onclick="closeSalaryPreviewOutside(event)">
    <div class="modal-box">
      <div class="modal-header">
        <h3>🧾 ตัวอย่างสลิปเงินเดือน</h3>
        <button class="modal-close" onclick="closeSalaryPreview()">×</button>
      </div>
      <div id="salaryPreviewBody" class="modal-body"></div>
    </div>
  </div>

  <script>
    /* =====================================================
       CONFIG & UTILS
       ===================================================== */
    const STORAGE_KEY = 'wage_calculator_v6_data';
    const NORMAL_WAGE = 352;
    const OT_RATE = 66;
    const SUNDAY_RATE = 88;
    const OT_START = 17 * 60 + 30;

    let shifts = [];
    let selectedFiles = [];
    let viewDate = new Date();

    const pad = n => String(n).padStart(2, '0');
    const formatNumber = val => Number(val || 0).toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const money = val => '฿' + formatNumber(val);
    const escapeHTML = str => String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    const thaiMonth = m => ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'][m];
    const thaiMonthShort = m => ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'][m];

    function parseDateKey(key) {
      return new Date(key + 'T00:00:00');
    }

    function formatDateKey(date) {
      return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    }

    function formatThaiDate(dateKey) {
      const d = parseDateKey(dateKey);
      return `${d.getDate()} ${thaiMonthShort(d.getMonth())} ${d.getFullYear() + 543}`;
    }

    function startOfDay(d) {
      return new Date(d.getFullYear(), d.getMonth(), d.getDate());
    }

    function dateObject(y, m, d) {
      return { year: y, month: m, day: d, key: `${y}-${pad(m)}-${pad(d)}` };
    }

    function toMinutes(t) {
      const [h, m] = t.split(':').map(Number);
      return h * 60 + m;
    }

    function timestamp(key, time) {
      return new Date(`${key}T${time}:00`).getTime();
    }

    function getDay(key) {
      return parseDateKey(key).getDay();
    }

    function makeId(s, e) {
      return `shift_${s.dateKey}_${s.time}_${e.dateKey}_${e.time}`;
    }

    /* DOM */
    const imageInput = document.getElementById('imageInput');
    const uploadZone = document.getElementById('uploadZone');
    const previewGrid = document.getElementById('previewGrid');
    const ocrButton = document.getElementById('ocrButton');
    const loading = document.getElementById('loading');
    const calendar = document.getElementById('calendar');
    const monthTitle = document.getElementById('monthTitle');
    const periods = document.getElementById('periods');
    const dayModal = document.getElementById('dayModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const salaryPeriod = document.getElementById('salaryPeriod');

    /* STARTUP */
    document.addEventListener('DOMContentLoaded', () => {
      loadData();
      renderAll();
      setupFileInput();
      setupDragDrop();
    });

    /* FILE INPUT & DRAG DROP */
    function setupFileInput() {
      imageInput.addEventListener('change', function() {
        addFiles(Array.from(this.files || []));
      });
    }

    function setupDragDrop() {
      ['dragenter', 'dragover'].forEach(evt => {
        uploadZone.addEventListener(evt, e => {
          e.preventDefault();
          e.stopPropagation();
          uploadZone.classList.add('dragover');
        });
      });

      ['dragleave', 'drop'].forEach(evt => {
        uploadZone.addEventListener(evt, e => {
          e.preventDefault();
          e.stopPropagation();
          uploadZone.classList.remove('dragover');
        });
      });

      uploadZone.addEventListener('drop', e => {
        const files = Array.from(e.dataTransfer.files || []).filter(f => f.type.startsWith('image/'));
        addFiles(files);
      });
    }

    function addFiles(files) {
      selectedFiles = [...selectedFiles, ...files];
      const map = new Map();
      selectedFiles.forEach(f => map.set(`${f.name}_${f.size}_${f.lastModified}`, f));
      selectedFiles = Array.from(map.values());
      renderPreviews();
    }

    function renderPreviews() {
      previewGrid.innerHTML = '';
      selectedFiles.forEach((file, index) => {
        const item = document.createElement('div');
        item.className = 'preview-item';
        const url = URL.createObjectURL(file);
        item.innerHTML = `
          <img src="${url}" alt="">
          <button type="button" class="remove-file" onclick="removeFile(${index})">×</button>
          <div class="preview-name">${escapeHTML(file.name)}</div>
        `;
        previewGrid.appendChild(item);
      });

      ocrButton.disabled = selectedFiles.length === 0;
      loading.textContent = selectedFiles.length > 0 ? `เลือกรูปแล้ว ${selectedFiles.length} รูป` : 'ยังไม่ได้เลือกรูป';
    }

    function removeFile(index) {
      selectedFiles.splice(index, 1);
      renderPreviews();
    }

    /* OCR PROCESS */
    async function runOCR() {
      if (selectedFiles.length === 0) return;
      ocrButton.disabled = true;
      const records = [];

      try {
        for (let i = 0; i < selectedFiles.length; i++) {
          const file = selectedFiles[i];
          loading.textContent = `🔍 กำลังอ่านรูป ${i + 1}/${selectedFiles.length}`;

          const compressed = await compressImage(file);
          const form = new FormData();
          form.append('image', compressed, file.name);

          const response = await fetch('ocr.php', { method: 'POST', body: form });
          if (!response.ok) throw new Error(`OCR Server Error: ${response.status}`);

          const data = await response.json();
          if (!data.success) throw new Error(data.error || 'OCR ไม่สำเร็จ');

          const text = data.text || '';
          const parsed = parseOCR(text);

          records.push({ fileName: file.name, text, date: parsed.date, times: parsed.times });
        }

        const newShifts = createShifts(records);
        if (newShifts.length === 0) throw new Error('ไม่พบวันที่หรือเวลาในรูป');

        mergeShifts(newShifts);
        selectedFiles = [];
        imageInput.value = '';
        renderPreviews();
        loading.textContent = `✅ บันทึกข้อมูล ${newShifts.length} กะเรียบร้อย`;
        renderAll();

      } catch (error) {
        console.error(error);
        loading.textContent = '❌ ' + error.message;
        alert(error.message);
      } finally {
        ocrButton.disabled = selectedFiles.length === 0;
      }
    }

    function compressImage(file) {
      return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
          URL.revokeObjectURL(url);
          const maxWidth = 1800;
          let width = img.width, height = img.height;
          if (width > maxWidth) {
            height = Math.round(height * (maxWidth / width));
            width = maxWidth;
          }
          const canvas = document.createElement('canvas');
          canvas.width = width;
          canvas.height = height;
          const ctx = canvas.getContext('2d');
          ctx.drawImage(img, 0, 0, width, height);
          canvas.toBlob(blob => blob ? resolve(blob) : reject(new Error('ไม่สามารถบีบอัดรูปได้')), 'image/jpeg', 0.88);
        };
        img.onerror = () => {
          URL.revokeObjectURL(url);
          reject(new Error('ไม่สามารถเปิดรูปได้'));
        };
        img.src = url;
      });
    }

    function parseOCR(text) {
      return { date: extractDate(text), times: extractTimes(text) };
    }

    function extractDate(text) {
      if (!text) return null;
      let m = text.match(/\b(20\d{2}|25\d{2})[-\/](\d{1,2})[-\/](\d{1,2})\b/);
      if (m) {
        let y = Number(m[1]);
        if (y > 2400) y -= 543;
        return dateObject(y, Number(m[2]), Number(m[3]));
      }
      m = text.match(/\b(\d{1,2})[-\/](\d{1,2})[-\/](\d{2,4})\b/);
      if (m) {
        let y = Number(m[3]);
        if (y < 100) y += 2000;
        if (y > 2400) y -= 543;
        return dateObject(y, Number(m[2]), Number(m[1]));
      }
      return null;
    }

    function extractTimes(text) {
      const found = [];
      if (!text) return found;
      const regex = /\b([01]?\d|2[0-3])[\.:]([0-5]\d)\b/g;
      let match;
      while ((match = regex.exec(text)) !== null) {
        const hour = String(Number(match[1])).padStart(2, '0');
        const time = `${hour}:${match[2]}`;
        if (!found.includes(time)) found.push(time);
      }
      return found;
    }

    function createShifts(records) {
      const events = [];
      records.forEach(r => {
        if (!r.date) return;
        r.times.forEach(t => {
          events.push({
            dateKey: r.date.key,
            date: r.date,
            time: t,
            minutes: toMinutes(t),
            timestamp: timestamp(r.date.key, t)
          });
        });
      });

      events.sort((a, b) => a.timestamp - b.timestamp);
      const result = [], used = new Set();

      for (let i = 0; i < events.length; i++) {
        if (used.has(i)) continue;
        const start = events[i];
        let pairIndex = -1;

        for (let j = i + 1; j < events.length; j++) {
          if (used.has(j)) continue;
          const end = events[j];
          const diff = (end.timestamp - start.timestamp) / 60000;
          if (diff <= 0) continue;
          if (diff > 16 * 60) break;
          if (validPair(start, end)) {
            pairIndex = j;
            break;
          }
        }

        if (pairIndex !== -1) {
          const end = events[pairIndex];
          used.add(i);
          used.add(pairIndex);
          result.push(buildShift(start, end));
        } else {
          used.add(i);
          result.push({
            id: 'incomplete_' + start.timestamp,
            dateKey: start.dateKey,
            startDateKey: start.dateKey,
            endDateKey: null,
            startTime: start.time,
            endTime: null,
            incomplete: true,
            pay: 0,
            isSunday: getDay(start.dateKey) === 0,
            isNight: start.minutes >= 19 * 60,
            normalHours: 0, otHours: 0, normalPay: 0, otPay: 0
          });
        }
      }
      return result;
    }

    function validPair(start, end) {
      const day = getDay(start.dateKey);
      const s = start.minutes, e = end.minutes;
      if (day === 0) return (e > s || end.dateKey !== start.dateKey);
      if (s >= 19 * 60) return (end.dateKey !== start.dateKey) ? (e <= 12 * 60) : (e > s);
      if (s >= 7 * 60 + 30 && s <= 12 * 60) return (start.dateKey === end.dateKey && e >= 16 * 60);
      return false;
    }

    function buildShift(start, end) {
      const day = getDay(start.dateKey);
      if (day === 0) return calculateSunday(start, end);
      if (start.minutes >= 19 * 60) return calculateNight(start, end);
      return calculateDay(start, end);
    }

    function calculateSunday(start, end) {
      let s = start.minutes, e = end.minutes;
      if (s >= 16 * 60 + 30 && s <= 17 * 60) s = 17 * 60;
      e = Math.floor(e / 30) * 30;
      if (end.dateKey !== start.dateKey && e < s) e += 24 * 60;

      const minutes = Math.max(0, e - s);
      const pay = (minutes / 60) * SUNDAY_RATE;
      return {
        id: makeId(start, end),
        dateKey: start.dateKey,
        startDateKey: start.dateKey,
        endDateKey: end.dateKey,
        startTime: start.time,
        endTime: end.time,
        incomplete: false,
        isSunday: true,
        isNight: false,
        normalHours: minutes / 60,
        otHours: 0,
        normalPay: pay,
        otPay: 0,
        pay
      };
    }

    function calculateDay(start, end) {
      let s = start.minutes;
      if (s >= 7 * 60 + 30 && s <= 8 * 60) s = 8 * 60;

      let otMinutes = 0;
      if (end.minutes >= OT_START) {
        const roundedEnd = Math.floor(end.minutes / 30) * 30;
        otMinutes = Math.max(0, roundedEnd - OT_START);
      }
      const otPay = (otMinutes / 60) * OT_RATE;

      return {
        id: makeId(start, end),
        dateKey: start.dateKey,
        startDateKey: start.dateKey,
        endDateKey: end.dateKey,
        startTime: start.time,
        endTime: end.time,
        incomplete: false,
        isSunday: false,
        isNight: false,
        normalHours: 8,
        otHours: otMinutes / 60,
        normalPay: NORMAL_WAGE,
        otPay,
        pay: NORMAL_WAGE + otPay
      };
    }

    function calculateNight(start, end) {
      let s = start.minutes, e = end.minutes;
      if (s >= 19 * 60 + 30 && s <= 20 * 60) s = 20 * 60;
      if (end.dateKey !== start.dateKey && e < s) e += 24 * 60;

      let otMinutes = 0;
      if (e >= (5 * 60 + 30 + 24 * 60)) {
        const roundedEnd = Math.floor(e / 30) * 30;
        otMinutes = Math.max(0, roundedEnd - (5 * 60 + 30 + 24 * 60));
      }
      const otPay = (otMinutes / 60) * OT_RATE;

      return {
        id: makeId(start, end),
        dateKey: start.dateKey,
        startDateKey: start.dateKey,
        endDateKey: end.dateKey,
        startTime: start.time,
        endTime: end.time,
        incomplete: false,
        isSunday: false,
        isNight: true,
        normalHours: 8,
        otHours: otMinutes / 60,
        normalPay: NORMAL_WAGE,
        otPay,
        pay: NORMAL_WAGE + otPay
      };
    }

    function mergeShifts(newShifts) {
      const map = new Map();
      shifts.forEach(s => map.set(s.id, s));
      newShifts.forEach(s => map.set(s.id, s));
      shifts = Array.from(map.values()).sort((a, b) => `${a.dateKey} ${a.startTime}`.localeCompare(`${b.dateKey} ${b.startTime}`));
      saveData();
    }

    function saveData() {
      localStorage.setItem(STORAGE_KEY, JSON.stringify({
        version: 6,
        updatedAt: new Date().toISOString(),
        shifts
      }));
    }

    function loadData() {
      try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) { shifts = []; return; }
        const data = JSON.parse(raw);
        shifts = Array.isArray(data.shifts) ? data.shifts : [];
      } catch (e) {
        console.error(e);
        shifts = [];
      }
    }

    /* RENDER FUNCTIONS */
    function renderAll() {
      renderSummary();
      renderCalendar();
      renderPeriods();
      renderSalaryPeriods();
    }

    function renderSummary() {
      const completed = shifts.filter(s => !s.incomplete);
      const dates = new Set(completed.map(s => s.dateKey));
      const total = completed.reduce((sum, s) => sum + Number(s.pay || 0), 0);

      document.getElementById('summaryDays').textContent = dates.size;
      document.getElementById('summaryWorked').textContent = completed.length;
      document.getElementById('summaryMoney').textContent = money(total);
    }

    function renderCalendar() {
      const year = viewDate.getFullYear(), month = viewDate.getMonth();
      monthTitle.textContent = `${thaiMonth(month)} ${year + 543}`;
      calendar.innerHTML = '';

      ['อา','จ','อ','พ','พฤ','ศ','ส'].forEach(name => {
        const div = document.createElement('div');
        div.className = 'weekday';
        div.textContent = name;
        calendar.appendChild(div);
      });

      const first = new Date(year, month, 1).getDay();
      const max = new Date(year, month + 1, 0).getDate();

      for (let i = 0; i < first; i++) {
        const empty = document.createElement('div');
        empty.className = 'day empty';
        calendar.appendChild(empty);
      }

      for (let day = 1; day <= max; day++) {
        const key = `${year}-${pad(month + 1)}-${pad(day)}`;
        const cell = document.createElement('div');
        const shift = getShiftForDate(key);
        const status = getDayStatus(key, shift);

        cell.className = `day ${status.className}`;
        let html = `<div class="day-number">${day}</div>`;

        if (status.label) {
          html += `<div class="day-status">${status.label}</div>`;
        }

        if (shift && !shift.incomplete) {
          html += `
            <div class="day-time">${escapeHTML(shift.startTime)}–${escapeHTML(shift.endTime)}</div>
            <div class="day-money">${money(shift.pay)}</div>
          `;
        }

        cell.innerHTML = html;
        cell.onclick = () => openDay(key);
        calendar.appendChild(cell);
      }
    }

    function changeMonth(delta) {
      viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() + delta, 1);
      renderCalendar();
    }

    function getShiftForDate(key) {
      const list = shifts.filter(s => s.startDateKey === key || s.dateKey === key);
      return list.length === 0 ? null : list.sort((a, b) => String(a.startTime).localeCompare(String(b.startTime)))[0];
    }

    function getDayStatus(key, shift) {
      const today = startOfDay(new Date());
      const date = parseDateKey(key);

      if (date > today) return { className: 'future', label: 'ยังไม่ถึง' };
      if (shift) {
        return shift.incomplete ? { className: 'missed', label: 'ลืมอัป' } : { className: 'worked', label: null };
      }
      return { className: 'missed', label: 'ลืมอัป' };
    }

    function openDay(key) {
      const dayShifts = shifts.filter(s => s.startDateKey === key || s.dateKey === key);
      modalTitle.textContent = `📅 ${formatThaiDate(key)}`;

      if (dayShifts.length === 0) {
        modalBody.innerHTML = `<div class="empty-state">ไม่มีข้อมูลการทำงานวันนี้</div>`;
      } else {
        modalBody.innerHTML = dayShifts.map(s => createShiftHTML(s)).join('');
      }
      dayModal.classList.add('show');
    }

    function createShiftHTML(shift) {
      if (shift.incomplete) {
        return `
          <div class="shift-detail incomplete-detail">
            <div class="shift-detail-title">⚠️ พบเวลาเข้า แต่ยังจับคู่เวลาออกไม่ได้</div>
            <div>เวลาเข้า: <strong>${escapeHTML(shift.startTime)}</strong></div>
          </div>
        `;
      }
      return `
        <div class="shift-detail">
          <div class="shift-detail-title">
            ${shift.isSunday ? '🟠 วันอาทิตย์' : shift.isNight ? '🌙 กะกลางคืน' : '☀️ กะกลางวัน'}
          </div>
          <div>เวลา: <strong>${escapeHTML(shift.startTime)} – ${escapeHTML(shift.endTime)}</strong></div>
          <div>ค่าแรงปกติ: <strong>${money(shift.normalPay)}</strong></div>
          <div>OT: <strong>${Number(shift.otHours || 0).toFixed(2)} ชม.</strong></div>
          <div>ค่า OT: <strong>${money(shift.otPay)}</strong></div>
          <div class="shift-total">รวม: <strong>${money(shift.pay)}</strong></div>
        </div>
      `;
    }

    function closeModal() {
      dayModal.classList.remove('show');
    }

    function closeModalOutside(e) {
      if (e.target === dayModal) closeModal();
    }

    /* PAY PERIOD SYSTEM */
    function getPayPeriod(dateKey) {
      const date = parseDateKey(dateKey);
      const y = date.getFullYear(), m = date.getMonth(), d = date.getDate();
      let start, end, type, cycleNum;

      if (d >= 26) {
        type = '26-10';
        cycleNum = '01';
        start = new Date(y, m, 26);
        end = new Date(y, m + 1, 10);
      } else if (d <= 10) {
        type = '26-10';
        cycleNum = '01';
        start = new Date(y, m - 1, 26);
        end = new Date(y, m, 10);
      } else {
        type = '11-25';
        cycleNum = '02';
        start = new Date(y, m, 11);
        end = new Date(y, m, 25);
      }

      const startKey = formatDateKey(start), endKey = formatDateKey(end);
      const targetMonthName = thaiMonth(end.getMonth());
      
      // สร้างชื่อรูปแบบเต็มสำหรับแสดงผลใน Dropdown เมนูเลือกสลิป
      const displayLabel = `รอบจ่าย${cycleNum}/${targetMonthName} • ${start.getDate()} ${thaiMonth(start.getMonth())} - ${end.getDate()} ${thaiMonth(end.getMonth())} ${end.getFullYear() + 543}`;

      return {
        key: `${startKey}_${endKey}`,
        type,
        cycleNum,
        startDate: startKey,
        endDate: endKey,
        displayLabel,
        name: `${formatThaiDate(startKey)} – ${formatThaiDate(endKey)}`
      };
    }

    function getAllPayPeriods() {
      const map = new Map();
      shifts.forEach(s => {
        const period = getPayPeriod(s.startDateKey || s.dateKey);
        if (!map.has(period.key)) map.set(period.key, period);
      });
      return Array.from(map.values()).sort((a, b) => b.startDate.localeCompare(a.startDate));
    }

    function getPeriodShifts(period) {
      return shifts.filter(s => {
        const date = s.startDateKey || s.dateKey;
        return (date >= period.startDate && date <= period.endDate);
      });
    }

    function getPeriodSummary(period) {
      const periodShifts = getPeriodShifts(period);
      const completed = periodShifts.filter(s => !s.incomplete);
      const workDays = new Set(completed.map(s => s.startDateKey || s.dateKey)).size;

      const wage = completed.reduce((sum, s) => sum + Number(s.normalPay || 0), 0);
      const ot = completed.reduce((sum, s) => sum + Number(s.otPay || 0), 0);
      const total = completed.reduce((sum, s) => sum + Number(s.pay || 0), 0);

      return { period, shifts: periodShifts, completed, workDays, wage, ot, total };
    }

    function renderPeriods() {
      const allPeriods = getAllPayPeriods();
      if (allPeriods.length === 0) {
        periods.innerHTML = `<div class="empty-state"><div class="empty-icon">💰</div><div class="empty-title">ยังไม่มีข้อมูลรอบจ่าย</div><div class="empty-sub">เมื่อมีข้อมูลค่าแรง รอบจ่ายจะแสดงที่นี่</div></div>`;
        return;
      }

      const visible = allPeriods.slice(0, 2);
      let html = visible.map(p => renderPeriodCard(p)).join('');

      if (allPeriods.length > 2) {
        html += `<button type="button" class="period-more-btn" onclick="openPeriodsMore()">📋 ดูเพิ่มเติม (${allPeriods.length - 2} รอบ)</button>`;
      }

      periods.innerHTML = html;
    }

    function renderPeriodCard(period) {
      const data = getPeriodSummary(period);
      return `
        <div class="period-item">
          <div class="period-top">
            <div>
              <span class="period-badge">รอบ ${period.type}</span>
              <div class="period-name-text">${escapeHTML(period.name)}</div>
            </div>
            <div class="period-total-value">${money(data.total)}</div>
          </div>

          <div class="period-stats">
            <div class="period-stat">
              <span class="period-stat-label">วันทำงาน</span>
              <strong class="period-stat-value">${data.workDays} วัน</strong>
            </div>
            <div class="period-stat">
              <span class="period-stat-label">ค่าแรง</span>
              <strong class="period-stat-value">${money(data.wage)}</strong>
            </div>
            <div class="period-stat">
              <span class="period-stat-label">OT</span>
              <strong class="period-stat-value">${money(data.ot)}</strong>
            </div>
          </div>
        </div>
      `;
    }

    function openPeriodsMore() {
      const allPeriods = getAllPayPeriods();
      const list = document.getElementById('allPeriodsList');
      list.innerHTML = allPeriods.length ? allPeriods.map(p => renderPeriodCard(p)).join('') : `<div class="empty-state">ยังไม่มีรอบจ่าย</div>`;
      document.getElementById('periodsMoreModal').classList.add('show');
    }

    function closePeriodsMore() {
      document.getElementById('periodsMoreModal').classList.remove('show');
    }

    function closePeriodsMoreOutside(e) {
      if (e.target.id === 'periodsMoreModal') closePeriodsMore();
    }

    /* SALARY SLIP FUNCTIONS */
    function renderSalaryPeriods() {
      const allPeriods = getAllPayPeriods();
      const currentValue = salaryPeriod.value;

      salaryPeriod.innerHTML = `<option value="">-- เลือกรอบจ่าย --</option>`;
      allPeriods.forEach(p => {
        const option = document.createElement('option');
        option.value = p.key;
        option.textContent = p.displayLabel; // ใช้รูปแบบแสดงผลแบบใหม่
        salaryPeriod.appendChild(option);
      });

      if (allPeriods.some(p => p.key === currentValue)) salaryPeriod.value = currentValue;
      else if (allPeriods.length > 0) salaryPeriod.value = allPeriods[0].key;
    }

    function getSelectedSalaryData() {
      const key = salaryPeriod.value;
      if (!key) throw new Error('กรุณาเลือกรอบจ่ายก่อน');

      const period = getAllPayPeriods().find(p => p.key === key);
      if (!period) throw new Error('ไม่พบข้อมูลรอบจ่าย');

      const summary = getPeriodSummary(period);
      const room = Math.max(0, Number(document.getElementById('roomAllowance').value || 0));
      const rate = Math.min(100, Math.max(0, Number(document.getElementById('socialSecurityRate').value || 0)));

      const beforeDeduction = summary.total + room;
      const socialSecurity = (beforeDeduction * rate) / 100;
      const total = Math.max(0, beforeDeduction - socialSecurity);

      return {
        period,
        name: document.getElementById('employeeName').value.trim() || '-',
        employeeId: document.getElementById('employeeId').value.trim() || '-',
        workDays: summary.workDays,
        wage: summary.wage,
        ot: summary.ot,
        room,
        rate,
        beforeDeduction,
        socialSecurity,
        total
      };
    }

    function previewSalarySlip() {
      try {
        const data = getSelectedSalaryData();
        const body = document.getElementById('salaryPreviewBody');

        body.innerHTML = `
          <div class="salary-preview">
            <div class="salary-preview-header">
              <h3>สลิปเงินเดือน</h3>
              <p>${escapeHTML(data.period.name)}</p>
            </div>

            <div class="salary-preview-info">
              <div class="info-item">
                <span>ชื่อพนักงาน</span>
                <strong>${escapeHTML(data.name)}</strong>
              </div>
              <div class="info-item">
                <span>รหัสพนักงาน</span>
                <strong>${escapeHTML(data.employeeId)}</strong>
              </div>
              <div class="info-item">
                <span>วันทำงาน</span>
                <strong>${data.workDays} วัน</strong>
              </div>
            </div>

            <div class="salary-preview-table">
              <div class="salary-line">
                <span>ค่าแรง</span>
                <strong>${money(data.wage)}</strong>
              </div>
              <div class="salary-line">
                <span>OT</span>
                <strong>${money(data.ot)}</strong>
              </div>
              <div class="salary-line">
                <span>ค่าห้องเย็น</span>
                <strong>${money(data.room)}</strong>
              </div>

              <div class="salary-line salary-subtotal">
                <span>รวมก่อนหัก</span>
                <strong>${money(data.beforeDeduction)}</strong>
              </div>

              <div class="salary-line deduction">
                <span>ประกันสังคม ${data.rate}%</span>
                <strong class="text-red">- ${money(data.socialSecurity)}</strong>
              </div>
            </div>

            <div class="salary-net-preview">
              <span>เงินรับสุทธิ</span>
              <strong>฿${formatNumber(data.total)}</strong>
            </div>
          </div>
        `;

        document.getElementById('salaryPreviewModal').classList.add('show');
      } catch (e) {
        alert(e.message);
      }
    }

    function closeSalaryPreview() {
      document.getElementById('salaryPreviewModal').classList.remove('show');
    }

    function closeSalaryPreviewOutside(e) {
      if (e.target.id === 'salaryPreviewModal') closeSalaryPreview();
    }

    /* DATA MANAGEMENT */
    function deleteSelectedDate() {
      const input = document.getElementById('deleteDate');
      const key = input.value;
      if (!key) {
        alert('กรุณาเลือกวันที่ต้องการลบ');
        return;
      }

      if (!confirm(`คุณต้องการลบข้อมูลวันที่ ${formatThaiDate(key)} ใช่หรือไม่?`)) return;

      const initialCount = shifts.length;
      shifts = shifts.filter(s => s.startDateKey !== key && s.dateKey !== key);

      if (shifts.length < initialCount) {
        saveData();
        renderAll();
        alert(`ลบข้อมูลวันที่ ${formatThaiDate(key)} เรียบร้อยแล้ว`);
      } else {
        alert('ไม่พบข้อมูลในวันที่เลือก');
      }
      input.value = '';
    }

    /* PDF GENERATION */
    async function loadThaiFont(doc) {
      const res = await fetch('NotoSansThai-Regular.ttf');
      if (!res.ok) throw new Error('ไม่พบไฟล์ NotoSansThai-Regular.ttf');
      const buffer = await res.arrayBuffer();
      const bytes = new Uint8Array(buffer);
      let binary = '';
      for (let i = 0; i < bytes.length; i += 0x8000) {
        binary += String.fromCharCode(...bytes.subarray(i, Math.min(i + 0x8000, bytes.length)));
      }
      doc.addFileToVFS('NotoSansThai-Regular.ttf', btoa(binary));
      doc.addFont('NotoSansThai-Regular.ttf', 'NotoSansThai', 'normal');
      doc.setFont('NotoSansThai', 'normal');
    }

    async function generateSalaryPDF() {
      try {
        if (!window.jspdf) throw new Error('ไม่พบ jsPDF');

        const data = getSelectedSalaryData();
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

        await loadThaiFont(doc);

        const pageWidth = doc.internal.pageSize.getWidth();
        const margin = 16, right = pageWidth - margin, contentWidth = pageWidth - margin * 2;
        let y = 18;

        doc.setFont('NotoSansThai', 'normal');
        doc.setFontSize(20);
        doc.setTextColor(15, 23, 42);
        doc.text('สลิปเงินเดือน', margin, y);

        doc.setFontSize(8);
        doc.setTextColor(100, 116, 139);
        doc.text('SALARY SLIP', margin, y + 6);

        doc.setFontSize(9);
        doc.text('รอบจ่าย:', right - 50, y);
        doc.setTextColor(30, 41, 59);
        doc.text(data.period.name, right, y, { align: 'right' });

        y += 16;

        const infoBoxHeight = 28, colWidth = contentWidth / 2;
        doc.setDrawColor(226, 232, 240);
        doc.setFillColor(248, 250, 252);
        doc.roundedRect(margin, y, contentWidth, infoBoxHeight, 3, 3, 'FD');
        doc.line(margin + colWidth, y + 4, margin + colWidth, y + infoBoxHeight - 4);

        const leftCenter = margin + colWidth / 2, rightCenter = margin + colWidth + colWidth / 2;

        doc.setFontSize(8);
        doc.setTextColor(100, 116, 139);
        doc.text('ชื่อพนักงาน', leftCenter, y + 8, { align: 'center' });
        doc.text('รหัสพนักงาน', rightCenter, y + 8, { align: 'center' });

        doc.setFontSize(10);
        doc.setTextColor(30, 41, 59);
        doc.text(data.name || '-', leftCenter, y + 16, { align: 'center' });
        doc.text(data.employeeId || '-', rightCenter, y + 16, { align: 'center' });

        doc.setFontSize(8);
        doc.setTextColor(100, 116, 139);
        doc.text(`วันทำงาน: ${data.workDays} วัน`, leftCenter, y + 23, { align: 'center' });
        doc.text(`วันที่ออกเอกสาร: ${formatThaiDate(formatDateKey(new Date()))}`, rightCenter, y + 23, { align: 'center' });

        y += infoBoxHeight + 10;

        const tableBody = [
          ['ค่าแรงปกติ (Wage)', money(data.wage)],
          ['ค่าล่วงเวลา (OT)', money(data.ot)],
          ['ค่าห้องเย็น (Cold Room Allowance)', money(data.room)],
          ['รวมก่อนหัก (Gross Income)', money(data.beforeDeduction)],
          [`หัก ประกันสังคม (Social Security ${data.rate}%)`, `- ${money(data.socialSecurity)}`],
          ['เงินรับสุทธิ (Net Salary)', money(data.total)]
        ];

        doc.autoTable({
          startY: y,
          margin: { left: margin, right: margin },
          head: [['รายการ', 'จำนวนเงิน (บาท)']],
          body: tableBody,
          styles: { font: 'NotoSansThai', fontSize: 10, cellPadding: 4 },
          headStyles: { fillColor: [99, 91, 255], textColor: [255, 255, 255], fontStyle: 'bold' },
          columnStyles: {
            0: { cellWidth: 'auto' },
            1: { cellWidth: 50, halign: 'right' }
          },
          didParseCell: function(d) {
            if (d.row.index === 3) {
              d.cell.styles.fontStyle = 'bold';
              d.cell.styles.fillColor = [241, 245, 249];
            }
            if (d.row.index === 5) {
              d.cell.styles.fontStyle = 'bold';
              d.cell.styles.fillColor = [238, 237, 255];
              d.cell.styles.textColor = [99, 91, 255];
            }
          }
        });

        doc.save(`สลิปเงินเดือน_${data.employeeId}_${data.period.startDate}.pdf`);

      } catch (e) {
        alert(e.message);
      }
    }
  </script>
</body>
</html>
