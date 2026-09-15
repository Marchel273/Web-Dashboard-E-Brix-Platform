<?php
// php_web_dashboard/controllers/AdminController.php
require_once __DIR__ . '/../config/api.php';

class AdminController {

    /* ─── SESSION SEED: load default data once ─── */
    private function seedSession(): void {
        if (!isset($_SESSION['ebrix_pending'])) {
            $_SESSION['ebrix_pending'] = [
                [
                    "id"         => "u-pending-01",
                    "nama"       => "Siti Rahmawati, S.P.",
                    "email"      => "siti.field@pg-gempolkrep.co.id",
                    "role"       => "PETUGAS_LAPANGAN",
                    "role_label" => "Petugas Lapangan",
                    "tgl_daftar" => "2026-09-14",
                    "status"     => "PENDING"
                ],
                [
                    "id"         => "u-pending-02",
                    "nama"       => "Ir. Bambang Triyono",
                    "email"      => "bambang.agronomi@pg-gempolkrep.co.id",
                    "role"       => "MANAGER_AGRONOMI",
                    "role_label" => "Manager Agronomi",
                    "tgl_daftar" => "2026-09-15",
                    "status"     => "PENDING"
                ]
            ];
        }

        if (!isset($_SESSION['ebrix_users'])) {
            $_SESSION['ebrix_users'] = [
                [
                    "id"         => "u-admin-001",
                    "nama"       => "Budi Santoso, S.T.",
                    "email"      => "admin@pg-gempolkrep.co.id",
                    "role"       => "ADMIN",
                    "role_label" => "Administrator",
                    "tgl_aktif"  => "2026-01-01",
                    "status"     => "AKTIF"
                ],
                [
                    "id"         => "u-mgr-002",
                    "nama"       => "Dr. Ir. Hendra Wijaya",
                    "email"      => "hendra.agronomi@pg-gempolkrep.co.id",
                    "role"       => "MANAGER_AGRONOMI",
                    "role_label" => "Manager Agronomi",
                    "tgl_aktif"  => "2026-02-10",
                    "status"     => "AKTIF"
                ],
                [
                    "id"         => "u-field-003",
                    "nama"       => "Ahmad Subagyo",
                    "email"      => "ahmad.field@pg-gempolkrep.co.id",
                    "role"       => "PETUGAS_LAPANGAN",
                    "role_label" => "Petugas Lapangan",
                    "tgl_aktif"  => "2026-03-15",
                    "status"     => "AKTIF"
                ]
            ];
        }

        if (!isset($_SESSION['ebrix_audit'])) {
            $_SESSION['ebrix_audit'] = [
                [
                    "id_log"     => "LOG-8801",
                    "user"       => "Budi Santoso (Admin)",
                    "aksi"       => "APPROVE_USER",
                    "detail"     => "Menyetujui pendaftaran akun Ahmad Subagyo (Petugas)",
                    "ip_address" => "192.168.1.45",
                    "created_at" => "2026-09-15 06:45:12"
                ],
                [
                    "id_log"     => "LOG-8802",
                    "user"       => "Dr. Ir. Hendra (Manager)",
                    "aksi"       => "PUBLISH_JADWAL",
                    "detail"     => "Menerbitkan Jadwal Panen Prioritas 1 ke PG Gempolkrep",
                    "ip_address" => "192.168.1.12",
                    "created_at" => "2026-09-15 07:10:00"
                ],
                [
                    "id_log"     => "LOG-8803",
                    "user"       => "Ahmad Subagyo (Petugas)",
                    "aksi"       => "INPUT_DATA_BRIX",
                    "detail"     => "Menginput sampel Brix 21.5 di BLOK-A1 via Camera ML OCR",
                    "ip_address" => "180.252.10.8",
                    "created_at" => "2026-09-15 07:15:30"
                ]
            ];
        }

        if (!isset($_SESSION['ebrix_pabrik'])) {
            $_SESSION['ebrix_pabrik'] = [
                ["id_pabrik" => "PBK-001", "nama_pabrik" => "PG Gempolkrep", "kapasitas_tpd" => 6500, "lokasi" => "Mojokerto, Jawa Timur", "status" => "OPERASIONAL"],
                ["id_pabrik" => "PBK-002", "nama_pabrik" => "PG Candi",       "kapasitas_tpd" => 4000, "lokasi" => "Sidoarjo, Jawa Timur",  "status" => "OPERASIONAL"]
            ];
        }

        if (!isset($_SESSION['ebrix_varietas'])) {
            $_SESSION['ebrix_varietas'] = [
                ["nama_varietas" => "Bululawa PS864",  "kematangan" => "AWAL",   "potensi_max" => "24.5", "deskripsi" => "Masak awal, rendemen tinggi, cocok tanah lempung"],
                ["nama_varietas" => "Kidang Kencana",  "kematangan" => "TENGAH", "potensi_max" => "23.0", "deskripsi" => "Masak tengah, ketahanan penyakit baik"]
            ];
        }
    }

    /* ─── APPEND AUDIT LOG ─── */
    private function appendAudit(string $aksi, string $detail): void {
        $logId = "LOG-" . rand(9000, 9999);
        $user  = $_SESSION['current_user']['name'] ?? 'Admin';
        array_unshift($_SESSION['ebrix_audit'], [
            "id_log"     => $logId,
            "user"       => $user,
            "aksi"       => $aksi,
            "detail"     => $detail,
            "ip_address" => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            "created_at" => date('Y-m-d H:i:s')
        ]);
    }

    /* ─── READ METHODS ─── */
    public function getPendingUsers(): array {
        $this->seedSession();
        return $_SESSION['ebrix_pending'];
    }

    public function getActiveUsers(): array {
        $this->seedSession();
        return $_SESSION['ebrix_users'];
    }

    public function getAuditLogs(): array {
        $this->seedSession();
        return $_SESSION['ebrix_audit'];
    }

    public function getMasterDataPabrik(): array {
        $this->seedSession();
        return $_SESSION['ebrix_pabrik'];
    }

    public function getMasterVarietas(): array {
        $this->seedSession();
        return $_SESSION['ebrix_varietas'];
    }

    /* ─── APPROVE USER ─── */
    public function approveUser(string $id): void {
        $this->seedSession();
        $found = null;
        $newPending = [];
        foreach ($_SESSION['ebrix_pending'] as $u) {
            if ($u['id'] === $id) { $found = $u; }
            else { $newPending[] = $u; }
        }
        if ($found) {
            $found['status']    = 'AKTIF';
            $found['tgl_aktif'] = date('Y-m-d');
            unset($found['tgl_daftar']);
            $_SESSION['ebrix_users'][] = $found;
            $_SESSION['ebrix_pending'] = $newPending;
            $this->appendAudit('APPROVE_USER', 'Menyetujui pendaftaran akun ' . $found['nama'] . ' (' . $found['role_label'] . ')');
        }
    }

    /* ─── REJECT USER ─── */
    public function rejectUser(string $id): void {
        $this->seedSession();
        $nama = '';
        $newPending = [];
        foreach ($_SESSION['ebrix_pending'] as $u) {
            if ($u['id'] === $id) { $nama = $u['nama']; }
            else { $newPending[] = $u; }
        }
        $_SESSION['ebrix_pending'] = $newPending;
        if ($nama) {
            $this->appendAudit('REJECT_USER', 'Menolak pendaftaran akun ' . $nama);
        }
    }

    /* ─── SOFT DELETE USER ─── */
    public function softDeleteUser(string $id): void {
        $this->seedSession();
        foreach ($_SESSION['ebrix_users'] as &$u) {
            if ($u['id'] === $id) {
                $u['status'] = 'NON-AKTIF';
                $this->appendAudit('SOFT_DELETE', 'Menonaktifkan akun ' . $u['nama'] . ' (' . $u['role_label'] . ')');
                break;
            }
        }
        unset($u);
    }

    /* ─── ADD PABRIK ─── */
    public function addPabrik(array $data): void {
        $this->seedSession();
        $newId = 'PBK-' . str_pad(count($_SESSION['ebrix_pabrik']) + 1, 3, '0', STR_PAD_LEFT);
        $_SESSION['ebrix_pabrik'][] = [
            "id_pabrik"    => $newId,
            "nama_pabrik"  => htmlspecialchars($data['nama_pabrik'] ?? ''),
            "kapasitas_tpd"=> intval($data['kapasitas_tpd'] ?? 0),
            "lokasi"       => htmlspecialchars($data['lokasi'] ?? ''),
            "status"       => "OPERASIONAL"
        ];
        $this->appendAudit('ADD_PABRIK', 'Menambahkan master data pabrik: ' . ($data['nama_pabrik'] ?? ''));
    }

    /* ─── ADD VARIETAS ─── */
    public function addVarietas(array $data): void {
        $this->seedSession();
        $_SESSION['ebrix_varietas'][] = [
            "nama_varietas" => htmlspecialchars($data['nama_varietas'] ?? ''),
            "kematangan"    => htmlspecialchars($data['kematangan'] ?? 'TENGAH'),
            "potensi_max"   => htmlspecialchars($data['potensi_max'] ?? '0'),
            "deskripsi"     => htmlspecialchars($data['deskripsi'] ?? '')
        ];
        $this->appendAudit('ADD_VARIETAS', 'Menambahkan varietas tebu: ' . ($data['nama_varietas'] ?? ''));
    }
}
?>
