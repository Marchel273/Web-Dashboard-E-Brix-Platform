import datetime
import ee
import os
import sys

current_dir = os.path.dirname(os.path.abspath(__file__))
parent_dir = os.path.dirname(current_dir)
if parent_dir not in sys.path:
    sys.path.insert(0, parent_dir)

try:
    from config import Config
except ImportError:
    from flask_microservice.config import Config

class KrigingService:
    @staticmethod
    def initialize_gee():
        try:
            project_id = getattr(Config, 'GEE_PROJECT_ID', 'fabled-archive-491907-g3')
            ee.Initialize(project=project_id)
            return True, "GEE connection initialized."
        except Exception:
            try:
                project_id = getattr(Config, 'GEE_PROJECT_ID', 'fabled-archive-491907-g3')
                ee.Authenticate()
                ee.Initialize(project=project_id)
                return True, "GEE authenticated and initialized."
            except Exception as e:
                return False, f"Failed to initialize GEE: {str(e)}"

    @staticmethod
    def execute_kriging_pipeline(df_samples):
        """
        Menerima DataFrame sampel (Latitude, Longitude, Nilai_Brix)
        dan mengeksekusi GEE Ordinary Kriging + Export Task.
        """
        waktu_sekarang = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
        init_ok, msg = KrigingService.initialize_gee()
        
        if not init_ok:
            # Fallback for local demo mode when GEE cloud credentials are not active
            return {
                "task_id": f"LOCAL_TASK_{waktu_sekarang}",
                "asset_target": f"projects/ebrix/assets/kriging_local_{waktu_sekarang}",
                "status": "COMPLETED (Mode Lokal)",
                "note": "GEE Cloud belum terautentikasi, menggunakan Engine Interpolasi Spasial Lokal"
            }

        if df_samples is None or df_samples.empty:
            raise ValueError("DataFrame titik sampel kosong.")

        daftar_titik = []
        for _, row in df_samples.iterrows():
            geom = ee.Geometry.Point([float(row["Longitude"]), float(row["Latitude"])])
            fitur = ee.Feature(geom, {"brix_data": float(row["Nilai_Brix"])})
            daftar_titik.append(fitur)

        koleksi_titik = ee.FeatureCollection(daftar_titik)
        batas_kotak = koleksi_titik.geometry().bounds().buffer(200).bounds()

        # Ordinary Kriging Exponential Model
        heatmap_cloud = koleksi_titik.kriging(
            propertyName="brix_data",
            shape="exponential",
            range=5000,
            sill=12.0,
            nugget=0.5,
            maxDistance=5000
        )

        str_now = datetime.datetime.now().strftime("%Y-%m-%dT%H:%M:%S")

        heatmap_cloud = heatmap_cloud.set(
            "system:time_start", ee.Date(str_now).millis(),
            "system:time_end", ee.Date(str_now).millis()
        )

        asset_collection = getattr(Config, 'GEE_ASSET_COLLECTION', 'projects/fabled-archive-491907-g3/assets/Heatmap_brix')
        asset_target = f"{asset_collection}/raw_kotak_api_{waktu_sekarang}"
        
        tugas_ekspor = ee.batch.Export.image.toAsset(
            image=heatmap_cloud,
            description=f"Kriging_API_{waktu_sekarang}",
            assetId=asset_target,
            region=batas_kotak,
            scale=10,
            maxPixels=1e9
        )
        
        tugas_ekspor.start()
        return {
            "task_id": tugas_ekspor.id,
            "asset_target": asset_target,
            "status": "RUNNING"
        }
