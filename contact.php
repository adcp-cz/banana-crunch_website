
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami | Jajan Pisang</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #FFD600;
            --secondary: #8A6F00;
            --bg-light: #FAFAFA;
            --card-bg: #FFFFFF;
            --text-dark: rgb(68, 68, 68);
            --text-muted: #6c757d;
            --radius-btn: 30px;
        }

        body { font-family: 'Open Sans', sans-serif; background-color: var(--bg-light); color: var(--text-dark); padding-top: 100px; }
        
         

        /* Contact Styles */
        .contact-card { background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: none; }
        .contact-info-item { display: flex; align-items: flex-start; margin-bottom: 30px; }
        .icon-box { width: 50px; height: 50px; background: rgba(255, 214, 0, 0.15); color: var(--secondary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-right: 20px; flex-shrink: 0; }
        
        .form-control { border-radius: 12px; padding: 12px 18px; border: 1px solid #E5E7EB; }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(255, 214, 0, 0.15); border-color: var(--primary); }
        
        .btn-send { background-color: var(--text-dark); color: #fff; border-radius: var(--radius-btn); padding: 12px 30px; font-weight: 600; transition: 0.3s; }
        .btn-send:hover { background-color: var(--secondary); color: #fff; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/components/navbar.php'; ?>

    <style>
        /* --- NAVBAR --- */
        .navbar-custom {
            padding: 15px 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--secondary) !important;
        }
        .nav-link {
            font-weight: 500;
            color: var(--text-dark) !important;
            margin: 0 10px;
            transition: var(--transition);
        }
        .nav-link:hover, .nav-link.active { color: var(--secondary) !important; }
        .nav-icons a {
            color: var(--text-dark);
            margin-left: 20px;
            font-size: 1.2rem;
            transition: var(--transition);
        }
        .nav-icons a:hover { color: var(--secondary); }
        .cart-badge {
            background-color: var(--secondary);
            font-size: 0.6rem;
            transform: translate(-10px, -10px);
        }
    </style>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Butuh Bantuan?</h1>
            <p class="text-muted">Kami siap menjawab pertanyaan Anda seputar produk dan kemitraan.</p>
        </div>

        <div class="row g-5">
            <div class="col-lg-5">
                <div class="contact-card h-100">
                    <h4 class="fw-bold mb-4">Informasi Kontak</h4>
                    
                    <div class="contact-info-item">
                        <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Alamat</h6>
                            <p class="text-muted mb-0">Jl. Raya Purwokerto No. 1, Purwokerto, Jawa Tengah</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="icon-box"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Email</h6>
                            <p class="text-muted mb-0">hello@jajanpisang.com</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="icon-box"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">WhatsApp</h6>
                            <p class="text-muted mb-0">+62 821 3588 7896</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="contact-card">
                    <h4 class="fw-bold mb-4">Kirim Pesan</h4>
                    <form action="#" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" placeholder="Nama Anda" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="email@anda.com" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subjek</label>
                            <input type="text" class="form-control" placeholder="Apa yang bisa kami bantu?" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Pesan</label>
                            <textarea class="form-control" rows="5" placeholder="Tuliskan pesan Anda di sini..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-send w-100">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-5 bg-white text-center border-top">
        <div class="container">
            <p class="text-muted">&copy; 2026 Jajan Pisang. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


