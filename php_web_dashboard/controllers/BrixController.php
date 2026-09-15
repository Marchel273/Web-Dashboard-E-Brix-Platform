<?php
// php_web_dashboard/controllers/BrixController.php
require_once __DIR__ . '/../config/api.php';

class BrixController {
    public function getBrixSamples() {
        $apiData = ApiClient::get('/data-brix');
        if ($apiData && isset($apiData['data'])) {
            return $apiData['data'];
        }

        // Fallback sample data if Flask API is offline
        return [
            ["Tanggal" => "2026-09-01", "Kode_Blok" => "Blok A1", "Latitude" => -7.7512, "Longitude" => 112.1234, "Nilai_Brix" => 21.5],
            ["Tanggal" => "2026-09-02", "Kode_Blok" => "Blok A2", "Latitude" => -7.7525, "Longitude" => 112.1250, "Nilai_Brix" => 17.2],
            ["Tanggal" => "2026-09-03", "Kode_Blok" => "Blok B1", "Latitude" => -7.7540, "Longitude" => 112.1280, "Nilai_Brix" => 12.8],
            ["Tanggal" => "2026-09-04", "Kode_Blok" => "Blok B2", "Latitude" => -7.7560, "Longitude" => 112.1310, "Nilai_Brix" => 19.8],
        ];
    }

    public function getMetrics($samples) {
        if (empty($samples)) {
            return ["avg" => 0, "max" => 0, "min" => 0, "count" => 0];
        }

        $brixValues = array_column($samples, 'Nilai_Brix');
        return [
            "avg" => round(array_sum($brixValues) / count($brixValues), 1),
            "max" => max($brixValues),
            "min" => min($brixValues),
            "count" => count($samples)
        ];
    }
}
?>
