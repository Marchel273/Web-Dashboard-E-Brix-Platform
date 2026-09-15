<?php
// php_web_dashboard/controllers/KrigingController.php
require_once __DIR__ . '/../config/api.php';

class KrigingController {
    public function triggerKrigingJob($userId = null) {
        $response = ApiClient::post('/kriging/trigger', [
            'user_id' => $userId,
            'triggered_at' => date('Y-m-d H:i:s')
        ]);

        if ($response && isset($response['status']) && $response['status'] === 'success') {
            $msg = "Tugas Kriging GEE berhasil dikirim!";
            if (isset($response['details']['note'])) {
                $msg .= " (" . $response['details']['note'] . ")";
            }
            return [
                "success" => true,
                "message" => $msg,
                "data" => isset($response['details']) ? $response['details'] : null
            ];
        }

        if ($response && isset($response['message'])) {
            return [
                "success" => false,
                "message" => "Gagal memicu Kriging: " . $response['message']
            ];
        }

        return [
            "success" => false,
            "message" => "Gagal terhubung ke Flask API. Pastikan Flask API online di port 5000."
        ];
    }
}
?>
