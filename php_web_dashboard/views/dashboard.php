<?php
// php_web_dashboard/views/dashboard.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-BRIX Web Dashboard - Monitoring Brix Spasial</title>
    <!-- Plus Jakarta Sans Font -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --primary: #00b050;
            --primary-dark: #1C3829;
            --primary-hover: #009040;
            --bg-main: #F0F4F1;
            --card-bg: #FFFFFF;
            --text-dark: #1a2e20;
            --text-muted: #7a9a84;
            --border-color: rgba(0,0,0,0.07);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-main);
            margin: 0;
            padding: 0;
            color: var(--text-dark);
        }
        .sidebar {
            width: 260px;
            background-color: var(--primary-dark);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 1.5rem 1rem;
            box-sizing: border-box;
            color: white;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar-top {
            width: 100%;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2rem;
            padding: 0 8px;
        }
        .sidebar-logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #00b050, #00d662);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(0, 176, 80, 0.4);
        }
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-nav li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.25s ease;
        }
        .sidebar-nav li a.active, .sidebar-nav li a:hover {
            background: rgba(255,255,255,0.12);
            color: #ffffff;
            transform: translateX(4px);
        }

        /* Sidebar Profile Card */
        .sidebar-user-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            padding: 12px 14px;
            margin-top: auto;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--primary);
            color: white;
            font-weight: 800;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .user-details {
            overflow: hidden;
        }
        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-role-badge {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,0.75);
            background: rgba(0, 176, 80, 0.25);
            padding: 2px 6px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 2px;
        }
        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            background: rgba(231, 76, 60, 0.15);
            color: #ff7675;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid rgba(231, 76, 60, 0.3);
            transition: all 0.2s ease;
        }
        .btn-logout:hover {
            background: #e74c3c;
            color: white;
        }

        .main-content {
            margin-left: 260px;
            padding: 1.75rem 2rem;
        }
        .header-card {
            background: white;
            border-radius: 16px;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
        }
        .api-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-right: 12px;
        }
        .api-status-pill.online {
            background: #e6f7ed;
            color: #1a7a40;
            border: 1px solid #b7ebc6;
        }
        .api-status-pill.offline {
            background: #fde8e8;
            color: #9b1c1c;
            border: 1px solid #f8b4b4;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        .api-status-pill.online .status-dot {
            background: #00b050;
            box-shadow: 0 0 0 3px rgba(0, 176, 80, 0.25);
            animation: pulse 2s infinite;
        }
        .api-status-pill.offline .status-dot {
            background: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.25);
        }
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 176, 80, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(0, 176, 80, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 176, 80, 0); }
        }

        /* Alert Styling */
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .alert-success {
            background: #e6f7ed;
            color: #1a7a40;
            border: 1px solid #a3e0b8;
        }
        .alert-danger {
            background: #fde8e8;
            color: #9b1c1c;
            border: 1px solid #f8b4b4;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .metric-card {
            background: white;
            border-radius: 14px;
            padding: 1.25rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
        }
        .metric-title {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .metric-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-top: 6px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 1.4rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }
        #map {
            height: 480px;
            border-radius: 12px;
            width: 100%;
            z-index: 1;
        }
        .btn-kriging {
            background: linear-gradient(135deg, #00b050, #009040);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0, 176, 80, 0.3);
        }
        .btn-kriging:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 176, 80, 0.4);
            background: linear-gradient(135deg, #00c45a, #009040);
        }
        .sample-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .sample-table th {
            padding: 10px 12px;
            text-align: left;
            background: #f8faf8;
            font-weight: 700;
            color: #555;
            border-bottom: 2px solid #edf2ee;
        }
        .sample-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f0f4f1;
        }
        .brix-pill {
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 12px;
        }
        .brix-high { background: #fde8e8; color: #e74c3c; }
        .brix-mid { background: #fef5e7; color: #f39c12; }
        .brix-low { background: #e6f7ed; color: #2ecc71; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-top">
        <div class="sidebar-brand">
            <div class="sidebar-logo">EB</div>
            <div>
                <div style="font-weight:800; font-size:16px; letter-spacing:0.5px;">E-BRIX</div>
                <div style="font-size:10px; opacity:0.65; font-weight:600;">Monitoring Spasial Tebu</div>
            </div>
        </div>
        <ul class="sidebar-nav">
            <li><a href="index.php?page=dashboard" class="<?= ($page==='dashboard')?'active':'' ?>"><i class="fa-solid fa-map-location-dot"></i> Peta & Analisis</a></li>
            <li><a href="index.php?page=jadwal_panen" class="<?= ($page==='jadwal_panen')?'active':'' ?>"><i class="fa-solid fa-calendar-check"></i> Optimization Panen</a></li>
        </ul>
    </div>

    <!-- Active User Profile Section -->
    <?php if (isset($currentUser)): ?>
    <div class="sidebar-user-card">
        <div class="user-info">
            <div class="user-avatar"><?= htmlspecialchars($currentUser['avatar']) ?></div>
            <div class="user-details">
                <div class="user-name"><?= htmlspecialchars($currentUser['name']) ?></div>
                <div class="user-role-badge"><?= htmlspecialchars($currentUser['role_label']) ?></div>
            </div>
        </div>
        <a href="index.php?action=logout" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar
        </a>
    </div>
    <?php endif; ?>
</div>

<div class="main-content">
    <div class="header-card">
        <div>
            <h2 style="margin:0; font-size:1.25rem; font-weight:800;">Dashboard Monitoring Brix Spasial</h2>
            <p style="margin:4px 0 0; font-size:12px; color:var(--text-muted);">Integrasi Web PHP Dashboard & Flask Microservice Engine</p>
        </div>
        <div style="display:flex; align-items:center;">
            <div class="api-status-pill <?= $isApiOnline ? 'online' : 'offline' ?>">
                <span class="status-dot"></span>
                <span>Flask API: <?= $isApiOnline ? 'ONLINE' : 'OFFLINE (Port 5000)' ?></span>
            </div>
            <form method="POST" action="index.php?action=trigger_kriging" style="margin:0;">
                <button type="submit" class="btn-kriging"><i class="fa-solid fa-bolt"></i> Trigger Kriging GEE</button>
            </form>
        </div>
    </div>

    <?php if (isset($krigingNotice)): ?>
        <?php if (!empty($krigingNotice['success'])): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check" style="font-size:1.2rem;"></i>
                <div>
                    <strong>Berhasil!</strong> <?= htmlspecialchars($krigingNotice['message']) ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation" style="font-size:1.2rem;"></i>
                <div>
                    <strong>Peringatan!</strong> <?= htmlspecialchars($krigingNotice['message']) ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-title">Rata-rata Brix</div>
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
            <div class="metric-title">Sampel Terdata</div>
            <div class="metric-value"><?= $metrics['count'] ?> Titik</div>
        </div>
    </div>

    <div class="content-grid">
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                <h3 style="margin:0; font-size:1rem; font-weight:800;">🗺️ Peta Spasial Distribusi Brix (Leaflet GIS)</h3>
                <span style="font-size:11px; font-weight:700; color:var(--text-muted); background:#f0f4f1; padding:4px 10px; border-radius:12px;">Google Satellite Tile</span>
            </div>
            <div id="map"></div>
        </div>
        <div class="card">
            <h3 style="margin-top:0; margin-bottom:1rem; font-size:1rem; font-weight:800;">📊 Daftar Sampel Terbaru</h3>
            <div style="max-height: 440px; overflow-y: auto;">
                <table class="sample-table">
                    <thead>
                        <tr>
                            <th>Blok</th>
                            <th>Brix</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($samples as $s): ?>
                        <tr>
                            <td style="font-weight:700;"><?= htmlspecialchars($s['Kode_Blok']) ?></td>
                            <td>
                                <?php 
                                    $val = floatval($s['Nilai_Brix']);
                                    $class = ($val >= 19) ? 'brix-high' : (($val >= 14) ? 'brix-mid' : 'brix-low');
                                ?>
                                <span class="brix-pill <?= $class ?>"><?= htmlspecialchars($s['Nilai_Brix']) ?>°</span>
                            </td>
                            <td style="color:#777; font-size:12px;"><?= htmlspecialchars(substr($s['Tanggal'], 0, 10)) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([-7.7530, 112.1260], 14);

    L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        attribution: 'Google Satellite | E-BRIX Platform'
    }).addTo(map);

    const samples = <?= json_encode($samples) ?>;
    samples.forEach(s => {
        if(s.Latitude && s.Longitude) {
            const val = parseFloat(s.Nilai_Brix);
            const color = val >= 19 ? '#e74c3c' : (val >= 14 ? '#f39c12' : '#2ecc71');
            
            const marker = L.circleMarker([s.Latitude, s.Longitude], {
                radius: 7,
                fillColor: color,
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(map);

            marker.bindPopup(`
                <div style="font-family:'Plus Jakarta Sans',sans-serif; padding:4px;">
                    <div style="font-weight:800; font-size:14px; margin-bottom:4px; color:#1a2e20;">${s.Kode_Blok}</div>
                    <div style="font-size:12px; color:#555;">Kadar Brix: <b style="color:${color}; font-size:13px;">${s.Nilai_Brix}°</b></div>
                    <div style="font-size:11px; color:#888; margin-top:2px;">Tanggal: ${s.Tanggal ? s.Tanggal.substring(0,10) : '-'}</div>
                </div>
            `);
        }
    });
</script>
</body>
</html>
