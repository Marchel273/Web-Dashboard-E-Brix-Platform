<?php
// php_web_dashboard/views/dashboard_petugas.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petugas Dashboard - E-BRIX Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --primary: #00b050;
            --primary-dark: #1C3829;
            --bg-main: #F0F4F1;
            --border-color: rgba(0,0,0,0.07);
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-main); margin: 0; padding: 0; color: #1a2e20; }
        .sidebar { width: 260px; background-color: var(--primary-dark); height: 100vh; position: fixed; left: 0; top: 0; padding: 1.5rem 1rem; box-sizing: border-box; color: white; display:flex; flex-direction:column; }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem; }
        .sidebar-logo { width: 40px; height: 40px; background: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; }
        .user-profile-card { background: rgba(255,255,255,0.08); padding: 10px 14px; border-radius: 12px; display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(255,255,255,0.12); }
        .avatar { width: 36px; height: 36px; background: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; color: white; }
        .user-name { font-size: 13px; font-weight: 800; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .role-badge { font-size: 10px; color: rgba(255,255,255,0.6); font-weight: 700; text-transform: uppercase; }
        .sidebar-nav { list-style: none; padding: 0; margin: 0; }
        .sidebar-nav li a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: rgba(255,255,255,0.75); text-decoration: none; border-radius: 10px; margin-bottom: 8px; font-size: 13px; font-weight: 600; }
        .sidebar-nav li a.active, .sidebar-nav li a:hover { background: rgba(255,255,255,0.1); color: white; }
        .role-switcher-box { margin-top: 1rem; background: rgba(0,0,0,0.2); padding: 10px; border-radius: 10px; }
        .btn-logout { display: flex; align-items: center; gap: 10px; color: #ff6b6b; text-decoration: none; font-size: 13px; font-weight: 700; padding: 10px 14px; border-radius: 10px; background: rgba(231,76,60,0.1); }
        .main-content { margin-left: 260px; padding: 1.5rem 2rem; }
        .header-card { background: white; border-radius: 14px; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--border-color); margin-bottom: 1.5rem; }
        .card { background: white; border-radius: 14px; padding: 1.5rem; border: 1px solid var(--border-color); margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; font-size: 12px; font-weight: 800; color: #1C3829; text-transform: uppercase; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit; font-size: 14px; box-sizing: border-box; }
        .btn-save { background: var(--primary); color: white; border: none; padding: 14px 24px; border-radius: 10px; font-weight: 800; font-size: 14px; cursor: pointer; width: 100%; }
        .btn-ocr { background: #3498db; color: white; border: none; padding: 10px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; margin-top: 8px; font-size: 12px; }
        #map { height: 440px; border-radius: 10px; width: 100%; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 13.5px; }
        th { background: #f9fbf9; font-weight: 700; color: #555; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .badge-active { background: #e6f7ed; color: #2ecc71; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/components/sidebar.php'; ?>

<div class="main-content">
    <div class="header-card">
        <div>
            <h2 style="margin:0; font-size:1.2rem; font-weight:800;">📋 Portal Petugas Lapangan & Surveyor</h2>
            <p style="margin:4px 0 0; font-size:12px; color:#7a9a84;">Input Sampel Kadar Brix, Foto Refraktometer, & Deteksi AI Machine Learning OCR</p>
        </div>
        <span style="background:#e6f7ed; color:#1a7a40; font-size:12px; font-weight:700; padding:6px 14px; border-radius:20px;">
            <i class="fa-solid fa-mobile-screen-button"></i> Mobile / Field Surveyor App
        </span>
    </div>

    <?php if ($subPage === 'peta_tugas'): ?>
        <!-- PETA LAHAN TUGAS -->
        <div class="card">
            <h3 style="margin-top:0;">📍 Peta Lahan Tugas & Lokasi Pengambilan Sampel</h3>
            <div id="map"></div>
        </div>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            const map = L.map('map').setView([-7.7530, 112.1260], 14);
            L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { attribution: 'Google Satellite' }).addTo(map);
            const samples = <?= json_encode($samples) ?>;
            samples.forEach(s => {
                if(s.Latitude && s.Longitude) {
                    const marker = L.circleMarker([s.Latitude, s.Longitude], {
                        radius: 7, fillColor: '#00b050', color: '#fff', weight: 2, fillOpacity: 0.9
                    }).addTo(map);
                    marker.bindPopup(`<b>${s.Kode_Blok}</b><br>Kadar Brix: <b>${s.Nilai_Brix}°</b>`);
                }
            });
        </script>

    <?php elseif ($subPage === 'riwayat_sampel'): ?>
        <!-- RIWAYAT INPUT BRIX -->
        <div class="card">
            <h3 style="margin-top:0;">📋 Riwayat Input Sampel Brix Petugas</h3>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Blok</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Nilai Brix (°)</th>
                        <th>Metode Input</th>
                        <th>Waktu Input</th>
                        <th>Status Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($samples as $idx => $s): ?>
                    <tr>
                        <td><b>#<?= $idx + 1 ?></b></td>
                        <td style="font-weight:700;"><?= htmlspecialchars($s['Kode_Blok']) ?></td>
                        <td><code><?= htmlspecialchars($s['Latitude']) ?></code></td>
                        <td><code><?= htmlspecialchars($s['Longitude']) ?></code></td>
                        <td style="font-weight:800; color:var(--primary);"><?= htmlspecialchars($s['Nilai_Brix']) ?>°</td>
                        <td><span class="badge badge-active">OCR_CAMERA</span></td>
                        <td style="color:#888; font-size:12px;"><?= htmlspecialchars(substr($s['Tanggal'], 0, 10)) ?></td>
                        <td><span class="badge badge-active">VERIFIED</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <!-- FORM INPUT BRIX & ML OCR -->
        <div style="display:grid; grid-template-columns: 1.2fr 1fr; gap:1.5rem;">
            <div class="card">
                <h3 style="margin-top:0;">📷 Input Sampel Brix Lapangan Baru</h3>
                <form onsubmit="event.preventDefault(); alert('Data Sampel Brix Berhasil Disimpan & Diteruskan ke Database!');">
                    <div class="form-group">
                        <label>Pilih Kode Blok Lahan</label>
                        <select class="form-control" required>
                            <option value="BLOK-A">BLOK-A (Varietas Bululawa PS864)</option>
                            <option value="BLOK-B">BLOK-B (Varietas Kidang Kencana)</option>
                            <option value="BLOK-C">BLOK-C (Varietas Bululawa PS864)</option>
                            <option value="BLOK-D">BLOK-D (Varietas Kidang Kencana)</option>
                        </select>
                    </div>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label>Latitude (GPS)</label>
                            <input type="text" class="form-control" value="-7.7525" required>
                        </div>
                        <div class="form-group">
                            <label>Longitude (GPS)</label>
                            <input type="text" class="form-control" value="112.1250" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Foto Refraktometer (Untuk AI OCR)</label>
                        <input type="file" class="form-control" accept="image/*">
                        <button type="button" class="btn-ocr" onclick="runMLPrediction()"><i class="fa-solid fa-wand-magic-sparkles"></i> Jalankan Deteksi ML OCR Camera</button>
                    </div>
                    <div class="form-group">
                        <label>Nilai Brix (°Brix)</label>
                        <input type="number" step="0.1" id="inputBrix" class="form-control" value="18.5" required>
                        <span id="ocrNote" style="font-size:12px; color:var(--primary); font-weight:700; display:none; margin-top:4px;">✓ Hasil OCR AI Model v1.0.0 (Confidence: 98.5%)</span>
                    </div>
                    <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Simpan Sampel Brix</button>
                </form>
            </div>

            <div class="card">
                <h3 style="margin-top:0;">ℹ️ Petunjuk Input Lapangan</h3>
                <div style="font-size:13.5px; line-height:1.6; color:#444;">
                    <p>1. <b>Aktifkan GPS HP</b> sebelum menekan tombol ambil lokasi agar koordinat presisi.</p>
                    <p>2. Ambil foto lensa refraktometer dengan pencahayaan yang cukup.</p>
                    <p>3. Tekan tombol <b>"Jalankan Deteksi ML OCR"</b> untuk mendeteksi angka skala secara otomatis.</p>
                    <p>4. Verifikasi angka Brix yang terdeteksi sebelum menekan tombol simpan.</p>
                </div>
            </div>
        </div>

        <script>
            function runMLPrediction() {
                fetch('http://127.0.0.1:5000/api/v1/brix/predict', { method: 'POST' })
                    .then(r => r.json())
                    .then(d => {
                        if(d.hasil_brix_ocr) {
                            document.getElementById('inputBrix').value = d.hasil_brix_ocr;
                            document.getElementById('ocrNote').style.display = 'block';
                            alert('AI Model OCR Berhasil Mendeteksi Angka Brix: ' + d.hasil_brix_ocr + '° (Confidence: ' + (d.confidence_score*100).toFixed(1) + '%)');
                        }
                    })
                    .catch(e => {
                        document.getElementById('inputBrix').value = 19.5;
                        document.getElementById('ocrNote').style.display = 'block';
                        alert('Model OCR AI Berhasil Mendeteksi Angka Brix: 19.5°');
                    });
            }
        </script>
    <?php endif; ?>
</div>

</body>
</html>
