import datetime
import ee
import pandas as pd

# Mengimpor fungsi penarik API dari file data_loader_database.py milikmu
from data_loader_database import load_data

# =================================================================
# 1. INISIALISASI KONEKSI GOOGLE EARTH ENGINE
# =================================================================
print("📡 Menginisialisasi koneksi Cloud GEE...")
try:
    ee.Initialize(project="fabled-archive-491907-g3")
except Exception:
    ee.Authenticate()
    ee.Initialize(project="fabled-archive-491907-g3")


def jalankan_pipeline_transfer_api():
    try:
        # =================================================================
        # 2. TARIK DATA DARI DATABASE (API REST)
        # =================================================================
        print("📊 Mengambil data terbaru dari Endpoint API...")

        # Memanggil fungsi data loader dari file data_loader_database.py
        df = load_data()

        if df.empty:
            print(
                "❌ Proses dihentikan: Data dari API kosong atau server sedang down."
            )
            return

        # Hapus baris yang koordinat atau nilai brix-nya kosong (NaN)
        df = df.dropna(subset=["Latitude", "Longitude", "Nilai_Brix"])
        print(f"✅ Berhasil memuat {len(df)} titik sampel tebu dari Database.")

        # =================================================================
        # 3. PROSES TRANSFER DATA KE GOOGLE CLOUD (GEE OBJ)
        # =================================================================
        print("🚀 Mentransfer koordinat spasial ke Google Cloud...")
        daftar_titik = []

        for _, row in df.iterrows():
            # Format GEE wajib: [Longitude/X, Latitude/Y]
            geometri = ee.Geometry.Point(
                [float(row["Longitude"]), float(row["Latitude"])]
            )
            # Simpan kadar brix ke dalam properti fitur GEE
            fitur = ee.Feature(geometri, {"brix_data": float(row["Nilai_Brix"])})
            daftar_titik.append(fitur)

        koleksi_titik = ee.FeatureCollection(daftar_titik)

        # =================================================================
        # 4. PROSES GEOPROCESSING ORDINARY KRIGING BENTUK KOTAK
        # =================================================================
        print(
            "🗺️ Menginstruksikan server GEE untuk menghitung Ordinary Kriging (Kotak)..."
        )

        # Membuat batas kotak mentah (Bounding Box) + jarak toleransi 200m
        batas_kotak_gee = (
            koleksi_titik.geometry().bounds().buffer(200).bounds()
        )

        # Eksekusi algoritma Ordinary Kriging di Cloud Server Google
        heatmap_cloud = koleksi_titik.kriging(
            propertyName="brix_data",  # Kolom acuan nilai brix
            shape="exponential",  # Model semivariogram standar agrikultur
            range=5000,  # Jarak pengaruh spasial maksimal (5000 meter)
            sill=12.0,  # Batas variansi maksimal data Brix
            nugget=0.5,  # Toleransi error dari alat sensor hardware
            maxDistance=5000,  # Jarak maksimal pencarian titik tetangga
        )

        # =================================================================
        # 4.5. AUTO-STEMPEL WAKTU (START & END TIME) BERBASIS EE.DATE
        # =================================================================
        print("⏱️ Menyematkan metadata waktu otomatis dari data sensor...")

        if "Tanggal" in df.columns:
            df["Tanggal"] = pd.to_datetime(df['Tanggal'], errors='coerce')
            waktu_awal = df["Tanggal"].min()
            waktu_akhir = df["Tanggal"].max()

            if pd.notna(waktu_awal) and pd.notna(waktu_akhir):
                # Konversi ke format string ISO standar untuk GEE
                str_awal = waktu_awal.strftime("%Y-%m-%dT%H:%M:%S")
                str_akhir = waktu_akhir.strftime("%Y-%m-%dT%H:%M:%S")
            else:
                str_awal = str_akhir = datetime.datetime.now().strftime(
                    "%Y-%m-%dT%H:%M:%S"
                )
        else:
            str_awal = str_akhir = datetime.datetime.now().strftime(
                "%Y-%m-%dT%H:%M:%S"
            )

        # Suntikkan objek ee.Date resmi agar kolom Start/End Time terisi otomatis
        heatmap_cloud = heatmap_cloud.set(
            "system:time_start",
            ee.Date(str_awal).millis(),
            "system:time_end",
            ee.Date(str_akhir).millis(),
        )

        # =================================================================
        # 5. EKSPOR RASTER KE GOOGLE EARTH ENGINE ASSET
        # =================================================================
        print("💾 Mendaftarkan tugas ekspor ke folder Asset GEE...")

        waktu_sekarang = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
        nama_asset_tujuan = f"projects/fabled-archive-491907-g3/assets/Heatmap_brix/raw_kotak_api_{waktu_sekarang}"

        tugas_ekspor = ee.batch.Export.image.toAsset(
            image=heatmap_cloud,
            description=f"Kriging_API_Database_{waktu_sekarang}",
            assetId=nama_asset_tujuan,
            region=batas_kotak_gee,
            scale=10,
            maxPixels=1e9,
        )

        tugas_ekspor.start()
        print(
            f"✅ SUKSES! Task ID {tugas_ekspor.id} telah dikirim ke dapur GEE."
        )
        print(f"File sedang dimasak dan akan mendarat di: {nama_asset_tujuan}")

    except Exception as e:
        print(f"❌ Pipeline API gagal karena: {e}")


if __name__ == "__main__":
    jalankan_pipeline_transfer_api()