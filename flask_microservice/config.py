import os
from dotenv import load_dotenv

load_dotenv()

class Config:
    # Flask Config
    SECRET_KEY = os.environ.get('SECRET_KEY', 'ebrix-secret-key-super-secure-2026')
    DEBUG = os.environ.get('FLASK_DEBUG', 'True').lower() in ['true', '1']
    PORT = int(os.environ.get('PORT', 5000))

    # PostgreSQL Database DSN
    DB_HOST = os.environ.get('DB_HOST', 'localhost')
    DB_PORT = os.environ.get('DB_PORT', '5432')
    DB_NAME = os.environ.get('DB_NAME', 'ebrix_db')
    DB_USER = os.environ.get('DB_USER', 'postgres')
    DB_PASSWORD = os.environ.get('DB_PASSWORD', 'postgres')

    @property
    def DB_DSN(self):
        return f"postgresql://{self.DB_USER}:{self.DB_PASSWORD}@{self.DB_HOST}:{self.DB_PORT}/{self.DB_NAME}"

    # Google Earth Engine Config
    GEE_PROJECT_ID = os.environ.get('GEE_PROJECT_ID', 'fabled-archive-491907-g3')
    GEE_ASSET_COLLECTION = os.environ.get('GEE_ASSET_COLLECTION', 'projects/fabled-archive-491907-g3/assets/Heatmap_brix')
