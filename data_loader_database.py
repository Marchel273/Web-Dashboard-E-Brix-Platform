import streamlit as st
import pandas as pd
import requests

@st.cache_data(ttl=600) 
def load_data():
    try:
        # Link API 
        url_endpoint = "https://xstn26ck-3000.asse.devtunnels.ms/data-brix"  
        
        respon = requests.get(url_endpoint, timeout=10)
        respon.raise_for_status() 
        
        # Tarik data JSON dari respon server
        data_json = respon.json()
        
        # AMBIL BAGIAN 'data'nya saja
        # membungkus datanya di dalam {"data": [...], "message": "...", "total": 5}
        list_data = data_json.get('data', [])
        df = pd.DataFrame(list_data)
        
        # 4. GANTI NAMA KOLOM (Translasi dari bahasa Database ke bahasa Dashboard)
        # Format: {'nama_di_database': 'Nama_di_Dashboard'}
        df = df.rename(columns={
            'latitude': 'Latitude',
            'longitude': 'Longitude',
            'id_blok': 'Kode_Blok',
            'nilai_brix': 'Nilai_Brix',
            'timestamp': 'Tanggal'
        })
        
        # 5. Format ulang teks waktu (GMT) menjadi format Datetime yang dikenali Streamlit
        if 'Tanggal' in df.columns:
            df['Tanggal'] = pd.to_datetime(df['Tanggal'])
            
        return df

    except Exception as e:
        st.error(f"⚠️ Gagal mengambil data dari Endpoint: {e}")
        # Kembalikan DataFrame kosong yang SUDAH PUNYA KOLOM, agar UI tidak crash (KeyError)
        return pd.DataFrame(columns=['Latitude', 'Longitude', 'Kode_Blok', 'Nilai_Brix', 'Tanggal'])