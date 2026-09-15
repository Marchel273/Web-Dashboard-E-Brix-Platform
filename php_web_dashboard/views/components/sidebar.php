<?php
// php_web_dashboard/views/components/sidebar.php
$role = $currentUser['role'] ?? 'ADMIN';
$page = $_GET['page'] ?? 'dashboard';
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo">EB</div>
        <div>
            <div style="font-weight:800; font-size:16px;">E-BRIX</div>
            <div style="font-size:10px; opacity:0.6;">Monitoring Spasial Tebu</div>
        </div>
    </div>

    <!-- User Profile Badge -->
    <div class="user-profile-card">
        <div class="avatar"><?= htmlspecialchars($currentUser['avatar'] ?? 'EB') ?></div>
        <div style="overflow:hidden;">
            <div class="user-name"><?= htmlspecialchars($currentUser['name'] ?? 'User E-BRIX') ?></div>
            <div class="role-badge"><?= htmlspecialchars($currentUser['role_label'] ?? 'User') ?></div>
        </div>
    </div>

    <ul class="sidebar-nav">
        <?php if ($role === 'ADMIN'): ?>
            <!-- ADMIN MENU -->
            <li>
                <a href="index.php?page=dashboard" class="<?= ($page==='dashboard')?'active':'' ?>">
                    <i class="fa-solid fa-users-gear"></i> Persetujuan & SDM
                </a>
            </li>
            <li>
                <a href="index.php?page=master_data" class="<?= ($page==='master_data')?'active':'' ?>">
                    <i class="fa-solid fa-database"></i> Master Data & GIS
                </a>
            </li>
            <li>
                <a href="index.php?page=audit_logs" class="<?= ($page==='audit_logs')?'active':'' ?>">
                    <i class="fa-solid fa-clipboard-list"></i> System Audit Logs
                </a>
            </li>

        <?php elseif ($role === 'MANAGER_AGRONOMI'): ?>
            <!-- MANAGER MENU -->
            <li>
                <a href="index.php?page=dashboard" class="<?= ($page==='dashboard')?'active':'' ?>">
                    <i class="fa-solid fa-map-location-dot"></i> Peta Spasial Kriging
                </a>
            </li>
            <li>
                <a href="index.php?page=jadwal_panen" class="<?= ($page==='jadwal_panen')?'active':'' ?>">
                    <i class="fa-solid fa-calendar-check"></i> Optimization Panen
                </a>
            </li>
            <li>
                <a href="index.php?page=analisis_trend" class="<?= ($page==='analisis_trend')?'active':'' ?>">
                    <i class="fa-solid fa-chart-line"></i> Analisis Trend Brix
                </a>
            </li>

        <?php else: ?>
            <!-- PETUGAS LAPANGAN MENU -->
            <li>
                <a href="index.php?page=dashboard" class="<?= ($page==='dashboard')?'active':'' ?>">
                    <i class="fa-solid fa-camera-retro"></i> Input Sampel & OCR
                </a>
            </li>
            <li>
                <a href="index.php?page=peta_tugas" class="<?= ($page==='peta_tugas')?'active':'' ?>">
                    <i class="fa-solid fa-map-pin"></i> Peta Lahan Tugas
                </a>
            </li>
            <li>
                <a href="index.php?page=riwayat_sampel" class="<?= ($page==='riwayat_sampel')?'active':'' ?>">
                    <i class="fa-solid fa-list-check"></i> Riwayat Input Brix
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <!-- Quick Switch Role Dropdown (For Demo & Testing) -->
    <div class="role-switcher-box">
        <div style="font-size:10px; font-weight:800; color:rgba(255,255,255,0.4); text-transform:uppercase; margin-bottom:6px;">
            🔀 Switch Role Demo
        </div>
        <select onchange="switchRole(this.value)" style="width:100%; padding:8px; border-radius:8px; background:rgba(255,255,255,0.1); color:white; border:1px solid rgba(255,255,255,0.2); font-weight:700; font-size:12px; cursor:pointer;">
            <option value="admin" <?= ($role==='ADMIN')?'selected':'' ?>>👑 Admin</option>
            <option value="manager" <?= ($role==='MANAGER_AGRONOMI')?'selected':'' ?>>👨‍🌾 Manager Agronomi</option>
            <option value="petugas" <?= ($role==='PETUGAS_LAPANGAN')?'selected':'' ?>>📋 Petugas Lapangan</option>
        </select>
    </div>

    <!-- Logout Button -->
    <div style="margin-top:auto; padding-top:1rem;">
        <a href="index.php?action=logout" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar (Logout)
        </a>
    </div>
</div>

<script>
    function switchRole(role) {
        // Auto submit login form as selected role for testing
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?action=login';
        
        const userInput = document.createElement('input');
        userInput.name = 'username';
        userInput.value = role;
        form.appendChild(userInput);

        const passInput = document.createElement('input');
        passInput.name = 'password';
        passInput.value = 'password';
        form.appendChild(passInput);

        document.body.appendChild(form);
        form.submit();
    }
</script>
