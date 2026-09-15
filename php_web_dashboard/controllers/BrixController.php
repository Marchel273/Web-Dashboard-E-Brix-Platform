<?php
// php_web_dashboard/controllers/BrixController.php
require_once __DIR__ . '/../config/api.php';

class BrixController {

    private function defaultSamples(): array {
        return [
            ["id" => "S-001", "Tanggal" => "2026-09-01", "Kode_Blok" => "Blok A1", "Latitude" => -7.7512, "Longitude" => 112.1234, "Nilai_Brix" => 21.5, "Petugas" => "Ahmad Subagyo", "Metode" => "Manual"],
            ["id" => "S-002", "Tanggal" => "2026-09-02", "Kode_Blok" => "Blok A2", "Latitude" => -7.7525, "Longitude" => 112.1250, "Nilai_Brix" => 17.2, "Petugas" => "Ahmad Subagyo", "Metode" => "Manual"],
            ["id" => "S-003", "Tanggal" => "2026-09-03", "Kode_Blok" => "Blok B1", "Latitude" => -7.7540, "Longitude" => 112.1280, "Nilai_Brix" => 12.8, "Petugas" => "Siti Rahmawati", "Metode" => "OCR"],
            ["id" => "S-004", "Tanggal" => "2026-09-04", "Kode_Blok" => "Blok B2", "Latitude" => -7.7560, "Longitude" => 112.1310, "Nilai_Brix" => 19.8, "Petugas" => "Ahmad Subagyo", "Metode" => "Manual"],
        ];
    }

    public function getBrixSamples(): array {
        $apiData = ApiClient::get('/data-brix');
        $apiSamples = ($apiData && isset($apiData['data'])) ? $apiData['data'] : $this->defaultSamples();

        // Merge session-submitted samples
        $sessionSamples = $_SESSION['brix_samples'] ?? [];
        return array_merge($sessionSamples, $apiSamples);
    }

    public function getMetrics(array $samples): array {
        if (empty($samples)) {
            return ["avg" => 0, "max" => 0, "min" => 0, "count" => 0];
        }
        $brixValues = array_column($samples, 'Nilai_Brix');
        return [
            "avg"   => round(array_sum($brixValues) / count($brixValues), 1),
            "max"   => max($brixValues),
            "min"   => min($brixValues),
            "count" => count($samples)
        ];
    }

    /* ─── SUBMIT NEW SAMPEL ─── */
    public function submitSampel(array $post): array {
        $nilai = floatval($post['nilai_brix'] ?? 0);
        if ($nilai <= 0 || $nilai > 30) {
            return ['success' => false, 'message' => 'Nilai Brix harus antara 0.1 - 30.0'];
        }

        $id = 'S-' . date('Ymd') . '-' . rand(100, 999);
        $sample = [
            "id"         => $id,
            "Tanggal"    => date('Y-m-d'),
            "Kode_Blok"  => htmlspecialchars($post['kode_blok']   ?? 'Blok Baru'),
            "Latitude"   => floatval($post['latitude']  ?? -7.7530),
            "Longitude"  => floatval($post['longitude'] ?? 112.1260),
            "Nilai_Brix" => $nilai,
            "Petugas"    => $_SESSION['current_user']['name'] ?? 'Petugas',
            "Metode"     => ($post['metode'] ?? 'manual') === 'ocr' ? 'OCR' : 'Manual',
        ];

        if (!isset($_SESSION['brix_samples'])) {
            $_SESSION['brix_samples'] = [];
        }
        array_unshift($_SESSION['brix_samples'], $sample);

        // Append to audit
        if (isset($_SESSION['ebrix_audit'])) {
            array_unshift($_SESSION['ebrix_audit'], [
                "id_log"     => 'LOG-' . rand(9000, 9999),
                "user"       => $sample['Petugas'] . ' (Petugas)',
                "aksi"       => 'INPUT_DATA_BRIX',
                "detail"     => 'Menginput sampel Brix ' . $nilai . '° di ' . $sample['Kode_Blok'] . ' via ' . $sample['Metode'],
                "ip_address" => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                "created_at" => date('Y-m-d H:i:s')
            ]);
        }

        return ['success' => true, 'message' => 'Sampel Brix ' . $nilai . '° berhasil disimpan di ' . $sample['Kode_Blok']];
    }
}
?>
