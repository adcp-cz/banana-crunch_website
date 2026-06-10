
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami | PisangKraf</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #FFD600;
            --secondary: #8A6F00;
            --bg-light: #FAFAFA;
            --text-dark: #1F1F1F;
            --text-muted: #6c757d;
            --radius-md: 12px;
            --transition: all 0.3s ease;
        }

        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-light); color: var(--text-dark); }
        
        

        /* --- HERO --- */
        .about-hero { padding: 150px 0 100px; text-align: center; background: linear-gradient(to bottom, #FFFDEB, #FAFAFA); }
        .about-hero h1 { font-weight: 700; font-size: 3rem; margin-bottom: 20px; }
        
        /* --- CONTENT --- */
        .story-section { padding: 80px 0; }
        .image-placeholder { border-radius: var(--radius-md); width: 100%; height: 400px; object-fit: cover; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        
        .value-card { background: #fff; padding: 30px; border-radius: var(--radius-md); height: 100%; transition: var(--transition); border: 1px solid #eee; }
        .value-card:hover { transform: translateY(-5px); border-color: var(--primary); }
        .value-icon { font-size: 2rem; color: var(--secondary); margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/components/navbar.php';?>

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

    <header class="about-hero" data-aos="fade-in">
        <div class="container">
            <h1 class="text-uppercase">Tentang PisangKraf</h1>
            <p class="text-muted lead">Mengolah komoditas lokal menjadi produk bernilai tinggi.</p>
        </div>
    </header>

    <section class="story-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="https://i.pinimg.com/736x/5b/f0/fc/5bf0fcacfc23fde493897a19e239bb17.jpg" class="image-placeholder" alt="Processing">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <h2 class="fw-bold mb-4">Filosofi Kami</h2>
                    <p class="mb-4 text-muted">Didirikan di Purwokerto, PisangKraf lahir dari kecintaan kami terhadap potensi hasil bumi Indonesia. Kami percaya bahwa pengolahan yang tepat—mulai dari pemilihan bahan baku hingga proses produksi—dapat mengangkat derajat produk lokal ke taraf premium.</p>
                    <p class="text-muted">Fokus kami bukan sekadar berjualan, melainkan membangun ekosistem yang menghubungkan kualitas petani dengan keinginan konsumen akan camilan yang sehat, alami, dan autentik.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-microchip"></i></div>
                        <h4>Teknik Presisi</h4>
                        <p class="text-muted">Kami menerapkan kontrol kualitas yang ketat pada setiap tahap produksi, memastikan konsistensi rasa dan tekstur.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-users"></i></div>
                        <h4 >Kolaborasi Lokal</h4>
                        <p class="text-muted">Kami bermitra langsung dengan petani pisang lokal untuk memastikan rantai pasok yang adil dan berkelanjutan.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-infinity"></i></div>
                        <h4>Inovasi Berkelanjutan</h4>
                        <p class="text-muted">Selaras dengan latar belakang kami di dunia teknik, kami terus bereksperimen dengan metode pengolahan yang lebih efisien dan modern.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 mt-5 bg-light text-center">
        <div class="container">
            <p class="text-muted">&copy; 2026 PisangKraf. Semua Hak Dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init();</script>
</body>
</html>