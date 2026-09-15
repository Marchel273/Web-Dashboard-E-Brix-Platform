<?php
// php_web_dashboard/controllers/AuthController.php
require_once __DIR__ . '/../config/api.php';

class AuthController {
    private static $users = [
        'admin' => [
            'id' => 'u-admin-001',
            'username' => 'admin',
            'password' => 'password',
            'name' => 'Budi Santoso, S.T.',
            'role' => 'ADMIN',
            'role_label' => '👑 Administrator',
            'email' => 'admin@pg-gempolkrep.co.id',
            'avatar' => 'BS'
        ],
        'manager' => [
            'id' => 'u-mgr-002',
            'username' => 'manager',
            'password' => 'password',
            'name' => 'Dr. Ir. Hendra Wijaya',
            'role' => 'MANAGER_AGRONOMI',
            'role_label' => '👨‍🌾 Manager Agronomi',
            'email' => 'hendra.agronomi@pg-gempolkrep.co.id',
            'avatar' => 'HW'
        ],
        'petugas' => [
            'id' => 'u-field-003',
            'username' => 'petugas',
            'password' => 'password',
            'name' => 'Ahmad Subagyo',
            'role' => 'PETUGAS_LAPANGAN',
            'role_label' => '📋 Petugas Lapangan',
            'email' => 'ahmad.field@pg-gempolkrep.co.id',
            'avatar' => 'AS'
        ]
    ];

    public function login($username, $password) {
        $username = strtolower(trim($username));
        
        if (isset(self::$users[$username]) && self::$users[$username]['password'] === $password) {
            $user = self::$users[$username];
            unset($user['password']);
            $_SESSION['user'] = $user;
            return [
                'success' => true,
                'message' => 'Login berhasil! Selamat datang, ' . $user['name']
            ];
        }

        return [
            'success' => false,
            'message' => 'Username atau password salah. Coba lagi!'
        ];
    }

    public function logout() {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return $_SESSION['user'];
        }
        return null;
    }
}
?>
