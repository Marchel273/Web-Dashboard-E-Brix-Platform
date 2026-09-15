<?php
// php_web_dashboard/index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/BrixController.php';
require_once __DIR__ . '/controllers/HarvestController.php';
require_once __DIR__ . '/controllers/KrigingController.php';
require_once __DIR__ . '/controllers/AdminController.php';

$authCtrl   = new AuthController();
$page       = isset($_GET['page'])   ? $_GET['page']   : 'dashboard';
$action     = isset($_GET['action']) ? $_GET['action'] : null;

/* ═══════════════════════════════════════
   HANDLE LOGOUT
═══════════════════════════════════════ */
if ($action === 'logout') {
    $authCtrl->logout();
    header('Location: index.php?page=login');
    exit;
}

/* ═══════════════════════════════════════
   HANDLE LOGIN POST
═══════════════════════════════════════ */
$loginError = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $res = $authCtrl->login($username, $password);
    if ($res['success']) {
        header('Location: index.php?page=dashboard');
        exit;
    } else {
        $loginError = $res['message'];
        $page = 'login';
    }
}

/* ═══════════════════════════════════════
   ENFORCE LOGIN
═══════════════════════════════════════ */
if (!$authCtrl->isLoggedIn() && $page !== 'login') {
    header('Location: index.php?page=login');
    exit;
}
if ($page === 'login') {
    if ($authCtrl->isLoggedIn()) {
        header('Location: index.php?page=dashboard');
        exit;
    }
    require_once __DIR__ . '/views/login.php';
    exit;
}

$currentUser = $authCtrl->getCurrentUser();
$role        = $currentUser['role'] ?? 'ADMIN';

/* ═══════════════════════════════════════
   CONTROLLER INSTANCES
═══════════════════════════════════════ */
$brixCtrl    = new BrixController();
$harvestCtrl = new HarvestController();
$adminCtrl   = new AdminController();
$krigingCtrl = new KrigingController();

/* ═══════════════════════════════════════
   FLASH MESSAGE HELPER
═══════════════════════════════════════ */
$flashMessage = null;
$flashType    = 'success';
if (isset($_GET['flash'])) {
    $flashMap = [
        'approved'    => ['Akun berhasil disetujui dan dipindahkan ke daftar aktif.', 'success'],
        'rejected'    => ['Pendaftaran akun berhasil ditolak.', 'warning'],
        'deleted'     => ['Akun berhasil dinonaktifkan (Soft Delete).', 'warning'],
        'pabrik_add'  => ['Data Pabrik berhasil ditambahkan.', 'success'],
        'varietas_add'=> ['Data Varietas berhasil ditambahkan.', 'success'],
        'published'   => ['Jadwal panen berhasil dipublikasikan.', 'success'],
        'sampel_ok'   => ['Sampel Brix berhasil disimpan.', 'success'],
        'sampel_err'  => ['Nilai Brix tidak valid. Harap isi 0.1 - 30.0.', 'error'],
        'kriging_ok'  => ['Analisis Kriging berhasil dijalankan.', 'success'],
    ];
    if (isset($flashMap[$_GET['flash']])) {
        [$flashMessage, $flashType] = $flashMap[$_GET['flash']];
    }
}

/* ═══════════════════════════════════════
   POST ACTION HANDLERS
═══════════════════════════════════════ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ── ADMIN: Approve User ── */
    if ($action === 'approve_user' && $role === 'ADMIN') {
        $id = $_POST['user_id'] ?? '';
        if ($id) $adminCtrl->approveUser($id);
        header('Location: index.php?page=dashboard&flash=approved');
        exit;
    }

    /* ── ADMIN: Reject User ── */
    if ($action === 'reject_user' && $role === 'ADMIN') {
        $id = $_POST['user_id'] ?? '';
        if ($id) $adminCtrl->rejectUser($id);
        header('Location: index.php?page=dashboard&flash=rejected');
        exit;
    }

    /* ── ADMIN: Soft Delete ── */
    if ($action === 'soft_delete' && $role === 'ADMIN') {
        $id = $_POST['user_id'] ?? '';
        if ($id) $adminCtrl->softDeleteUser($id);
        header('Location: index.php?page=dashboard&flash=deleted');
        exit;
    }

    /* ── ADMIN: Add Pabrik ── */
    if ($action === 'add_pabrik' && $role === 'ADMIN') {
        $adminCtrl->addPabrik($_POST);
        header('Location: index.php?page=master_data&flash=pabrik_add');
        exit;
    }

    /* ── ADMIN: Add Varietas ── */
    if ($action === 'add_varietas' && $role === 'ADMIN') {
        $adminCtrl->addVarietas($_POST);
        header('Location: index.php?page=master_data&flash=varietas_add');
        exit;
    }

    /* ── MANAGER / ADMIN: Publish Jadwal ── */
    if ($action === 'publish_jadwal' && in_array($role, ['ADMIN', 'MANAGER_AGRONOMI'])) {
        $kodeBlok = $_POST['kode_blok'] ?? '';
        if ($kodeBlok) $harvestCtrl->publishJadwal($kodeBlok);
        header('Location: index.php?page=jadwal_panen&flash=published');
        exit;
    }

    /* ── PETUGAS / ADMIN: Submit Sampel Brix ── */
    if ($action === 'submit_sampel') {
        $res = $brixCtrl->submitSampel($_POST);
        $flash = $res['success'] ? 'sampel_ok' : 'sampel_err';
        header('Location: index.php?page=input_sampel&flash=' . $flash);
        exit;
    }

    /* ── MANAGER / ADMIN: Trigger Kriging ── */
    if ($action === 'trigger_kriging' && in_array($role, ['ADMIN', 'MANAGER_AGRONOMI'])) {
        $res = $krigingCtrl->triggerKrigingJob($currentUser['id'] ?? 'u-admin-001');
        header('Location: index.php?page=peta_kriging&flash=kriging_ok');
        exit;
    }
}

/* ── MANAGER / ADMIN: Export CSV (GET) ── */
if ($action === 'export_csv' && in_array($role, ['ADMIN', 'MANAGER_AGRONOMI'])) {
    $recs = $harvestCtrl->getRecommendations();
    $harvestCtrl->exportCSV($recs);
    // exportCSV calls exit internally
}

/* ═══════════════════════════════════════
   DATA FETCHING
═══════════════════════════════════════ */
$samples         = $brixCtrl->getBrixSamples();
$metrics         = $brixCtrl->getMetrics($samples);
$recommendations = $harvestCtrl->getRecommendations();
$subPage         = $page;

/* ═══════════════════════════════════════
   ROUTE VIEWS BASED ON ROLE
═══════════════════════════════════════ */
if ($role === 'ADMIN') {
    $pendingUsers  = $adminCtrl->getPendingUsers();
    $activeUsers   = $adminCtrl->getActiveUsers();
    $auditLogs     = $adminCtrl->getAuditLogs();
    $pabrikList    = $adminCtrl->getMasterDataPabrik();
    $varietasList  = $adminCtrl->getMasterVarietas();

    if (in_array($page, ['peta_kriging', 'jadwal_panen', 'analisis_trend'])) {
        require_once __DIR__ . '/views/dashboard_manager.php';
    } elseif (in_array($page, ['input_sampel', 'peta_tugas', 'riwayat_sampel'])) {
        require_once __DIR__ . '/views/dashboard_petugas.php';
    } else {
        require_once __DIR__ . '/views/dashboard_admin.php';
    }
} elseif ($role === 'MANAGER_AGRONOMI') {
    require_once __DIR__ . '/views/dashboard_manager.php';
} else {
    // PETUGAS_LAPANGAN
    require_once __DIR__ . '/views/dashboard_petugas.php';
}
?>
