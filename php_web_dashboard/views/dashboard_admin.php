<?php
// php_web_dashboard/views/dashboard_admin.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-BRIX Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 13.5px; }
        th { background: #f9fbf9; font-weight: 700; color: #555; }
        .btn-act { padding: 6px 14px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; font-size: 12px; }
        .btn-approve { background: #e6f7ed; color: #1a7a40; margin-right: 6px; }
        .btn-delete { background: #fde8e8; color: #e74c3c; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .badge-pending { background: #fef5e7; color: #f39c12; }
        .badge-active { background: #e6f7ed; color: #2ecc71; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/components/sidebar.php'; ?>

<div class="main-content">
    <div class="header-card">
        <div>
            <h2 style="margin:0; font-size:1.2rem; font-weight:800;">👑 Dashboard Administrator & SDM</h2>
            <p style="margin:4px 0 0; font-size:12px; color:#7a9a84;">Tata Kelola Akun Pengguna, Master Data Pabrik, & System Audit Logs</p>
        </div>
        <span style="background:#e6f7ed; color:#1a7a40; font-size:12px; font-weight:700; padding:6px 14px; border-radius:20px;">
            <i class="fa-solid fa-shield-check"></i> Hak Akses Admin Sistem
        </span>
    </div>

    <?php if ($subPage === 'master_data'): ?>
        <!-- MASTER DATA PABRIK & VARIETAS -->
        <div class="card">
            <h3 style="margin-top:0;">🌾 Master Data Pabrik Gula & Varietas Tebu</h3>
            <table style="margin-bottom: 2rem;">
                <thead>
                    <tr>
                        <th>ID Pabrik</th>
                        <th>Nama Pabrik</th>
                        <th>Kapasitas Giling (TPD)</th>
                        <th>Lokasi Wilayah</th>
                        <th>Status Operasional</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pabrikList as $p): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($p['id_pabrik']) ?></b></td>
                        <td style="font-weight:700;"><?= htmlspecialchars($p['nama_pabrik']) ?></td>
                        <td><b><?= number_format($p['kapasitas_tpd']) ?> Ton/Hari</b></td>
                        <td><?= htmlspecialchars($p['lokasi']) ?></td>
                        <td><span class="badge badge-active"><?= htmlspecialchars($p['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>🌱 Varietas Tebu Terdaftar</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Varietas</th>
                        <th>Kategori Kematangan</th>
                        <th>Potensi Maximum Brix</th>
                        <th>Deskripsi Pertanian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($varietasList as $v): ?>
                    <tr>
                        <td style="font-weight:700; color:var(--primary);"><?= htmlspecialchars($v['nama_varietas']) ?></td>
                        <td><b>Masak <?= htmlspecialchars($v['kematangan']) ?></b></td>
                        <td><b><?= htmlspecialchars($v['potensi_max']) ?></b></td>
                        <td style="color:#666;"><?= htmlspecialchars($v['deskripsi']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($subPage === 'audit_logs'): ?>
        <!-- AUDIT LOGS -->
        <div class="card">
            <h3 style="margin-top:0;">📜 System Audit Logs (Jejak Aktivitas Sistem)</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Log</th>
                        <th>User Pelaku</th>
                        <th>Jenis Aksi</th>
                        <th>Detail Perubahan Data</th>
                        <th>IP Address</th>
                        <th>Waktu (Timestamp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($auditLogs as $log): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($log['id_log']) ?></b></td>
                        <td style="font-weight:700;"><?= htmlspecialchars($log['user']) ?></td>
                        <td><span class="badge badge-active"><?= htmlspecialchars($log['aksi']) ?></span></td>
                        <td style="color:#444;"><?= htmlspecialchars($log['detail']) ?></td>
                        <td><code><?= htmlspecialchars($log['ip_address']) ?></code></td>
                        <td style="color:#888; font-size:12px;"><?= htmlspecialchars($log['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <!-- USER APPROVAL & SDM -->
        <div class="card">
            <h3 style="margin-top:0;">⏳ Pendaftaran Akun Baru Membutuhkan Persetujuan (Pending Approval)</h3>
            <table style="margin-bottom: 2rem;">
                <thead>
                    <tr>
                        <th>Nama Petugas / User</th>
                        <th>Email</th>
                        <th>Role Pengajuan</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                        <th>Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pendingUsers as $u): ?>
                    <tr>
                        <td style="font-weight:700;"><?= htmlspecialchars($u['nama']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><b><?= htmlspecialchars($u['role_label']) ?></b></td>
                        <td><?= htmlspecialchars($u['tgl_daftar']) ?></td>
                        <td><span class="badge badge-pending">PENDING APPROVAL</span></td>
                        <td>
                            <button class="btn-act btn-approve" onclick="alert('Akun <?= $u['nama'] ?> berhasil disetujui (AKTIF)!')">✓ Setujui</button>
                            <button class="btn-act btn-delete" onclick="alert('Pendaftaran <?= $u['nama'] ?> ditolak.')">✕ Tolak</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>👥 Daftar Akun Petugas & Manager Aktif</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID User</th>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Role Hak Akses</th>
                        <th>Tanggal Aktif</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($activeUsers as $au): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($au['id']) ?></b></td>
                        <td style="font-weight:700;"><?= htmlspecialchars($au['nama']) ?></td>
                        <td><?= htmlspecialchars($au['email']) ?></td>
                        <td><b><?= htmlspecialchars($au['role_label']) ?></b></td>
                        <td><?= htmlspecialchars($au['tgl_aktif']) ?></td>
                        <td><span class="badge badge-active">AKTIF</span></td>
                        <td>
                            <button class="btn-act btn-delete" onclick="alert('Akun <?= $au['nama'] ?> dinonaktifkan / soft-delete.')">Soft Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
