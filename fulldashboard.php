<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Smart Blood Analyzer | Full CRUD + Dashboard + Symptoms</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1500px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .card h3 {
            color: #1e293b;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
            padding-left: 12px;
            font-weight: 600;
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 15px 0;
        }

        .metric-unit {
            font-size: 0.9rem;
            color: #475569;
        }

        .status-normal { color: #10b981; }
        .status-high { color: #ef4444; }
        .status-low { color: #f59e0b; }

        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 700;
            margin: 5px 6px 5px 0;
        }
        .badge-positive { background: #dc2626; color: white; }
        .badge-negative { background: #10b981; color: white; }

        .input-section {
            background: white;
            border-radius: 24px;
            padding: 24px;
            margin-top: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .input-group {
            margin-bottom: 18px;
        }
        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #0f172a;
            font-size: 0.9rem;
        }
        .input-group input, .input-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            font-size: 1rem;
            transition: all 0.2s;
            background: #f8fafc;
        }
        .input-group input:focus, .input-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.2);
            background: white;
        }

        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
            box-shadow: 0 5px 12px rgba(0,0,0,0.15);
        }
        button:hover { opacity: 0.92; transform: scale(0.98); }

        .records-wrapper {
            background: white;
            border-radius: 24px;
            padding: 20px;
            margin-top: 20px;
            overflow-x: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .records-wrapper h3 {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #0f172a;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            flex-wrap: wrap;
        }
        .patient-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            min-width: 1000px;
        }
        .patient-table th {
            background: #f1f5f9;
            padding: 14px 10px;
            text-align: left;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 2px solid #cbd5e1;
        }
        .patient-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .patient-table tr:hover { background: #fef9e3; }
        .action-btn {
            background: #3b82f6;
            border: none;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: bold;
            cursor: pointer;
            color: white;
            margin-right: 6px;
            box-shadow: none;
        }
        .action-btn.update-btn { background: #f59e0b; }
        .action-btn.delete-btn { background: #ef4444; }
        .action-btn:hover { transform: scale(1.02); opacity: 0.9; }
        .clear-all-btn { background: #475569; margin-left: 12px; padding: 8px 18px; font-size: 0.85rem; }
        .badge-infect { display: inline-block; background: #f1f5f9; border-radius: 30px; padding: 4px 8px; font-size: 0.7rem; font-weight: 600; margin-right: 5px; }
        .positive-tag { background: #fee2e2; color: #b91c1c; }
        .negative-tag { background: #dcfce7; color: #15803d; }
        .edit-mode-indicator {
            background: #fef3c7;
            padding: 10px 16px;
            border-radius: 60px;
            display: inline-block;
            margin-bottom: 15px;
            font-weight: 500;
        }
        /* SYMPTOMS STYLES - new addition without altering dashboard */
        .symptoms-panel {
            background: white;
            border-radius: 24px;
            padding: 24px;
            margin-top: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .symptoms-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
        }
        .symptoms-header h3 {
            color: #1e293b;
            font-weight: 700;
            font-size: 1.4rem;
            margin: 0;
            border-left: 4px solid #10b981;
            padding-left: 12px;
        }
        .symptoms-badge {
            background: #eef2ff;
            color: #1e40af;
            border-radius: 40px;
            padding: 5px 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .symptoms-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            min-height: 70px;
        }
        .symptom-tag {
            background: linear-gradient(135deg, #fef9c3, #fde047);
            color: #854d0e;
            padding: 8px 18px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: 0.1s ease;
        }
        .symptom-tag:before {
            content: "🩺";
            font-size: 1rem;
        }
        .no-symptoms {
            background: #f1f5f9;
            color: #475569;
            padding: 10px 20px;
            border-radius: 60px;
            font-style: italic;
            font-size: 0.9rem;
        }
        .device-simulate {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            flex-wrap: wrap;
            border-top: 1px dashed #cbd5e1;
            padding-top: 18px;
            justify-content: space-between;
            align-items: center;
        }
        .device-btn-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .device-btn {
            background: #334155;
            box-shadow: none;
            font-size: 0.8rem;
            padding: 8px 16px;
            margin-top: 0;
        }
        .device-btn.primary-device {
            background: linear-gradient(135deg, #059669, #10b981);
        }
        .symptom-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
            background: #f8fafc;
            padding: 8px 15px;
            border-radius: 60px;
        }
        .symptom-input-group input {
            border: 1px solid #cbd5e1;
            border-radius: 30px;
            padding: 8px 15px;
            width: 180px;
            background: white;
        }
        .small-device-btn {
            background: #3b82f6;
            padding: 8px 16px;
            font-size: 0.75rem;
        }
        @media (max-width: 780px) {
            .header h1 { font-size: 1.8rem; }
            .metric-value { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🩸 Smart Blood Analyzer Dashboard</h1>
        <p>Create, Read, Update, Delete — Complete Patient Blood Test Management + Device Symptom Capture</p>
    </div>

    <!-- Metric Cards Grid (latest result) -->
    <div class="grid" id="metricsGrid"></div>

    <!-- Add / Update Form Section -->
    <div class="input-section" id="formSection">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <h3 id="formTitle">📝 Add New Test Result</h3>
            <button id="cancelUpdateBtn" style="background: #6c757d; display: none;" onclick="cancelUpdateMode()">✖️ Cancel Update</button>
        </div>
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div class="input-group"><label>👤 Patient Name *</label><input type="text" id="patientName" placeholder="Full name" value="Emma Watson"></div>
            <div class="input-group"><label>🎂 Age</label><input type="number" id="age" placeholder="Age" value="42"></div>
            <div class="input-group"><label>⚥ Gender</label><select id="gender"><option>Female</option><option>Male</option><option>Other</option></select></div>
            <div class="input-group"><label>🍬 Glucose (mg/dL)</label><input type="number" id="glucose" value="105" step="1"></div>
            <div class="input-group"><label>❤️ Cholesterol (mg/dL)</label><input type="number" id="cholesterol" value="190" step="1"></div>
            <div class="input-group"><label>🩸 Hemoglobin (g/dL)</label><input type="number" id="hemoglobin" value="13.8" step="0.1"></div>
            <div class="input-group"><label>🌡️ Temperature (°C)</label><input type="number" id="temperature" value="36.7" step="0.1"></div>
            <div class="input-group"><label>🔥 CRP (mg/L)</label><input type="number" id="crp" value="4.2" step="0.1"></div>
        </div>
        <div class="input-group">
            <label>🧫 Infectious Diseases Screening</label>
            <div style="display: flex; gap: 18px; flex-wrap: wrap;">
                <label><input type="checkbox" id="covid"> COVID-19</label>
                <label><input type="checkbox" id="malaria"> Malaria</label>
                <label><input type="checkbox" id="hiv"> HIV</label>
                <label><input type="checkbox" id="hepatitis"> Hepatitis B</label>
            </div>
        </div>
        <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: space-between; align-items: center;">
            <button id="saveBtn" onclick="handleSaveOrUpdate()">💾 Save Test Result</button>
            <button onclick="addDemoRecord()" style="background: #3b82f6;">📊 Add Demo Record</button>
        </div>
    </div>

    <!-- ========== NEW: SYMPTOMS SPACE (Device-checked symptoms) ========== -->
    <div class="symptoms-panel" id="symptomsSpace">
        <div class="symptoms-header">
            <h3>🩺 Device-Captured Symptoms</h3>
            <span class="symptoms-badge" id="symptomDeviceStatus">📟 Integrated Check</span>
        </div>
        <div class="symptoms-container" id="symptomsListContainer">
            <!-- dynamic symptoms will appear here -->
            <div class="no-symptoms">⚕️ No symptoms recorded by device. Perform device check or add manually.</div>
        </div>
        <div class="device-simulate">
            <div class="device-btn-group">
                <button class="device-btn primary-device" id="simulateDeviceCheckBtn" onclick="simulateDeviceHealthCheck()">🩺 Run Device Check (Simulate Symptoms)</button>
                <button class="device-btn" onclick="clearCurrentSymptoms()">🗑️ Clear All Symptoms</button>
            </div>
            <div class="symptom-input-group">
                <input type="text" id="customSymptomInput" placeholder="e.g., Headache, Fatigue, Cough" autocomplete="off">
                <button class="small-device-btn" onclick="addCustomSymptom()">➕ Add Symptom</button>
            </div>
        </div>
        <div style="font-size: 0.75rem; color: #475569; margin-top: 12px; text-align: right;">
            💡 Symptoms linked to current patient (saved with test record)
        </div>
    </div>
    <!-- ========== END SYMPTOMS SPACE ========== -->

    <!-- Glucose Trend Chart -->
    <div class="input-section chart-container">
        <h3>📈 Glucose Trend Over All Tests</h3>
        <canvas id="glucoseChart"></canvas>
    </div>

    <!-- PATIENT RECORDS TABLE (Full CRUD) -->
    <div class="records-wrapper">
        <h3>
            <span>📋 Complete Patient Records (CRUD enabled)</span>
            <span style="font-size: 0.9rem; font-weight: normal; background: #eef2ff; padding: 4px 12px; border-radius: 40px;">
                🧾 Total: <span id="recordCount">0</span> tests
            </span>
            <button onclick="clearAllRecords()" class="clear-all-btn">🗑️ Clear All Records</button>
        </h3>
        <div style="overflow-x: auto;">
            <table class="patient-table" id="patientRecordsTable">
                <thead>
                    <tr><th>Date & Time</th><th>Patient</th><th>Age/Gender</th><th>Glucose</th><th>Chol</th><th>Hgb</th><th>Temp</th><th>CRP</th><th>Infections</th><th>Symptoms (Device)</th><th>Actions</th></tr>
                </thead>
                <tbody id="recordsTableBody">
                    <tr><td colspan="11" style="text-align:center; padding: 32px;">✨ Loading records......</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // ---------- DATA ----------
    let testHistory = [];          // each item: full patient object with unique id + symptoms array
    let glucoseChart;
    let currentEditId = null;      // if not null, we are in update mode
    // TEMPORARY symptoms for current form (before saving)
    let currentDeviceSymptoms = [];   // array of symptom strings

    // Reference ranges
    const ranges = {
        glucose: { min: 70, max: 140, unit: "mg/dL" },
        cholesterol: { min: 125, max: 200, unit: "mg/dL" },
        hemoglobin: { min: 12, max: 17.5, unit: "g/dL" },
        temperature: { min: 36.1, max: 37.2, unit: "°C" },
        crp: { min: 0, max: 10, unit: "mg/L" }
    };

    function getStatus(value, range) {
        if (value < range.min) return { text: "LOW", class: "status-low" };
        if (value > range.max) return { text: "HIGH", class: "status-high" };
        return { text: "NORMAL", class: "status-normal" };
    }

    function generateClinicalNote(record) {
        const gluStatus = getStatus(record.glucose, ranges.glucose);
        const tempStatus = getStatus(record.temperature, ranges.temperature);
        if (gluStatus.text !== "NORMAL" && tempStatus.text !== "NORMAL") return "⚠️ Abnormal glucose & temperature.";
        if (gluStatus.text !== "NORMAL") return `⚠️ Glucose ${gluStatus.text}.`;
        if (tempStatus.text !== "NORMAL") return `⚠️ Temperature ${tempStatus.text}.`;
        if (record.crp > 10) return "🔥 Elevated CRP – inflammation suspected.";
        return "✅ Values within range.";
    }

    // render symptoms panel (UI)
    function renderSymptomsPanel() {
        const container = document.getElementById('symptomsListContainer');
        if (!container) return;
        if (!currentDeviceSymptoms.length) {
            container.innerHTML = '<div class="no-symptoms">⚕️ No symptoms recorded by device. Perform device check or add manually.</div>';
            return;
        }
        let symptomsHtml = '';
        currentDeviceSymptoms.forEach((sym, idx) => {
            symptomsHtml += `<div class="symptom-tag">${escapeHtml(sym)} <span style="cursor:pointer; margin-left:6px; font-weight:bold;" onclick="removeSymptom(${idx})">✖️</span></div>`;
        });
        container.innerHTML = symptomsHtml;
    }

    window.removeSymptom = function(idx) {
        currentDeviceSymptoms.splice(idx, 1);
        renderSymptomsPanel();
        updateSymptomDeviceStatus();
    };

    window.addCustomSymptom = function() {
        const input = document.getElementById('customSymptomInput');
        const symptomText = input.value.trim();
        if (!symptomText) return;
        currentDeviceSymptoms.push(symptomText);
        input.value = '';
        renderSymptomsPanel();
        updateSymptomDeviceStatus();
    };

    window.clearCurrentSymptoms = function() {
        if (currentDeviceSymptoms.length && confirm("Clear all current symptoms for this test?")) {
            currentDeviceSymptoms = [];
            renderSymptomsPanel();
            updateSymptomDeviceStatus();
        } else if (!currentDeviceSymptoms.length) {
            alert("No symptoms to clear.");
        }
    };

    function updateSymptomDeviceStatus() {
        const statusSpan = document.getElementById('symptomDeviceStatus');
        if (statusSpan) {
            statusSpan.innerHTML = currentDeviceSymptoms.length ? `📋 ${currentDeviceSymptoms.length} symptom(s) captured` : `🔍 No symptoms yet`;
        }
    }

    // Intelligent device simulation: based on lab values + random real symptoms
    window.simulateDeviceHealthCheck = function() {
        // read current form values to produce relevant symptoms
        const glucoseVal = parseFloat(document.getElementById('glucose').value);
        const tempVal = parseFloat(document.getElementById('temperature').value);
        const crpVal = parseFloat(document.getElementById('crp').value);
        const covidChecked = document.getElementById('covid').checked;
        const malariaChecked = document.getElementById('malaria').checked;
        const hepChecked = document.getElementById('hepatitis').checked;
        
        let newSymptoms = [];
        // glucose related symptoms
        if (glucoseVal > 150) newSymptoms.push("Polydipsia (excess thirst)", "Frequent urination");
        else if (glucoseVal < 60) newSymptoms.push("Dizziness", "Sweating");
        // temperature abnormalities
        if (tempVal > 37.5) newSymptoms.push("Fever", "Chills");
        else if (tempVal < 35.5) newSymptoms.push("Hypothermia sensation", "Shivering");
        // CRP elevated -> inflammation symptoms
        if (crpVal > 15) newSymptoms.push("Body aches", "General fatigue");
        else if (crpVal > 8) newSymptoms.push("Mild fatigue");
        // Infectious markers
        if (covidChecked) newSymptoms.push("Dry cough", "Loss of smell", "Sore throat");
        if (malariaChecked) newSymptoms.push("Cyclic fever", "Joint pain");
        if (hepChecked) newSymptoms.push("Jaundice (yellowish skin)", "Nausea");
        
        // Add a couple generic contextual symptoms for realism if none exist
        if (newSymptoms.length === 0) {
            if (glucoseVal > 130) newSymptoms.push("Mild headache");
            else if (tempVal > 37.2) newSymptoms.push("Warm skin");
            else newSymptoms.push("Asymptomatic (device reports no symptoms)");
        } else {
            // remove duplicates
            newSymptoms = [...new Set(newSymptoms)];
        }
        // limit to max 5 for cleanliness
        if (newSymptoms.length > 5) newSymptoms = newSymptoms.slice(0,5);
        currentDeviceSymptoms = newSymptoms;
        renderSymptomsPanel();
        updateSymptomDeviceStatus();
        // flash success message
        const deviceBtn = document.getElementById('simulateDeviceCheckBtn');
        const originalText = deviceBtn.innerText;
        deviceBtn.innerText = "✅ Device analyzed!";
        setTimeout(() => { deviceBtn.innerText = originalText; }, 1500);
    };

    // Update Dashboard Cards (latest record)
    function updateDashboard() {
        if (testHistory.length === 0) {
            document.getElementById('metricsGrid').innerHTML = '<div class="card"><h3>📊 No Data</h3><p>Add or update records using the form. ✨</p></div>';
            return;
        }
        const latest = [...testHistory].sort((a,b) => new Date(b.timestamp) - new Date(a.timestamp))[0];
        const metrics = [
            { title: "🍬 Glucose", value: latest.glucose, range: ranges.glucose },
            { title: "❤️ Cholesterol", value: latest.cholesterol, range: ranges.cholesterol },
            { title: "🩸 Hemoglobin", value: latest.hemoglobin, range: ranges.hemoglobin },
            { title: "🌡️ Temperature", value: latest.temperature, range: ranges.temperature },
            { title: "🔥 CRP (inflammation)", value: latest.crp, range: ranges.crp }
        ];
        let html = '';
        metrics.forEach(metric => {
            const status = getStatus(metric.value, metric.range);
            html += `<div class="card"><h3>${metric.title}</h3><div class="metric-value ${status.class}">${metric.value}</div>
                    <div class="metric-unit">${metric.range.unit}</div><div class="${status.class}" style="margin-top: 10px; font-weight: bold;">${status.text}</div>
                    <small>Ref: ${metric.range.min}–${metric.range.max} ${metric.range.unit}</small></div>`;
        });
        let infectiousHtml = `<div class="card"><h3>🦠 Infectious Panel</h3>`;
        infectiousHtml += `<div><span class="badge ${latest.covid ? 'badge-positive' : 'badge-negative'}">🦠 COVID-19: ${latest.covid ? 'POSITIVE' : 'NEGATIVE'}</span></div>`;
        infectiousHtml += `<div><span class="badge ${latest.malaria ? 'badge-positive' : 'badge-negative'}">🦟 Malaria: ${latest.malaria ? 'POSITIVE' : 'NEGATIVE'}</span></div>`;
        infectiousHtml += `<div><span class="badge ${latest.hiv ? 'badge-positive' : 'badge-negative'}">🧬 HIV: ${latest.hiv ? 'POSITIVE' : 'NEGATIVE'}</span></div>`;
        infectiousHtml += `<div><span class="badge ${latest.hepatitis ? 'badge-positive' : 'badge-negative'}">🧫 Hepatitis B: ${latest.hepatitis ? 'POSITIVE' : 'NEGATIVE'}</span></div></div>`;
        html += infectiousHtml;
        let symptomDisplay = latest.symptoms && latest.symptoms.length ? latest.symptoms.slice(0,4).map(s => `<span class="badge-infect" style="background:#fef3c7;">🩺 ${escapeHtml(s)}</span>`).join(' ') : '<span class="badge-infect negative-tag">No symptoms recorded</span>';
        html += `<div class="card"><h3>👤 Latest Patient</h3><p><strong>Name:</strong> ${escapeHtml(latest.patientName)}</p>
                <p><strong>Age:</strong> ${latest.age} yrs | ${latest.gender}</p><p><strong>Test:</strong> ${new Date(latest.timestamp).toLocaleString()}</p>
                <p><strong>Device Symptoms:</strong> ${symptomDisplay}</p>
                <p><strong>Note:</strong> ${generateClinicalNote(latest)}</p></div>`;
        document.getElementById('metricsGrid').innerHTML = html;
        updateGlucoseChart();
    }

    function updateGlucoseChart() {
        const ctx = document.getElementById('glucoseChart').getContext('2d');
        const sortedByDate = [...testHistory].sort((a,b) => new Date(a.timestamp) - new Date(b.timestamp));
        const glucoseValues = sortedByDate.map(t => t.glucose);
        const labels = sortedByDate.map((_, idx) => `Test ${idx+1}`);
        if (glucoseChart) glucoseChart.destroy();
        glucoseChart = new Chart(ctx, {
            type: 'line',
            data: { labels: labels, datasets: [{ label: 'Glucose (mg/dL)', data: glucoseValues, borderColor: '#667eea', backgroundColor: 'rgba(102,126,234,0.1)', tension: 0.3, fill: true, pointBackgroundColor: '#764ba2' }] },
            options: { responsive: true, maintainAspectRatio: true, plugins: { tooltip: { callbacks: { label: (ctx) => `Glucose: ${ctx.raw} mg/dL` } } } }
        });
    }

    function renderPatientRecordsTable() {
        const tbody = document.getElementById('recordsTableBody');
        const recordCountSpan = document.getElementById('recordCount');
        recordCountSpan.innerText = testHistory.length;
        if (!testHistory.length) {
            tbody.innerHTML = '<tr><td colspan="11" style="text-align:center; padding: 32px;">📭 No patient records. Add new test or demo record.</td></tr>';
            return;
        }
        let rows = '';
        [...testHistory].reverse().forEach(record => {
            const infectionsHtml = [];
            if (record.covid) infectionsHtml.push('<span class="badge-infect positive-tag">COVID+</span>');
            if (record.malaria) infectionsHtml.push('<span class="badge-infect positive-tag">Malaria+</span>');
            if (record.hiv) infectionsHtml.push('<span class="badge-infect positive-tag">HIV+</span>');
            if (record.hepatitis) infectionsHtml.push('<span class="badge-infect positive-tag">HepB+</span>');
            if (infectionsHtml.length === 0) infectionsHtml.push('<span class="badge-infect negative-tag">All negative</span>');
            const glucoseStatus = getStatus(record.glucose, ranges.glucose).text.substring(0,1);
            const symptomsDisplay = (record.symptoms && record.symptoms.length) ? record.symptoms.slice(0,3).map(s => `<span class="badge-infect" style="background:#e0f2fe;">${escapeHtml(s)}</span>`).join(' ') + (record.symptoms.length>3 ? ' +more' : '') : '<span class="badge-infect negative-tag">—</span>';
            rows += `<tr>
                <td style="white-space: nowrap;">${new Date(record.timestamp).toLocaleString()}</td>
                <td><strong>${escapeHtml(record.patientName)}</strong></td>
                <td>${record.age} / ${record.gender.charAt(0)}</td>
                <td>${record.glucose} <span style="font-size:0.7rem;">(${glucoseStatus})</span></td>
                <td>${record.cholesterol}</td><td>${record.hemoglobin}</td><td>${record.temperature}</td><td>${record.crp}</td>
                <td>${infectionsHtml.join(' ')}</td>
                <td>${symptomsDisplay}</td>
                <td><button class="action-btn update-btn" onclick="prepareUpdate('${record.id}')">✏️ Update</button>
                    <button class="action-btn delete-btn" onclick="deleteRecordById('${record.id}')">🗑️ Del</button></td>
            </tr>`;
        });
        tbody.innerHTML = rows;
    }

    function generateId() { return Date.now().toString(36) + Math.random().toString(36).substr(2, 5); }

    function handleSaveOrUpdate() {
        if (currentEditId !== null) updateExistingRecord();
        else createNewRecord();
    }

    function createNewRecord() {
        const patientName = document.getElementById('patientName').value.trim();
        if (!patientName) { alert("❌ Patient name is required"); return; }
        const age = parseInt(document.getElementById('age').value);
        if (isNaN(age) || age < 0) { alert("Valid age required"); return; }
        const newRecord = {
            id: generateId(), timestamp: new Date().toISOString(), patientName: patientName, age: age,
            gender: document.getElementById('gender').value, glucose: parseFloat(document.getElementById('glucose').value),
            cholesterol: parseFloat(document.getElementById('cholesterol').value), hemoglobin: parseFloat(document.getElementById('hemoglobin').value),
            temperature: parseFloat(document.getElementById('temperature').value), crp: parseFloat(document.getElementById('crp').value),
            covid: document.getElementById('covid').checked, malaria: document.getElementById('malaria').checked,
            hiv: document.getElementById('hiv').checked, hepatitis: document.getElementById('hepatitis').checked,
            symptoms: [...currentDeviceSymptoms]   // store symptoms captured
        };
        if (isNaN(newRecord.glucose) || isNaN(newRecord.cholesterol) || isNaN(newRecord.hemoglobin) || isNaN(newRecord.temperature) || isNaN(newRecord.crp)) {
            alert("Please fill all numeric fields correctly."); return;
        }
        testHistory.push(newRecord);
        resetSymptomsAfterSave();
        resetFormFieldsAfterSubmit();
        updateDashboard();
        renderPatientRecordsTable();
        alert(`✅ New test for ${newRecord.patientName} added with ${newRecord.symptoms.length} symptoms.`);
    }

    function updateExistingRecord() {
        const index = testHistory.findIndex(r => r.id === currentEditId);
        if (index === -1) { cancelUpdateMode(); return; }
        const updatedRecord = { ...testHistory[index],
            patientName: document.getElementById('patientName').value.trim(),
            age: parseInt(document.getElementById('age').value), gender: document.getElementById('gender').value,
            glucose: parseFloat(document.getElementById('glucose').value), cholesterol: parseFloat(document.getElementById('cholesterol').value),
            hemoglobin: parseFloat(document.getElementById('hemoglobin').value), temperature: parseFloat(document.getElementById('temperature').value),
            crp: parseFloat(document.getElementById('crp').value), covid: document.getElementById('covid').checked,
            malaria: document.getElementById('malaria').checked, hiv: document.getElementById('hiv').checked,
            hepatitis: document.getElementById('hepatitis').checked, symptoms: [...currentDeviceSymptoms],
            timestamp: new Date().toISOString()
        };
        testHistory[index] = updatedRecord;
        cancelUpdateMode();
        updateDashboard();
        renderPatientRecordsTable();
        alert(`✏️ Record updated.`);
    }

    window.prepareUpdate = function(id) {
        const record = testHistory.find(r => r.id === id);
        if (!record) return;
        currentEditId = id;
        document.getElementById('patientName').value = record.patientName;
        document.getElementById('age').value = record.age; document.getElementById('gender').value = record.gender;
        document.getElementById('glucose').value = record.glucose; document.getElementById('cholesterol').value = record.cholesterol;
        document.getElementById('hemoglobin').value = record.hemoglobin; document.getElementById('temperature').value = record.temperature;
        document.getElementById('crp').value = record.crp; document.getElementById('covid').checked = record.covid;
        document.getElementById('malaria').checked = record.malaria; document.getElementById('hiv').checked = record.hiv;
        document.getElementById('hepatitis').checked = record.hepatitis;
        currentDeviceSymptoms = record.symptoms ? [...record.symptoms] : [];
        renderSymptomsPanel(); updateSymptomDeviceStatus();
        document.getElementById('formTitle').innerHTML = '✏️ UPDATE TEST RESULT (Editing)';
        document.getElementById('saveBtn').innerHTML = '🔄 Update Record';
        document.getElementById('cancelUpdateBtn').style.display = 'inline-block';
        document.getElementById('formSection').scrollIntoView({ behavior: 'smooth' });
    };

    window.cancelUpdateMode = function() {
        currentEditId = null; resetSymptomsAfterSave();
        document.getElementById('formTitle').innerHTML = '📝 Add New Test Result';
        document.getElementById('saveBtn').innerHTML = '💾 Save Test Result';
        document.getElementById('cancelUpdateBtn').style.display = 'none';
        resetFormFieldsToDefault();
        renderSymptomsPanel(); updateSymptomDeviceStatus();
    };

    function resetSymptomsAfterSave() {
        currentDeviceSymptoms = [];
        renderSymptomsPanel();
        updateSymptomDeviceStatus();
    }

    function resetFormFieldsAfterSubmit() {
        if (currentEditId !== null) return;
        document.getElementById('covid').checked = false; document.getElementById('malaria').checked = false;
        document.getElementById('hiv').checked = false; document.getElementById('hepatitis').checked = false;
        document.getElementById('patientName').value = ''; document.getElementById('age').value = '';
        document.getElementById('glucose').value = '95'; document.getElementById('cholesterol').value = '180';
        document.getElementById('hemoglobin').value = '13.5'; document.getElementById('temperature').value = '36.6';
        document.getElementById('crp').value = '3'; document.getElementById('gender').value = 'Female';
        resetSymptomsAfterSave();
        document.getElementById('patientName').focus();
    }
    
    function resetFormFieldsToDefault() {
        document.getElementById('patientName').value = 'New Patient';
        document.getElementById('age').value = '35'; document.getElementById('glucose').value = '100';
        document.getElementById('cholesterol').value = '185'; document.getElementById('hemoglobin').value = '14.0';
        document.getElementById('temperature').value = '36.7'; document.getElementById('crp').value = '4';
        document.getElementById('covid').checked = false; document.getElementById('malaria').checked = false;
        document.getElementById('hiv').checked = false; document.getElementById('hepatitis').checked = false;
        document.getElementById('gender').value = 'Female';
    }

    window.deleteRecordById = function(id) {
        if (confirm("Delete?")) { testHistory = testHistory.filter(r => r.id !== id); if(!testHistory.length) cancelUpdateMode(); updateDashboard(); renderPatientRecordsTable(); alert("Deleted.");}
    };
    window.clearAllRecords = function() { if(confirm("DELETE ALL?")){ testHistory = []; cancelUpdateMode(); updateDashboard(); renderPatientRecordsTable(); }};
    window.addDemoRecord = function() {
        testHistory.push({ id: generateId(), timestamp: new Date().toISOString(), patientName: "Michael Chen", age: 58, gender: "Male", glucose: 165, cholesterol: 232, hemoglobin: 14.9, temperature: 37.5, crp: 18.2, covid: false, malaria: false, hiv: false, hepatitis: true, symptoms: ["Fatigue", "Body aches"] });
        updateDashboard(); renderPatientRecordsTable(); alert("Demo with symptoms added.");
        if(currentEditId) cancelUpdateMode();
    };
    function escapeHtml(str) { if(!str) return ''; return str.replace(/[&<>]/g, function(m){ if(m==='&') return '&amp;'; if(m==='<') return '&lt;'; if(m==='>') return '&gt;'; return m;});}
    function initSampleData() {
        if(testHistory.length===0){
            testHistory.push({ id: generateId(), timestamp: new Date(Date.now() - 172800000).toISOString(), patientName: "Olivia Martinez", age: 29, gender: "Female", glucose: 92, cholesterol: 172, hemoglobin: 13.2, temperature: 36.5, crp: 2.5, covid: false, malaria: false, hiv: false, hepatitis: false, symptoms: ["Mild fatigue"] });
            testHistory.push({ id: generateId(), timestamp: new Date(Date.now() - 86400000).toISOString(), patientName: "Robert Yang", age: 47, gender: "Male", glucose: 128, cholesterol: 208, hemoglobin: 15.0, temperature: 37.0, crp: 7.8, covid: true, malaria: false, hiv: false, hepatitis: false, symptoms: ["Cough", "Loss of taste"] });
            testHistory.push({ id: generateId(), timestamp: new Date().toISOString(), patientName: "Sophia Williams", age: 54, gender: "Female", glucose: 112, cholesterol: 194, hemoglobin: 12.7, temperature: 36.9, crp: 5.1, covid: false, malaria: true, hiv: false, hepatitis: false, symptoms: ["Chills", "Sweating"] });
        }
        updateDashboard(); renderPatientRecordsTable();
    }
    initSampleData();
    renderSymptomsPanel();
    updateSymptomDeviceStatus();
</script>
</body>
</html>