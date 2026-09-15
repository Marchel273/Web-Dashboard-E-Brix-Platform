import random

class MLService:
    @staticmethod
    def predict_refractometer_ocr(image_bytes=None):
        """
        Machine Learning / Computer Vision OCR Stub untuk deteksi angka Brix dari foto refraktometer.
        Kembalikan estimasi angka brix dan confidence score.
        """
        # Simulated OCR Inference
        simulated_brix = round(random.uniform(12.5, 24.5), 1)
        simulated_confidence = round(random.uniform(0.9200, 0.9950), 4)

        return {
            "hasil_brix_ocr": simulated_brix,
            "confidence_score": simulated_confidence,
            "model_version": "v1.0.0-resnet50-ocr",
            "status": "SUCCESS"
        }
