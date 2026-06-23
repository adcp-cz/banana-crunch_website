<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jajan Pisang | Lupa Kata Sandi</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- DESIGN SYSTEM & VARIABLES (Konsisten dengan Login/Register) --- */
        :root {
            --primary: #FFD600;
            --secondary: #8A6F00;
            --bg-light: #FAFAFA;
            --card-bg: #FFFFFF;
            --text-dark: rgb(68, 68, 68);
            --text-secondary: #666666;
            --text-muted: #9CA3AF;
            --radius-md: 14px;
            --radius-pill: 50px;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            transition: var(--transition);
        }

        /* --- SPLIT SCREEN LAYOUT --- */
        .login-container {
            min-height: 100vh;
        }

        /* Sisi Kiri: Visual Branding */
        .login-left {
            position: relative;
            background-image: url('https://images.unsplash.com/photo-1603833665858-e61d17a86224?q=80&w=1200&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
        }

        .login-left::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(31, 31, 31, 0.85) 0%, rgba(138, 111, 0, 0.7) 100%);
            z-index: 1;
        }

        .brand-wrapper {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #FFFFFF;
            max-width: 480px;
        }

        .brand-logo-text {
            font-size: 3.5rem;
            font-weight: 700;
            letter-spacing: -1px;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .brand-slogan {
            font-size: 1.25rem;
            font-weight: 400;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }

        /* Sisi Kanan: Form Lupa Password */
        .login-right {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background-color: var(--bg-light);
        }

        .form-panel-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .welcome-title {
            font-weight: 700;
            font-size: 2rem;
            color: var(--text-dark);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .welcome-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-bottom: 35px;
        }

        /* --- STYLING UTK FORM INPUTS --- */
        .form-label-custom {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
            display: block;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 24px;
        }

        .form-control-custom {
            width: 100%;
            padding: 14px 20px;
            font-size: 0.95rem;
            background-color: #FFFFFF;
            border: 1.5px solid #E5E7EB;
            border-radius: var(--radius-md);
            color: var(--text-dark);
            outline: none;
            transition: var(--transition);
        }

        .form-control-custom:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 4px rgba(138, 111, 0, 0.1);
        }

        /* --- BUTTONS --- */
        .btn-login-submit {
            background-color: var(--primary);
            color: var(--text-dark);
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: var(--radius-pill);
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(255, 214, 0, 0.2);
            margin-bottom: 25px;
        }

        .btn-login-submit:hover {
            background-color: var(--secondary);
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(138, 111, 0, 0.25);
        }

        /* --- FOOTER FORM LINK --- */
        .form-redirect-footer {
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .link-register-redirect {
            color: var(--secondary);
            font-weight: 600;
        }

        .link-register-redirect:hover {
            color: var(--text-dark);
            text-decoration: underline;
        }

        /* --- RESPONSIVE ADJUSTMENTS --- */
        @media (max-width: 991.98px) {
            .login-left {
                display: none !important;
            }
            .login-right {
                padding: 24px;
            }
        }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="row g-0 login-container">
            
            <div class="col-lg-6 d-none d-lg-flex login-left">
                <div class="brand-wrapper">
                    <h1 class="brand-logo-text">Jajan Pisang</h1>
                    <p class="brand-slogan">Kelezatan Pisang Dalam Setiap Gigitan</p>
                </div>
            </div>

            <div class="col-lg-6 col-12 login-right">
                <div class="form-panel-wrapper">
                    
                    <div class="text-start">
                        <h2 class="welcome-title">Lupa Kata Sandi?</h2>
                        <p class="welcome-subtitle">Jangan khawatir! Masukkan alamat email Anda yang terdaftar, kami akan mengirimkan instruksi untuk menyetel ulang kata sandi.</p>
                    </div>

                    <form action="proses_forgot_password.php" method="POST" autocomplete="off">
                        
                        <div class="input-group-custom">
                            <label for="email" class="form-label-custom">Alamat Email Terdaftar</label>
                            <input type="email" id="email" name="email" class="form-control-custom" placeholder="contoh@email.com" required>
                        </div>

                        <button type="submit" class="btn-login-submit">Kirim Link Reset</button>

                    </form>

                    <div class="form-redirect-footer">
                        <a href="login.php" class="link-register-redirect"><i class="fas fa-arrow-left me-2"></i>Kembali ke Login</a>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


