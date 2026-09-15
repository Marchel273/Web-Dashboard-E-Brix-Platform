<?php
// php_web_dashboard/views/login.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - E-BRIX Platform</title>
    <!-- Plus Jakarta Sans Font -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --primary: #00b050;
            --primary-dark: #1C3829;
            --primary-hover: #009040;
            --primary-gradient: linear-gradient(135deg, #00b050, #009040);
            --bg-light: #F4F6F2;
            --text-dark: #1a2e20;
            --text-muted: #6b8272;
        }

        * { box-sizing: border-box; }
        
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
            background-color: #0d1a13;
        }

        /* ==========================================
           1. SPLASH SCREEN PRELOADER ANIMATION
           ========================================== */
        #splash-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.6s ease, visibility 0.6s ease;
        }

        #splash-screen.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        .splash-brand {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 1.5rem;
        }

        .splash-logo-text {
            font-size: 4.8rem;
            font-weight: 900;
            letter-spacing: -1.5px;
            color: #1C3829;
        }

        .splash-logo-text .green {
            color: var(--primary);
            text-shadow: 0 0 25px rgba(0, 176, 80, 0.4);
        }

        .splash-icon {
            font-size: 3.2rem;
            color: var(--primary);
            animation: pulseRadar 1.5s infinite ease-in-out;
        }

        @keyframes pulseRadar {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.18); opacity: 1; filter: drop-shadow(0 0 12px rgba(0,176,80,0.6)); }
            100% { transform: scale(0.9); opacity: 0.7; }
        }

        .splash-loading-text {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 7px;
            color: #00b050;
            margin-top: 12px;
            animation: blinkText 1.4s infinite ease-in-out;
        }

        @keyframes blinkText {
            0%, 100% { opacity: 0.3; letter-spacing: 7px; }
            50% { opacity: 1; letter-spacing: 9px; }
        }

        /* ==========================================
           2. BACKGROUND VIDEO & GLASS OVERLAY
           ========================================== */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
        }

        .video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.92) contrast(1.05) saturate(1.1);
            transform: scale(1.05);
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(28, 56, 41, 0.2), rgba(10, 23, 16, 0.45));
            backdrop-filter: blur(2px);
        }

        /* ==========================================
           3. ENLARGED & BALANCED LOGIN CONTAINER
           ========================================== */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 3rem;
        }

        .login-card {
            width: 100%;
            max-width: 1280px;
            min-height: 700px;
            max-height: 860px;
            height: 85vh;
            background: #ffffff;
            border-radius: 32px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255,255,255,0.15);
            animation: zoomInCard 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes zoomInCard {
            from { opacity: 0; transform: scale(0.96) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* --- LEFT SIDE: FORM SECTION --- */
        .form-section {
            background: var(--bg-light);
            padding: 3.5rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 1.5rem;
        }

        .brand-logo-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-gradient);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            box-shadow: 0 8px 20px rgba(0, 176, 80, 0.35);
        }

        .brand-name {
            font-weight: 800;
            font-size: 19px;
            color: var(--primary-dark);
            letter-spacing: 0.5px;
        }

        .brand-sub {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .secure-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1px solid #d8e5dc;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1.2rem;
            width: fit-content;
        }

        .form-title {
            font-size: 2.6rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin: 0 0 10px 0;
            letter-spacing: -0.8px;
        }

        .form-desc {
            font-size: 15px;
            color: var(--text-muted);
            margin: 0 0 1.75rem 0;
            line-height: 1.5;
            font-weight: 500;
            min-height: 45px;
        }

        /* Role selector pills */
        .role-selector-title {
            font-size: 11px;
            font-weight: 800;
            color: var(--primary-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .role-selector {
            display: flex;
            background: #e3ebe5;
            padding: 6px;
            border-radius: 32px;
            margin-bottom: 1.75rem;
            gap: 8px;
        }

        .role-pill {
            flex: 1;
            border: none;
            background: transparent;
            padding: 12px 16px;
            border-radius: 26px;
            font-size: 13px;
            font-weight: 800;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.25s ease;
            text-align: center;
        }

        .role-pill.active, .role-pill:hover {
            background: #ffffff;
            color: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .input-group {
            margin-bottom: 1.4rem;
        }

        .input-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            font-weight: 800;
            color: var(--primary-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box i.input-icon {
            position: absolute;
            left: 20px;
            color: #8fa896;
            font-size: 17px;
        }

        .input-field {
            width: 100%;
            padding: 16px 48px 16px 52px;
            border-radius: 32px;
            border: 1px solid #dce6de;
            background: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-dark);
            transition: all 0.25s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 176, 80, 0.15);
        }

        .toggle-password {
            position: absolute;
            right: 20px;
            background: none;
            border: none;
            color: #8fa896;
            cursor: pointer;
            font-size: 16px;
            padding: 0;
        }

        .btn-submit {
            width: 100%;
            padding: 18px;
            border-radius: 32px;
            border: none;
            background: var(--primary-gradient);
            color: white;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 10px 28px rgba(0, 176, 80, 0.35);
            margin-top: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(0, 176, 80, 0.45);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-error {
            background: #fde8e8;
            color: #9b1c1c;
            border: 1px solid #f8b4b4;
            padding: 14px 18px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* --- RIGHT SIDE: BANNER & QUOTE --- */
        .banner-section {
            background: linear-gradient(145deg, #fbfaf6, #e4f0e8);
            padding: 3.5rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .sparkle-badge {
            width: 54px;
            height: 54px;
            background: #fff4e5;
            color: #f39c12;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(243, 156, 18, 0.2);
        }

        .quote-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-dark);
            line-height: 1.25;
            margin-bottom: 1.5rem;
            letter-spacing: -0.8px;
        }

        .quote-subtitle {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.65;
            font-weight: 500;
            margin-bottom: 2.5rem;
        }

        .platform-tag {
            display: flex;
            align-items: center;
            gap: 16px;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 14px 22px;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.06);
            width: fit-content;
            box-shadow: 0 6px 18px rgba(0,0,0,0.04);
            z-index: 2;
        }

        .platform-avatar {
            width: 44px;
            height: 44px;
            background: var(--primary-dark);
            color: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 15px;
        }

        .platform-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--primary-dark);
        }

        .platform-desc {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* Decorative Farm Landscape SVG Vector */
        .vector-landscape {
            position: absolute;
            bottom: 0;
            right: -20px;
            width: 100%;
            height: 230px;
            pointer-events: none;
            opacity: 0.85;
            z-index: 1;
        }

        @media (max-width: 900px) {
            .login-card {
                grid-template-columns: 1fr;
                max-width: 500px;
                min-height: auto;
                height: auto;
            }
            .banner-section {
                display: none;
            }
            .form-section {
                padding: 2.5rem;
            }
        }
    </style>
</head>
<body>

<!-- 1. SPLASH SCREEN PRELOADER (E-BRIX BRANDING) -->
<div id="splash-screen">
    <div class="splash-brand">
        <div class="splash-logo-text">
            <span class="green">E-</span>BRIX
        </div>
        <i class="fa-solid fa-seedling splash-icon"></i>
    </div>
    <div class="splash-loading-text">M E M U A T . . .</div>
</div>

<!-- 2. MOVING VIDEO BACKGROUND -->
<div class="video-background">
    <video autoplay loop muted playsinline poster="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1600&q=80">
        <source src="https://assets.mixkit.co/videos/preview/mixkit-aerial-view-of-a-green-farm-field-41584-large.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>
</div>

<!-- 3. MAIN LOGIN CARD CONTAINER -->
<div class="login-wrapper">
    <div class="login-card">
        
        <!-- LEFT COLUMN: FORM SECTION -->
        <div class="form-section">
            <div>
                <div class="brand-header">
                    <div class="brand-logo-icon">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <div>
                        <div class="brand-name">E-BRIX PLATFORM</div>
                        <div class="brand-sub">PG GEMPOLKREP</div>
                    </div>
                </div>

                <div class="secure-pill" id="rolePillBadge">
                    <i class="fa-solid fa-shield-halved"></i> <span>PORTAL ADMIN SISTEM (FULL ACCESS)</span>
                </div>

                <h1 class="form-title">Sign In</h1>
                <p class="form-desc" id="roleDescText">Portal Administrator: Mengakses seluruh fitur sistem (Persetujuan SDM, Master Data, Peta Spasial GEE, Optimization Panen, & Input Sampel).</p>

                <?php if (isset($loginError)): ?>
                    <div class="alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span><?= htmlspecialchars($loginError) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Role Quick Selector Pills (Clean text & FontAwesome icons) -->
                <div class="role-selector-title">Pilih Role Hak Akses:</div>
                <div class="role-selector">
                    <button type="button" class="role-pill active" onclick="selectRole('admin', 'password', this)"><i class="fa-solid fa-user-shield"></i> Admin</button>
                    <button type="button" class="role-pill" onclick="selectRole('manager', 'password', this)"><i class="fa-solid fa-user-tie"></i> Manager</button>
                    <button type="button" class="role-pill" onclick="selectRole('petugas', 'password', this)"><i class="fa-solid fa-clipboard-user"></i> Petugas</button>
                </div>

                <form method="POST" action="index.php?action=login">
                    <div class="input-group">
                        <div class="input-label">
                            <span>ID / USERNAME / EMAIL</span>
                        </div>
                        <div class="input-box">
                            <i class="fa-solid fa-envelope input-icon"></i>
                            <input type="text" id="username" name="username" class="input-field" placeholder="Username atau email" value="admin" required autocomplete="username">
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-label">
                            <span>PASSWORD</span>
                            <span style="color:#00b050; cursor:pointer; text-transform:none; font-weight:800;" onclick="selectRole('admin', 'password', document.querySelector('.role-pill'))">Kredensial Role</span>
                        </div>
                        <div class="input-box">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="input-field" placeholder="Masukkan password" value="password" required autocomplete="current-password">
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                                <i class="fa-solid fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        Masuk Sekarang <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
            </div>
            
            <div style="font-size:12px; color:var(--text-muted); font-weight:600; text-align:center; margin-top:1.5rem;">
                &copy; <?= date('Y') ?> E-BRIX Enterprise Platform | PG Gempolkrep
            </div>
        </div>

        <!-- RIGHT COLUMN: BANNER & QUOTE -->
        <div class="banner-section">
            <div>
                <div class="sparkle-badge">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>

                <h2 class="quote-title" id="bannerTitle">
                    Presisi Spasial & Optimization Panen Tebu Presisi.
                </h2>

                <p class="quote-subtitle" id="bannerSubtitle">
                    Platform Intelijen Pemantauan Kadar Brix Spasial, Pemodelan Kriging, dan Manajemen Alokasi Panen Berbasis GEE Cloud & AI Engine.
                </p>
            </div>

            <div class="platform-tag">
                <div class="platform-avatar" id="platformAvatar">AD</div>
                <div>
                    <div class="platform-name" id="platformName">Portal Administrator</div>
                    <div class="platform-desc" id="platformDesc">System Security & Super-User Full Access</div>
                </div>
            </div>

            <!-- Vector SVG Illustration of Field / Sugar Cane Buildings -->
            <svg class="vector-landscape" viewBox="0 0 500 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 160 Q 120 120, 250 150 T 500 130 V 200 H 0 Z" fill="#1C3829" opacity="0.12" />
                <path d="M0 175 Q 180 145, 350 170 T 500 160 V 200 H 0 Z" fill="#00b050" opacity="0.18" />
                <path d="M 320 170 L 320 110 L 350 90 L 380 110 L 380 170 Z" stroke="#1C3829" stroke-width="2" stroke-dasharray="3 3" fill="none" opacity="0.35" />
                <path d="M 390 170 L 390 80 L 430 80 L 430 170 Z" stroke="#1C3829" stroke-width="2" stroke-dasharray="3 3" fill="none" opacity="0.35" />
                <path d="M 440 170 L 440 130 L 470 110 L 470 170 Z" stroke="#1C3829" stroke-width="2" stroke-dasharray="3 3" fill="none" opacity="0.35" />
            </svg>
        </div>

    </div>
</div>

<script>
    // 1. Preloader Splash Timeout Animation
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const splash = document.getElementById('splash-screen');
            if (splash) {
                splash.classList.add('fade-out');
            }
        }, 1600);
    });

    // Role Metadata Details (Clean text without emojis)
    const roleMeta = {
        'admin': {
            badge: 'PORTAL ADMIN SISTEM (FULL ACCESS)',
            desc: 'Portal Administrator: Akses penuh ke seluruh fitur sistem (Persetujuan SDM, Master Data, Peta Spasial GEE, Optimization Panen, & Input Sampel).',
            title: 'Tata Kelola Sistem & Otorisasi SDM.',
            subtitle: 'Mengontrol keamanan akses pengguna, verifikasi pendaftaran petugas lapangan, serta manajemen master data pabrik gula.',
            avatar: 'AD',
            name: 'Portal Administrator',
            sub: 'System Security & Super-User Full Access'
        },
        'manager': {
            badge: 'PORTAL MANAGER AGRONOMI',
            desc: 'Portal Akses Manager: Analisis spasial kematangan Brix, pemodelan Kriging GEE, & keputusan jadwal panen.',
            title: 'Presisi Spasial & Optimization Panen Tebu.',
            subtitle: 'Platform Intelijen Pemantauan Kadar Brix Spasial, Pemodelan Kriging, dan Manajemen Alokasi Kuota Giling Pabrik.',
            avatar: 'MA',
            name: 'Portal Manager Agronomi',
            sub: 'Spatial Intelligence & Harvest Scheduling'
        },
        'petugas': {
            badge: 'PORTAL PETUGAS LAPANGAN',
            desc: 'Portal Field Surveyor: Input data sampel kadar Brix lapangan & pemicu deteksi kamera ML OCR refraktometer.',
            title: 'Field Ingestion & AI Camera Refractometer.',
            subtitle: 'Aplikasi survey lapangan untuk menginput koordinat GPS sampel tebu dan ekstraksi otomatis angka Brix via AI Computer Vision.',
            avatar: 'FL',
            name: 'Portal Petugas Lapangan',
            sub: 'Field Data Ingestion & ML OCR Engine'
        }
    };

    // 2. Role Quick Select Switcher with Dynamic Description
    function selectRole(user, pass, btn) {
        document.getElementById('username').value = user;
        document.getElementById('password').value = pass;
        
        document.querySelectorAll('.role-pill').forEach(p => p.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const meta = roleMeta[user];
        if (meta) {
            document.querySelector('#rolePillBadge span').innerText = meta.badge;
            document.getElementById('roleDescText').innerText = meta.desc;
            document.getElementById('bannerTitle').innerText = meta.title;
            document.getElementById('bannerSubtitle').innerText = meta.subtitle;
            document.getElementById('platformAvatar').innerText = meta.avatar;
            document.getElementById('platformName').innerText = meta.name;
            document.getElementById('platformDesc').innerText = meta.sub;
        }
    }

    // 3. Password Visibility Toggle
    function togglePasswordVisibility() {
        const passInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        if (passInput.type === 'password') {
            passInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>

</body>
</html>
