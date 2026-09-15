<?php
// php_web_dashboard/views/dashboard_manager.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - E-BRIX Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/dashboard.css" />
</head>
<body>

<!-- Hamburger toggle (mobile) -->
<button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
    <i class="fa-solid fa-bars"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<?php require_once __DIR__ . '/components/sidebar.php'; ?>

<div class="main-content" id="mainContent">
    <div class="header-card">
        <div>
            <h2 style="margin:0; font-size:1.2rem; font-weight:800;">Dashboard Manager Agronomi</h2>
            <p style="margin:4px 0 0; font-size:12px; color:#7a9a84;">Analisis Spasial Brix, Pemodelan Kriging GEE, & Optimization Jadwal Panen</p>
        </div>
        <form method="POST" action="index.php?action=trigger_kriging">
            <button type="submit" class="btn-kriging"><i class="fa-solid fa-bolt"></i> Trigger Kriging GEE</button>
        </form>
    </div>

    <?php if (isset($krigingNotice)): ?>
        <div style="background:#e6f7ed; color:#1a7a40; padding:12px; border-radius:8px; margin-bottom:1rem; font-weight:600;">
            <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($krigingNotice) ?>
        </div>
    <?php endif; ?>

    <?php if ($subPage === 'jadwal_panen'): ?>
        <!-- OPTIMIZATION JADWAL PANEN -->
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 style="margin:0;">Matriks Rekomendasi & Optimization Panen Tebu</h3>
                    <p style="margin:4px 0 0; font-size:12px; color:#7a9a84;">Hasil Kalkulasi Algoritma Indeks Kematangan Brix & Kuota Giling Pabrik</p>
                </div>
                <button class="btn-kriging" onclick="alert('Jadwal Panen Berhasil Di-Approve & Diterbitkan ke Pabrik Gula!')">✓ Approve & Publish Jadwal</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Urutan</th>
                        <th>Kode Blok</th>
                        <th>Varietas Tebu</th>
                        <th>Luas (Ha)</th>
                        <th>Rata-rata Brix</th>
                        <th>Indeks Kematangan (%)</th>
                        <th>Est. Tonase</th>
                        <th>Status Prioritas</th>
                        <th>Rekomendasi Tgl Panen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recommendations as $idx => $r): ?>
                    <tr>
                        <td><b>#<?= $idx + 1 ?></b></td>
                        <td style="font-weight:700;"><?= htmlspecialchars($r['kode_blok']) ?></td>
                        <td><?= htmlspecialchars($r['nama_varietas']) ?></td>
                        <td><?= htmlspecialchars($r['luas_hektar']) ?> Ha</td>
                        <td style="font-weight:800; color:var(--primary);"><?= htmlspecialchars($r['rata_brix']) ?>°</td>
                        <td><b><?= htmlspecialchars($r['indeks_kematangan_persen']) ?>%</b></td>
                        <td><b><?= htmlspecialchars($r['estimasi_tonase']) ?> Ton</b></td>
                        <td>
                            <?php if($r['prioritas_kode'] === 'PRIORITAS_1_SEGERA'): ?>
                                <span class="badge badge-p1">Prioritas 1: Segera</span>
                            <?php elseif($r['prioritas_kode'] === 'PRIORITAS_2_WASPADA'): ?>
                                <span class="badge badge-p2">Prioritas 2: Waspada</span>
                            <?php else: ?>
                                <span class="badge badge-p3">Prioritas 3: Belum Matang</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight:600; color:#555;"><?= htmlspecialchars($r['rekomendasi_tgl_panen']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($subPage === 'analisis_trend'): ?>
        <!-- ANALISIS TREND CHARTS -->
        <div class="card">
            <h3 style="margin-top:0;">Analisis Trend Kenaikan Brix & Performa Lahan</h3>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-top:1rem;">
                <div style="background:#f9fbf9; padding:1.2rem; border-radius:12px; border:1px solid #eee;">
                    <h4 style="margin-top:0;">Grafik Trend Kenaikan Brix Temporal</h4>
                    <canvas id="chartTrend" height="200"></canvas>
                </div>
                <div style="background:#f9fbf9; padding:1.2rem; border-radius:12px; border:1px solid #eee;">
                    <h4 style="margin-top:0;">Rata-rata Brix per Blok Lahan</h4>
                    <canvas id="chartBarBlok" height="200"></canvas>
                </div>
            </div>
        </div>
        <script>
            new Chart(document.getElementById('chartTrend'), {
                type: 'line',
                data: {
                    labels: ['01 Sep', '04 Sep', '08 Sep', '11 Sep', '14 Sep'],
                    datasets: [{ label: 'Brix Rata-rata', data: [12.5, 14.2, 16.8, 18.5, 21.5], borderColor: '#00b050', tension: 0.3, fill: false }]
                }
            });
            new Chart(document.getElementById('chartBarBlok'), {
                type: 'bar',
                data: {
                    labels: ['Blok A1', 'Blok A2', 'Blok B1', 'Blok B2'],
                    datasets: [{ label: 'Rata-rata Brix (°)', data: [21.5, 17.2, 12.8, 19.8], backgroundColor: ['#e74c3c', '#f39c12', '#2ecc71', '#e74c3c'] }]
                }
            });
        </script>

    <?php else: ?>
        <!-- PETA SPASIAL KRIGING GEE -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-title">Rata-rata Brix Spasial</div>
                <div class="metric-value"><?= $metrics['avg'] ?>°</div>
            </div>
            <div class="metric-card">
                <div class="metric-title">Brix Tertinggi</div>
                <div class="metric-value"><?= $metrics['max'] ?>°</div>
            </div>
            <div class="metric-card">
                <div class="metric-title">Brix Terendah</div>
                <div class="metric-value"><?= $metrics['min'] ?>°</div>
            </div>
            <div class="metric-card">
                <div class="metric-title">Total Sampel Spasial</div>
                <div class="metric-value"><?= $metrics['count'] ?> Titik</div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1.5rem;">
            <div class="card">
                <h3 style="margin-top:0;">Peta Spasial Kriging & Heatmap GEE (Leaflet GIS)</h3>
                <div id="map"></div>
            </div>
            <div class="card">
                <h3 style="margin-top:0;">Sampel Spasial Terdata</h3>
                <div style="max-height: 440px; overflow-y: auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:13px;">
                        <thead>
                            <tr style="border-bottom:2px solid #eee; text-align:left;">
                                <th style="padding:8px;">Blok</th>
                                <th style="padding:8px;">Brix</th>
                                <th style="padding:8px;">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($samples as $s): ?>
                            <tr style="border-bottom:1px solid #f5f5f5;">
                                <td style="padding:8px; font-weight:600;"><?= htmlspecialchars($s['Kode_Blok']) ?></td>
                                <td style="padding:8px; color:var(--primary); font-weight:700;"><?= htmlspecialchars($s['Nilai_Brix']) ?>°</td>
                                <td style="padding:8px; color:#888;"><?= htmlspecialchars(substr($s['Tanggal'], 0, 10)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            const map = L.map('map').setView([-7.7530, 112.1260], 14);
            L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { attribution: 'Google Satellite' }).addTo(map);
            const samples = <?= json_encode($samples) ?>;
            samples.forEach(s => {
                if(s.Latitude && s.Longitude) {
                    const marker = L.circleMarker([s.Latitude, s.Longitude], {
                        radius: 6,
                        fillColor: s.Nilai_Brix >= 19 ? '#e74c3c' : (s.Nilai_Brix >= 14 ? '#f39c12' : '#2ecc71'),
                        color: '#fff', weight: 2, fillOpacity: 0.9
                    }).addTo(map);
                    marker.bindPopup(`<b>${s.Kode_Blok}</b><br>Kadar Brix: <b>${s.Nilai_Brix}°</b>`);
                }
            });
        </script>
    <?php endif; ?>
</div>

<script>
(function(){
    const btn     = document.getElementById('hamburgerBtn');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); }
    btn.addEventListener('click', function(){ sidebar.classList.contains('open') ? closeSidebar() : openSidebar(); });
    overlay.addEventListener('click', closeSidebar);
    document.querySelectorAll('.sidebar-nav li a').forEach(function(a){
        a.addEventListener('click', function(){ if(window.innerWidth <= 768) closeSidebar(); });
    });
})();
</script>

</body>
</html>
