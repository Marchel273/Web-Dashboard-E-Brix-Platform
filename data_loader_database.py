import streamlit as st
import pandas as pd
import requests

@st.cache_data(ttl=200) 
def load_data():
    try:
        # Link API 
        url_endpoint = "https://xstn26ck-3000.asse.devtunnels.ms/data-brix"  
        
        respon = requests.get(url_endpoint, timeout=30)
        respon.raise_for_status() 
        
        # Tarik data JSON dari respon server
        data_json = respon.json()
        
        # =========================================================
        # [PERBAIKAN UTAMA]: BONGKAR KARDUS GEOJSON
        # Mengambil data dari dalam features -> properties
        # =========================================================
        if "features" in data_json:
            list_data = [fitur["properties"] for fitur in data_json["features"]]
        else:
            # Fallback (Jaga-jaga jika Fa mengubah kembali ke format 'data')
            list_data = data_json.get('data', [])
            
        df = pd.DataFrame(list_data)
        
        # =========================================================
        # GANTI NAMA KOLOM (Translasi ke bahasa Dashboard)
        # =========================================================
        df = df.rename(columns={
            'latitude': 'Latitude',
            'longitude': 'Longitude',
            'id_blok': 'Kode_Blok',
            'nilai_brix': 'Nilai_Brix',
            'timestamp': 'Tanggal' 
        })
        
        # Tambahan Estetika: Ubah angka 1 menjadi teks 'Blok 1'
        if 'Kode_Blok' in df.columns:
            df['Kode_Blok'] = "Blok " + df['Kode_Blok'].astype(str)
        
        # Format ulang teks waktu (GMT) menjadi format Datetime
        if 'Tanggal' in df.columns:
            df['Tanggal'] = pd.to_datetime(df['Tanggal'])
            
        return df

    except Exception as e:
        st.error(f"⚠️ Gagal mengambil data dari Endpoint: {e}")
        # Kembalikan DataFrame kosong yang SUDAH PUNYA KOLOM, agar UI tidak crash (KeyError)
        return pd.DataFrame(columns=['Latitude', 'Longitude', 'Kode_Blok', 'Nilai_Brix', 'Tanggal'])