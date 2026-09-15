<?php
// php_web_dashboard/index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/BrixController.php';
require_once __DIR__ . '/controllers/HarvestController.php';
require_once __DIR__ . '/controllers/KrigingController.php';

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
$krigingNotice = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'trigger_kriging') {
    $krigingCtrl = new KrigingController();
    $krigingNotice = $krigingCtrl->triggerKrigingJob($currentUser['id'] ?? 'u-admin-001');
}

// Check Flask API health status
$apiHealth = ApiClient::get('/health');
$isApiOnline = ($apiHealth && isset($apiHealth['status']) && $apiHealth['status'] === 'ONLINE');

$brixCtrl = new BrixController();
$harvestCtrl = new HarvestController();

if ($page === 'jadwal_panen') {
    $recommendations = $harvestCtrl->getRecommendations();
    require_once __DIR__ . '/views/jadwal_panen.php';
} else {
    $samples = $brixCtrl->getBrixSamples();
    $metrics = $brixCtrl->getMetrics($samples);
    require_once __DIR__ . '/views/dashboard.php';
}
?>
