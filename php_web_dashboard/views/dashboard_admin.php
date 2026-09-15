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
            <h2 style="margin:0; font-size:1.2rem; font-weight:800;">Dashboard Administrator & SDM</h2>
            <p style="margin:4px 0 0; font-size:12px; color:#7a9a84;">Tata Kelola Akun Pengguna, Master Data Pabrik, & System Audit Logs (Super-User Full Access)</p>
        </div>
        <span style="background:#e6f7ed; color:#1a7a40; font-size:12px; font-weight:700; padding:6px 14px; border-radius:20px;">
            <i class="fa-solid fa-shield-halved"></i> Hak Akses Super Admin
        </span>
    </div>

    <?php if ($subPage === 'master_data'): ?>
        <!-- MASTER DATA PABRIK & VARIETAS -->
        <div class="card">
            <h3 style="margin-top:0;">Master Data Pabrik Gula & Varietas Tebu</h3>
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

            <h3>Varietas Tebu Terdaftar</h3>
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
            <h3 style="margin-top:0;">System Audit Logs (Jejak Aktivitas Sistem)</h3>
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
            <h3 style="margin-top:0;">Pendaftaran Akun Baru Membutuhkan Persetujuan (Pending Approval)</h3>
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

            <h3>Daftar Akun Petugas & Manager Aktif</h3>
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

<script>
(function(){
    const btn     = document.getElementById('hamburgerBtn');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); }
    btn.addEventListener('click', function(){ sidebar.classList.contains('open') ? closeSidebar() : openSidebar(); });
    overlay.addEventListener('click', closeSidebar);
    // Close sidebar when a nav link is clicked on mobile
    document.querySelectorAll('.sidebar-nav li a').forEach(function(a){
        a.addEventListener('click', function(){ if(window.innerWidth <= 768) closeSidebar(); });
    });
})();
</script>

</body>
</html>
