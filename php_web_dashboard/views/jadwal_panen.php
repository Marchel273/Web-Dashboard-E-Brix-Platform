<?php
// php_web_dashboard/views/jadwal_panen.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-BRIX Optimization & Jadwal Panen</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --primary: #00b050;
            --primary-dark: #1C3829;
            --bg-main: #F0F4F1;
            --border-color: rgba(0,0,0,0.07);
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-main);
            margin: 0;
            padding: 0;
            color: #1a2e20;
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
            color: white;
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
        .card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        th {
            background: #f9fbf9;
            font-weight: 700;
            color: #555;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
        }
        .badge-p1 { background: #fde8e8; color: #e74c3c; }
        .badge-p2 { background: #fef5e7; color: #f39c12; }
        .badge-p3 { background: #e6f7ed; color: #2ecc71; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-top">
        <div class="sidebar-brand">
            <div class="sidebar-logo">EB</div>
            <div>
                <div style="font-weight:800; font-size:16px;">E-BRIX</div>
                <div style="font-size:10px; opacity:0.6;">Monitoring Spasial Tebu</div>
            </div>
        </div>
        <ul class="sidebar-nav">
            <li><a href="index.php?page=dashboard"><i class="fa-solid fa-map-location-dot"></i> Peta & Analisis</a></li>
            <li><a href="index.php?page=jadwal_panen" class="active"><i class="fa-solid fa-calendar-check"></i> Optimization Panen</a></li>
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
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 style="margin:0; font-size:1.25rem; font-weight:800;">Matriks Rekomendasi & Prioritas Panen</h2>
                <p style="margin:4px 0 0; font-size:12px; color:#7a9a84;">Kalkulasi Otomatis Berdasarkan Indeks Kematangan Brix & Umur Tanam Tebu</p>
            </div>
            <span style="background:#e6f7ed; color:#1a7a40; font-size:12px; font-weight:700; padding:6px 14px; border-radius:20px;">
                <i class="fa-solid fa-industry"></i> Pabrik Gula: PG Gempolkrep
            </span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Urutan</th>
                    <th>Kode Blok</th>
                    <th>Varietas Tebu</th>
                    <th>Luas (Ha)</th>
                    <th>Rata-rata Brix</th>
                    <th>Kematangan (%)</th>
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
                            <span class="badge badge-p1">🔴 Prioritas 1: Segera</span>
                        <?php elseif($r['prioritas_kode'] === 'PRIORITAS_2_WASPADA'): ?>
                            <span class="badge badge-p2">🟡 Prioritas 2: Waspada</span>
                        <?php else: ?>
                            <span class="badge badge-p3">🟢 Prioritas 3: Belum Matang</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:600; color:#555;"><?= htmlspecialchars($r['rekomendasi_tgl_panen']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
