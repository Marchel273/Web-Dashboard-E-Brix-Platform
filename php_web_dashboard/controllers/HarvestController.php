<?php
// php_web_dashboard/controllers/HarvestController.php
require_once __DIR__ . '/../config/api.php';

class HarvestController {
    public function getRecommendations() {
        $apiData = ApiClient::get('/harvest/recommendations');
        if ($apiData && isset($apiData['recommendations'])) {
            return $apiData['recommendations'];
        }

        // Fallback dummy recommendations if Flask API is offline
        return [
            [
                "kode_blok" => "Blok A1",
                "nama_varietas" => "Bululawa PS864",
                "luas_hektar" => 12.5,
                "rata_brix" => 21.5,
                "indeks_kematangan_persen" => 97.7,
                "estimasi_tonase" => 1000,
                "prioritas_kode" => "PRIORITAS_1_SEGERA",
                "prioritas_label" => "🔴 Prioritas 1: Panen Segera",
                "rekomendasi_tgl_panen" => date('Y-m-d', strtotime('+3 days'))
            ],
            [
                "kode_blok" => "Blok B2",
                "nama_varietas" => "Kidang Kencana",
                "luas_hektar" => 8.0,
                "rata_brix" => 19.8,
                "indeks_kematangan_persen" => 90.0,
                "estimasi_tonase" => 640,
                "prioritas_kode" => "PRIORITAS_1_SEGERA",
                "prioritas_label" => "🔴 Prioritas 1: Panen Segera",
                "rekomendasi_tgl_panen" => date('Y-m-d', strtotime('+5 days'))
            ],
            [
                "kode_blok" => "Blok A2",
                "nama_varietas" => "Bululawa PS864",
                "luas_hektar" => 10.0,
                "rata_brix" => 17.2,
                "indeks_kematangan_persen" => 78.2,
                "estimasi_tonase" => 800,
                "prioritas_kode" => "PRIORITAS_2_WASPADA",
                "prioritas_label" => "🟡 Prioritas 2: Waspada / Siap Panen",
                "rekomendasi_tgl_panen" => date('Y-m-d', strtotime('+14 days'))
            ],
            [
                "kode_blok" => "Blok B1",
                "nama_varietas" => "Kidang Kencana",
                "luas_hektar" => 15.0,
                "rata_brix" => 12.8,
                "indeks_kematangan_persen" => 58.1,
                "estimasi_tonase" => 1200,
                "prioritas_kode" => "PRIORITAS_3_BELUM_MATANG",
                "prioritas_label" => "🟢 Prioritas 3: Belum Matang",
                "rekomendasi_tgl_panen" => date('Y-m-d', strtotime('+30 days'))
            ]
        ];
    }
}
?>
