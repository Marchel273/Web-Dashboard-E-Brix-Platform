import datetime

def calculate_harvest_recommendations(blok_data_list):
    """
    Kalkulasi Indeks Kematangan Brix dan Urutan Prioritas Panen.
    Criteria:
    - Prioritas 1 (Segera Panen): Brix >= 19.0 atau umur tebu >= 11 bulan
    - Prioritas 2 (Waspada/Mendekati Panen): Brix 14.0 - 18.9
    - Prioritas 3 (Belum Matang): Brix < 14.0
    """
    recommendations = []

    for blok in blok_data_list:
        kode_blok = blok.get('kode_blok', 'Unknown')
        rata_brix = float(blok.get('rata_brix', 0.0))
        luas_hektar = float(blok.get('luas_hektar', 1.0))
        varietas = blok.get('nama_varietas', 'Standard')
        
        # Estimasi tonase: Rata-rata 80 Ton per Hektar
        estimasi_tonase = int(luas_hektar * 80)
        
        # Hitung Indeks Kematangan (Brix / 22.0 * 100)
        indeks_kematangan = min(round((rata_brix / 22.0) * 100, 1), 100.0)

        # Determine Priority
        if rata_brix >= 19.0:
            prioritas = 'PRIORITAS_1_SEGERA'
            status_label = '🔴 Prioritas 1: Panen Segera'
            tgl_rekomendasi = datetime.date.today() + datetime.timedelta(days=3)
        elif 14.0 <= rata_brix < 19.0:
            prioritas = 'PRIORITAS_2_WASPADA'
            status_label = '🟡 Prioritas 2: Waspada / Siap Panen'
            tgl_rekomendasi = datetime.date.today() + datetime.timedelta(days=14)
        else:
            prioritas = 'PRIORITAS_3_BELUM_MATANG'
            status_label = '🟢 Prioritas 3: Belum Matang'
            tgl_rekomendasi = datetime.date.today() + datetime.timedelta(days=30)

        recommendations.append({
            "kode_blok": kode_blok,
            "nama_varietas": varietas,
            "luas_hektar": luas_hektar,
            "rata_brix": rata_brix,
            "indeks_kematangan_persen": indeks_kematangan,
            "estimasi_tonase": estimasi_tonase,
            "prioritas_kode": prioritas,
            "prioritas_label": status_label,
            "rekomendasi_tgl_panen": tgl_rekomendasi.strftime("%Y-%m-%d")
        })

    # Sort by Brix descending (highest Brix harvested first)
    recommendations.sort(key=lambda x: x['rata_brix'], reverse=True)
    return recommendations
