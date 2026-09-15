<?php
// php_web_dashboard/controllers/AdminController.php
require_once __DIR__ . '/../config/api.php';

class AdminController {
    public function getPendingUsers() {
        return [
            [
                "id" => "u-pending-01",
                "nama" => "Siti Rahmawati, S.P.",
                "email" => "siti.field@pg-gempolkrep.co.id",
                "role" => "PETUGAS_LAPANGAN",
                "role_label" => "📋 Petugas Lapangan",
                "tgl_daftar" => "2026-09-14",
                "status" => "PENDING"
            ],
            [
                "id" => "u-pending-02",
                "nama" => "Ir. Bambang Triyono",
                "email" => "bambang.agronomi@pg-gempolkrep.co.id",
                "role" => "MANAGER_AGRONOMI",
                "role_label" => "👨‍🌾 Manager Agronomi",
                "tgl_daftar" => "2026-09-15",
                "status" => "PENDING"
            ]
        ];
    }

    public function getActiveUsers() {
        return [
            [
                "id" => "u-admin-001",
                "nama" => "Budi Santoso, S.T.",
                "email" => "admin@pg-gempolkrep.co.id",
                "role" => "ADMIN",
                "role_label" => "👑 Administrator",
                "tgl_aktif" => "2026-01-01",
                "status" => "AKTIF"
            ],
            [
                "id" => "u-mgr-002",
                "nama" => "Dr. Ir. Hendra Wijaya",
                "email" => "hendra.agronomi@pg-gempolkrep.co.id",
                "role" => "MANAGER_AGRONOMI",
                "role_label" => "👨‍🌾 Manager Agronomi",
                "tgl_aktif" => "2026-02-10",
                "status" => "AKTIF"
            ],
            [
                "id" => "u-field-003",
                "nama" => "Ahmad Subagyo",
                "email" => "ahmad.field@pg-gempolkrep.co.id",
                "role" => "PETUGAS_LAPANGAN",
                "role_label" => "📋 Petugas Lapangan",
                "tgl_aktif" => "2026-03-15",
                "status" => "AKTIF"
            ]
        ];
    }

    public function getAuditLogs() {
        return [
            [
                "id_log" => "LOG-8801",
                "user" => "Budi Santoso (Admin)",
                "aksi" => "APPROVE_USER",
                "detail" => "Menyetujui pendaftaran akun Ahmad Subagyo (Petugas)",
                "ip_address" => "192.168.1.45",
                "created_at" => "2026-09-15 06:45:12"
            ],
            [
                "id_log" => "LOG-8802",
                "user" => "Dr. Ir. Hendra (Manager)",
                "aksi" => "PUBLISH_JADWAL",
                "detail" => "Menerbitkan Jadwal Panen Prioritas 1 ke PG Gempolkrep (Alokasi: 1,640 Ton)",
                "ip_address" => "192.168.1.12",
                "created_at" => "2026-09-15 07:10:00"
            ],
            [
                "id_log" => "LOG-8803",
                "user" => "Ahmad Subagyo (Petugas)",
                "aksi" => "INPUT_DATA_BRIX",
                "detail" => "Menginput sampel Brix 21.5° di BLOK-A1 via Camera ML OCR",
                "ip_address" => "180.252.10.8",
                "created_at" => "2026-09-15 07:15:30"
            ]
        ];
    }

    public function getMasterDataPabrik() {
        return [
            [
                "id_pabrik" => "PBK-001",
                "nama_pabrik" => "PG Gempolkrep",
                "kapasitas_tpd" => 6500,
                "lokasi" => "Mojokerto, Jawa Timur",
                "status" => "OPERASIONAL"
            ],
            [
                "id_pabrik" => "PBK-002",
                "nama_pabrik" => "PG Candi",
                "kapasitas_tpd" => 4000,
                "lokasi" => "Sidoarjo, Jawa Timur",
                "status" => "OPERASIONAL"
            ]
        ];
    }

    public function getMasterVarietas() {
        return [
            [
                "nama_varietas" => "Bululawa PS864",
                "kematangan" => "AWAL",
                "potensi_max" => "24.5°",
                "deskripsi" => "Masak awal, rendemen tinggi, cocok untuk tanah lempung"
            ],
            [
                "nama_varietas" => "Kidang Kencana",
                "kematangan" => "TENGAH",
                "potensi_max" => "23.0°",
                "deskripsi" => "Masak tengah, ketahanan penyakit baik"
            ]
        ];
    }
}
?>
