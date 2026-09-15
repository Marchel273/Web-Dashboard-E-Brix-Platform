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

$authCtrl = new AuthController();
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Handle Logout
if ($action === 'logout') {
    $authCtrl->logout();
    header('Location: index.php?page=login');
    exit;
}

// Handle Login POST
$loginError = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    $res = $authCtrl->login($username, $password);
    if ($res['success']) {
        header('Location: index.php?page=dashboard');
        exit;
    } else {
        $loginError = $res['message'];
        $page = 'login';
    }
}

// Auto-login default Admin for smooth initial loading
if (!$authCtrl->isLoggedIn() && $page !== 'login') {
    $_SESSION['user'] = [
        'id' => 'u-admin-001',
        'username' => 'admin',
        'name' => 'Budi Santoso, S.T.',
        'role' => 'ADMIN',
        'role_label' => '👑 Administrator',
        'email' => 'admin@pg-gempolkrep.co.id',
        'avatar' => 'BS'
    ];
}

// Check Authentication for Login Page
if ($page === 'login') {
    if ($authCtrl->isLoggedIn()) {
        header('Location: index.php?page=dashboard');
        exit;
    }
    require_once __DIR__ . '/views/login.php';
    exit;
}

$currentUser = $authCtrl->getCurrentUser();
$role = $currentUser['role'] ?? 'ADMIN';
$krigingNotice = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'trigger_kriging') {
    $krigingCtrl = new KrigingController();
    $res = $krigingCtrl->triggerKrigingJob($currentUser['id'] ?? 'u-admin-001');
    $krigingNotice = $res['message'];
}

// Controllers Instances
$brixCtrl = new BrixController();
$harvestCtrl = new HarvestController();
$adminCtrl = new AdminController();

// Data fetching based on role & sub-page
$samples = $brixCtrl->getBrixSamples();
$metrics = $brixCtrl->getMetrics($samples);
$recommendations = $harvestCtrl->getRecommendations();
$subPage = $page;

// Route Views based on Role
if ($role === 'ADMIN') {
    $pendingUsers = $adminCtrl->getPendingUsers();
    $activeUsers = $adminCtrl->getActiveUsers();
    $auditLogs = $adminCtrl->getAuditLogs();
    $pabrikList = $adminCtrl->getMasterDataPabrik();
    $varietasList = $adminCtrl->getMasterVarietas();
    require_once __DIR__ . '/views/dashboard_admin.php';
} elseif ($role === 'MANAGER_AGRONOMI') {
    require_once __DIR__ . '/views/dashboard_manager.php';
} else {
    // PETUGAS_LAPANGAN
    require_once __DIR__ . '/views/dashboard_petugas.php';
}
?>
