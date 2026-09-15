-- =============================================================================
-- E-BRIX PLATFORM ENTERPRISE DATABASE SCHEMA
-- Engine: PostgreSQL 14+ with PostGIS & UUID extensions
-- =============================================================================

-- 1. EXTENSIONS
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "postgis";

-- 2. ENUM TYPES
CREATE TYPE user_role AS ENUM (
    'ADMIN',
    'MANAGER_AGRONOMI',
    'PETUGAS_LAPANGAN',
    'PETANI'
);

CREATE TYPE user_status AS ENUM (
    'PENDING',
    'AKTIF',
    'NONAKTIF'
);

CREATE TYPE varietas_kematangan AS ENUM (
    'AWAL',
    'TENGAH',
    'AKHIR'
);

CREATE TYPE kategori_tanam AS ENUM (
    'PC',  -- Plant Cane (Tanam Perdana)
    'R1',  -- Ratoon 1 (Keprasan 1)
    'R2',  -- Ratoon 2 (Keprasan 2)
    'R3'   -- Ratoon 3 (Keprasan 3)
);

CREATE TYPE metode_input AS ENUM (
    'MANUAL',
    'OCR_CAMERA',
    'SENSOR_IOT'
);

CREATE TYPE prioritas_panen AS ENUM (
    'PRIORITAS_1_SEGERA',
    'PRIORITAS_2_WASPADA',
    'PRIORITAS_3_BELUM_MATANG'
);

CREATE TYPE status_jadwal AS ENUM (
    'DRAFT',
    'APPROVED',
    'IN_PROGRESS',
    'COMPLETED',
    'CANCELLED'
);

CREATE TYPE job_status AS ENUM (
    'SUBMITTED',
    'RUNNING',
    'COMPLETED',
    'FAILED'
);

-- 3. TRIGGER FUNCTION FOR UPDATED_AT TIMESTAMP
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- 4. TABLES IMPLEMENTATION

-- 4.1 USERS
CREATE TABLE users (
    id_user UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role user_role NOT NULL DEFAULT 'PETUGAS_LAPANGAN',
    status user_status NOT NULL DEFAULT 'PENDING',
    reset_otp VARCHAR(6) DEFAULT NULL,
    otp_expiry TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- 4.2 USER TOKENS (JWT SESSION MANAGEMENT)
CREATE TABLE user_tokens (
    id_token UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    id_user UUID NOT NULL REFERENCES users(id_user) ON DELETE CASCADE,
    refresh_token TEXT NOT NULL,
    device_info VARCHAR(255) DEFAULT NULL,
    is_revoked BOOLEAN DEFAULT FALSE,
    expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 4.3 PETANI (USER PROFILE DETAILS)
CREATE TABLE petani (
    id_petani UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    id_user UUID NOT NULL UNIQUE REFERENCES users(id_user) ON DELETE CASCADE,
    nama VARCHAR(100) NOT NULL,
    nomor_telepon VARCHAR(20) DEFAULT NULL,
    alamat TEXT DEFAULT NULL,
    kelompok_tani VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 4.4 PABRIK GULA
CREATE TABLE pabrik_gula (
    id_pabrik UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nama_pabrik VARCHAR(100) NOT NULL,
    kapasitas_giling_tpd INT NOT NULL, -- Tons Per Day
    geom GEOMETRY(Point, 4326) DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER update_pabrik_gula_updated_at BEFORE UPDATE ON pabrik_gula
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- 4.5 VARIETAS TEBU
CREATE TABLE varietas_tebu (
    id_varietas UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nama_varietas VARCHAR(100) NOT NULL UNIQUE,
    kematangan varietas_kematangan NOT NULL DEFAULT 'TENGAH',
    potensi_brix_max NUMERIC(4, 2) DEFAULT 25.00,
    deskripsi TEXT DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 4.6 LAHAN (HAMPARAN UTAMA)
CREATE TABLE lahan (
    id_lahan UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    id_pabrik UUID REFERENCES pabrik_gula(id_pabrik) ON DELETE SET NULL,
    nama_lahan VARCHAR(100) NOT NULL,
    geom GEOMETRY(MultiPolygon, 4326) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER update_lahan_updated_at BEFORE UPDATE ON lahan
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- 4.7 BLOK LAHAN (PETAK LAHAN)
CREATE TABLE blok_lahan (
    id_blok UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    id_lahan UUID NOT NULL REFERENCES lahan(id_lahan) ON DELETE CASCADE,
    id_varietas UUID REFERENCES varietas_tebu(id_varietas) ON DELETE SET NULL,
    kode_blok VARCHAR(50) NOT NULL UNIQUE,
    luas_hektar NUMERIC(8, 2) DEFAULT 0.00,
    tanggal_tanam DATE DEFAULT NULL,
    kategori_tanam kategori_tanam DEFAULT 'PC',
    rata_brix_terakhir NUMERIC(4, 2) DEFAULT 0.00,
    geom GEOMETRY(Polygon, 4326) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER update_blok_lahan_updated_at BEFORE UPDATE ON blok_lahan
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- 4.8 DATA BRIX (SAMPEL PENGUKURAN BRIX)
CREATE TABLE data_brix (
    id_data UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    id_blok UUID NOT NULL REFERENCES blok_lahan(id_blok) ON DELETE CASCADE,
    id_user UUID NOT NULL REFERENCES users(id_user) ON DELETE RESTRICT,
    latitude NUMERIC(10, 8) NOT NULL,
    longitude NUMERIC(11, 8) NOT NULL,
    nilai_brix NUMERIC(4, 2) NOT NULL,
    foto_url TEXT DEFAULT NULL,
    metode_input metode_input NOT NULL DEFAULT 'MANUAL',
    timestamp_sampel TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    geom GEOMETRY(Point, 4326) GENERATED ALWAYS AS (ST_SetSRID(ST_MakePoint(longitude, latitude), 4326)) STORED,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER update_data_brix_updated_at BEFORE UPDATE ON data_brix
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- 4.9 PREDIKSI ML (COMPUTER VISION / OCR RESULT)
CREATE TABLE prediksi_ml (
    id_prediksi UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    id_data UUID NOT NULL UNIQUE REFERENCES data_brix(id_data) ON DELETE CASCADE,
    hasil_brix_ocr NUMERIC(4, 2) NOT NULL,
    confidence_score NUMERIC(5, 4) NOT NULL, -- Contoh: 0.9850 (98.5%)
    model_version VARCHAR(50) NOT NULL DEFAULT 'v1.0.0',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 4.10 REKOMENDASI PANEN
CREATE TABLE rekomendasi_panen (
    id_rekomendasi UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    id_blok UUID NOT NULL REFERENCES blok_lahan(id_blok) ON DELETE CASCADE,
    indeks_kematangan_brix NUMERIC(5, 2) NOT NULL,
    estimasi_tonase INT NOT NULL DEFAULT 0,
    prioritas prioritas_panen NOT NULL DEFAULT 'PRIORITAS_3_BELUM_MATANG',
    rekomendasi_tgl_panen DATE NOT NULL,
    calculated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 4.11 JADWAL PANEN
CREATE TABLE jadwal_panen (
    id_jadwal UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    id_rekomendasi UUID NOT NULL REFERENCES rekomendasi_panen(id_rekomendasi) ON DELETE CASCADE,
    id_pabrik UUID NOT NULL REFERENCES pabrik_gula(id_pabrik) ON DELETE RESTRICT,
    tanggal_rencana_panen DATE NOT NULL,
    alokasi_kuota_tebu_ton INT NOT NULL,
    status_persetujuan status_jadwal NOT NULL DEFAULT 'DRAFT',
    approved_by UUID REFERENCES users(id_user) ON DELETE SET NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER update_jadwal_panen_updated_at BEFORE UPDATE ON jadwal_panen
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- 4.12 KRIGING JOBS (GEE ASYNC TASKS)
CREATE TABLE kriging_jobs (
    id_job UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    id_user UUID REFERENCES users(id_user) ON DELETE SET NULL,
    gee_task_id VARCHAR(100) DEFAULT NULL,
    gee_asset_path VARCHAR(255) DEFAULT NULL,
    status job_status NOT NULL DEFAULT 'SUBMITTED',
    error_message TEXT DEFAULT NULL,
    started_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    finished_at TIMESTAMP WITH TIME ZONE DEFAULT NULL
);

-- 4.13 AUDIT LOGS
CREATE TABLE audit_logs (
    id_log UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    id_user UUID REFERENCES users(id_user) ON DELETE SET NULL,
    aksi VARCHAR(100) NOT NULL,
    detail_perubahan JSONB DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 5. SPATIAL & PERFORMANCE INDEXES

-- Spatial GIST Indexes (PostGIS)
CREATE INDEX idx_pabrik_gula_geom ON pabrik_gula USING GIST (geom);
CREATE INDEX idx_lahan_geom ON lahan USING GIST (geom);
CREATE INDEX idx_blok_lahan_geom ON blok_lahan USING GIST (geom);
CREATE INDEX idx_data_brix_geom ON data_brix USING GIST (geom);

-- Foreign Key & Performance B-Tree Indexes
CREATE INDEX idx_users_email ON users (email);
CREATE INDEX idx_users_role ON users (role);
CREATE INDEX idx_user_tokens_user ON user_tokens (id_user);
CREATE INDEX idx_blok_lahan_lahan ON blok_lahan (id_lahan);
CREATE INDEX idx_blok_lahan_kode ON blok_lahan (kode_blok);
CREATE INDEX idx_data_brix_blok ON data_brix (id_blok);
CREATE INDEX idx_data_brix_timestamp ON data_brix (timestamp_sampel);
CREATE INDEX idx_rekomendasi_blok ON rekomendasi_panen (id_blok);
CREATE INDEX idx_jadwal_tanggal ON jadwal_panen (tanggal_rencana_panen);
CREATE INDEX idx_jadwal_pabrik ON jadwal_panen (id_pabrik);
CREATE INDEX idx_kriging_jobs_status ON kriging_jobs (status);
CREATE INDEX idx_audit_logs_user ON audit_logs (id_user);
