import sys
import os
import pandas as pd
from flask import Flask, request, jsonify
from flask_cors import CORS

# Force UTF-8 for Windows Console Output
if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

# Add current directory and parent directory to sys.path for flexible module resolution
current_dir = os.path.dirname(os.path.abspath(__file__))
parent_dir = os.path.dirname(current_dir)
if current_dir not in sys.path:
    sys.path.insert(0, current_dir)
if parent_dir not in sys.path:
    sys.path.insert(0, parent_dir)

try:
    from config import Config
    from services.harvest_service import calculate_harvest_recommendations
    from services.kriging_service import KrigingService
    from services.ml_service import MLService
except ImportError:
    from flask_microservice.config import Config
    from flask_microservice.services.harvest_service import calculate_harvest_recommendations
    from flask_microservice.services.kriging_service import KrigingService
    from flask_microservice.services.ml_service import MLService

app = Flask(__name__)
app.config.from_object(Config)
CORS(app)

# Fallback in-memory / CSV loader
BASE_DIR = parent_dir
CSV_PATH = os.path.join(BASE_DIR, "Data_eBrix_Tren_Naik.csv")

def get_sample_data():
    if os.path.exists(CSV_PATH):
        df = pd.read_csv(CSV_PATH)
        df['Tanggal'] = pd.to_datetime(df['Tanggal'])
        return df
    # Fallback dummy data
    return pd.DataFrame([
        {"Tanggal": "2026-09-01", "Kode_Blok": "Blok A1", "Latitude": -7.7512, "Longitude": 112.1234, "Nilai_Brix": 21.5},
        {"Tanggal": "2026-09-02", "Kode_Blok": "Blok A2", "Latitude": -7.7525, "Longitude": 112.1250, "Nilai_Brix": 17.2},
        {"Tanggal": "2026-09-03", "Kode_Blok": "Blok B1", "Latitude": -7.7540, "Longitude": 112.1280, "Nilai_Brix": 12.8},
        {"Tanggal": "2026-09-04", "Kode_Blok": "Blok B2", "Latitude": -7.7560, "Longitude": 112.1310, "Nilai_Brix": 19.8},
    ])

@app.route('/api/v1/health', methods=['GET'])
def health_check():
    return jsonify({
        "status": "ONLINE",
        "service": "E-BRIX Flask Microservice Engine",
        "version": "v1.0.0",
        "database": "PostgreSQL + PostGIS Ready"
    }), 200

@app.route('/api/v1/data-brix', methods=['GET'])
def get_data_brix():
    try:
        df = get_sample_data()
        records = df.to_dict(orient='records')
        return jsonify({
            "status": "success",
            "total_records": len(records),
            "data": records
        }), 200
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

@app.route('/api/v1/harvest/recommendations', methods=['GET'])
def get_harvest_recommendations():
    try:
        df = get_sample_data()
        grouped = df.groupby('Kode_Blok')['Nilai_Brix'].mean().reset_index()
        blok_list = []
        for idx, row in grouped.iterrows():
            blok_list.append({
                "kode_blok": row['Kode_Blok'],
                "rata_brix": row['Nilai_Brix'],
                "luas_hektar": round(5.0 + (idx * 2.5), 1),
                "nama_varietas": "Bululawa PS864" if idx % 2 == 0 else "Kidang Kencana"
            })
        
        recs = calculate_harvest_recommendations(blok_list)
        return jsonify({
            "status": "success",
            "total_blok": len(recs),
            "recommendations": recs
        }), 200
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

@app.route('/api/v1/kriging/trigger', methods=['POST'])
def trigger_kriging_job():
    try:
        df = get_sample_data()
        res = KrigingService.execute_kriging_pipeline(df)
        return jsonify({
            "status": "success",
            "message": "GEE Ordinary Kriging job submitted successfully",
            "details": res
        }), 200
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

@app.route('/api/v1/brix/predict', methods=['POST'])
def predict_brix_ocr():
    try:
        res = MLService.predict_refractometer_ocr()
        return jsonify(res), 200
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == '__main__':
    print(f"[E-BRIX] Starting Flask Microservice Engine on Port {Config.PORT}...")
    app.run(host='0.0.0.0', port=Config.PORT, debug=Config.DEBUG)
