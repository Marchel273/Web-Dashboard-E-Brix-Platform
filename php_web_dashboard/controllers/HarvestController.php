<?php
// php_web_dashboard/controllers/HarvestController.php
require_once __DIR__ . '/../config/api.php';

class HarvestController {

    private function defaultRecs(): array {
        return [
            ["kode_blok" => "Blok A1", "nama_varietas" => "Bululawa PS864", "luas_hektar" => 12.5, "rata_brix" => 21.5, "indeks_kematangan_persen" => 97.7, "estimasi_tonase" => 1000, "prioritas_kode" => "PRIORITAS_1_SEGERA",      "prioritas_label" => "Prioritas 1: Panen Segera",    "rekomendasi_tgl_panen" => date('Y-m-d', strtotime('+3 days'))],
            ["kode_blok" => "Blok B2", "nama_varietas" => "Kidang Kencana", "luas_hektar" => 8.0,  "rata_brix" => 19.8, "indeks_kematangan_persen" => 90.0, "estimasi_tonase" => 640,  "prioritas_kode" => "PRIORITAS_1_SEGERA",      "prioritas_label" => "Prioritas 1: Panen Segera",    "rekomendasi_tgl_panen" => date('Y-m-d', strtotime('+5 days'))],
            ["kode_blok" => "Blok A2", "nama_varietas" => "Bululawa PS864", "luas_hektar" => 10.0, "rata_brix" => 17.2, "indeks_kematangan_persen" => 78.2, "estimasi_tonase" => 800,  "prioritas_kode" => "PRIORITAS_2_WASPADA",     "prioritas_label" => "Prioritas 2: Waspada / Siap Panen", "rekomendasi_tgl_panen" => date('Y-m-d', strtotime('+14 days'))],
            ["kode_blok" => "Blok B1", "nama_varietas" => "Kidang Kencana", "luas_hektar" => 15.0, "rata_brix" => 12.8, "indeks_kematangan_persen" => 58.1, "estimasi_tonase" => 1200, "prioritas_kode" => "PRIORITAS_3_BELUM_MATANG", "prioritas_label" => "Prioritas 3: Belum Matang",   "rekomendasi_tgl_panen" => date('Y-m-d', strtotime('+30 days'))],
        ];
    }

    public function getRecommendations(): array {
        $apiData = ApiClient::get('/harvest/recommendations');
        $recs = ($apiData && isset($apiData['recommendations'])) ? $apiData['recommendations'] : $this->defaultRecs();

        // Inject published status from session
        $published = $_SESSION['jadwal_published'] ?? [];
        foreach ($recs as &$r) {
            $r['published'] = in_array($r['kode_blok'], $published);
        }
        unset($r);
        return $recs;
    }

    /* ─── PUBLISH JADWAL PANEN ─── */
    public function publishJadwal(string $kodeBlok): void {
        if (!isset($_SESSION['jadwal_published'])) {
            $_SESSION['jadwal_published'] = [];
        }
        if (!in_array($kodeBlok, $_SESSION['jadwal_published'])) {
            $_SESSION['jadwal_published'][] = $kodeBlok;
        }

        // Audit log
        if (isset($_SESSION['ebrix_audit'])) {
            $userName = $_SESSION['current_user']['name'] ?? 'Manager';
            array_unshift($_SESSION['ebrix_audit'], [
                "id_log"     => 'LOG-' . rand(9000, 9999),
                "user"       => $userName . ' (Manager)',
                "aksi"       => 'PUBLISH_JADWAL',
                "detail"     => 'Mempublikasikan jadwal panen untuk ' . $kodeBlok,
                "ip_address" => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                "created_at" => date('Y-m-d H:i:s')
            ]);
        }
    }

    /* ─── EXPORT CSV ─── */
    public function exportCSV(array $recs): void {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="rekomendasi_panen_' . date('Ymd') . '.csv"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        fputcsv($out, ['Kode Blok', 'Varietas', 'Luas (Ha)', 'Rata Brix', 'Kematangan (%)', 'Estimasi Tonase', 'Prioritas', 'Tgl Panen Rekomendasi', 'Status']);
        foreach ($recs as $r) {
            fputcsv($out, [
                $r['kode_blok'],
                $r['nama_varietas'],
                $r['luas_hektar'],
                $r['rata_brix'],
                $r['indeks_kematangan_persen'],
                $r['estimasi_tonase'],
                $r['prioritas_label'],
                $r['rekomendasi_tgl_panen'],
                ($r['published'] ?? false) ? 'PUBLISHED' : 'DRAFT'
            ]);
        }
        fclose($out);
        exit;
    }
}
?>
