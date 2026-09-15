"""
Database Seed Script for E-BRIX Platform
Engine: PostgreSQL + PostGIS (schema_ebrix.sql)
"""
import os
import sys

# Force UTF-8 for Windows Console Output
if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

try:
    from dotenv import load_dotenv
    load_dotenv()
except ImportError:
    pass

try:
    import psycopg2
    HAS_PSYCOPG2 = True
except ImportError:
    HAS_PSYCOPG2 = False

DB_HOST = os.environ.get('DB_HOST', 'localhost')
DB_PORT = os.environ.get('DB_PORT', '5432')
DB_NAME = os.environ.get('DB_NAME', 'ebrix_db')
DB_USER = os.environ.get('DB_USER', 'postgres')
DB_PASSWORD = os.environ.get('DB_PASSWORD', 'postgres')

def seed_database():
    print("[E-BRIX] Starting Database Seeding...")
    if not HAS_PSYCOPG2:
        print("[WARNING] Driver 'psycopg2' belum terinstall di Python Environment ini.")
        print("[INFO] Silakan jalankan: pip install psycopg2-binary")
        return

    try:
        conn = psycopg2.connect(
            host=DB_HOST,
            port=DB_PORT,
            dbname=DB_NAME,
            user=DB_USER,
            password=DB_PASSWORD,
            connect_timeout=5
        )
        cur = conn.cursor()

        # 1. Insert Pabrik Gula
        print("  -> Inserting Pabrik Gula...")
        cur.execute("""
            INSERT INTO pabrik_gula (nama_pabrik, kapasitas_giling_tpd, geom)
            VALUES ('PG Gempolkrep', 6500, ST_SetSRID(ST_MakePoint(112.4350, -7.4250), 4326))
            ON CONFLICT DO NOTHING;
        """)

        # 2. Insert Varietas Tebu
        print("  -> Inserting Varietas Tebu...")
        cur.execute("""
            INSERT INTO varietas_tebu (nama_varietas, kematangan, potensi_brix_max, deskripsi)
            VALUES 
                ('Bululawa PS864', 'AWAL', 24.50, 'Varietas masak awal dengan rendemen tinggi'),
                ('Kidang Kencana', 'TENGAH', 23.00, 'Varietas masak tengah dengan ketahanan penyakit baik')
            ON CONFLICT (nama_varietas) DO NOTHING;
        """)

        # 3. Insert Users
        print("  -> Inserting Users...")
        cur.execute("""
            INSERT INTO users (username, email, password_hash, role, status)
            VALUES 
                ('admin_ebrix', 'admin@ebrix.id', '$2b$12$e7x18v...dummyhash', 'ADMIN', 'AKTIF'),
                ('manager_sugar', 'manager@ebrix.id', '$2b$12$e7x18v...dummyhash', 'MANAGER_AGRONOMI', 'AKTIF'),
                ('surveyor_budi', 'budi@ebrix.id', '$2b$12$e7x18v...dummyhash', 'PETUGAS_LAPANGAN', 'AKTIF')
            ON CONFLICT (username) DO NOTHING;
        """)

        conn.commit()
        cur.close()
        conn.close()
        print("[SUCCESS] Database seeding completed successfully!")
    except Exception as e:
        print(f"[NOTICE] Database connection check: {e}")

if __name__ == '__main__':
    seed_database()
