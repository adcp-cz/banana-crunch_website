<?php
session_start();
require '../database/koneksi.php';

// 1. PROTEKSI HALAMAN
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pesan = ''; // Variabel untuk menampung notifikasi sukses/gagal

// 2. PROSES UPDATE PROFIL JIKA FORM DIKIRIM (Metode POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_baru = mysqli_real_escape_string($koneksi, $_POST['name']);
    $email_baru = mysqli_real_escape_string($koneksi, $_POST['email']);
    $telepon_baru = mysqli_real_escape_string($koneksi, $_POST['phone']);

    // Update data ke database
    $query_update = "UPDATE users SET name = '$nama_baru', email = '$email_baru', phone = '$telepon_baru' WHERE id = $user_id";
    
    if (mysqli_query($koneksi, $query_update)) {
        $pesan = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <i class='fas fa-check-circle me-2'></i> Profil Anda berhasil diperbarui!
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
    } else {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <i class='fas fa-exclamation-circle me-2'></i> Gagal memperbarui profil: " . mysqli_error($koneksi) . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
    }
}

// 3. AMBIL DATA PENGGUNA TERBARU (Untuk ditampilkan di form dan sidebar)
$query_user = "SELECT * FROM users WHERE id = $user_id";
$result_user = mysqli_query($koneksi, $query_user);
$user_data = mysqli_fetch_assoc($result_user);

$nama_lengkap = $user_data['name'];
$email = $user_data['email'];
$telepon = isset($user_data['phone']) ? $user_data['phone'] : '';

// Pembuatan Inisial (Contoh: Andhika Dwi -> AD)
$kata = explode(" ", $nama_lengkap);
$inisial = strtoupper(substr($kata[0], 0, 1) . (isset($kata[1]) ? substr($kata[1], 0, 1) : ''));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya | PisangKraf</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #FFD600;
            --secondary: #8A6F00;
            --bg-main: #FAFAFA;
            --bg-sidebar: #FFFFFF;
            --card-bg: #FFFFFF;
            --text-dark: #1F1F1F;
            --text-muted: #8E8E93;
            --border-color: #EFEFEF;
            --radius-lg: 20px;
            --radius-md: 12px;
            --radius-pill: 50px;
            --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.03);
            --sidebar-width: 280px;
            --transition: all 0.3s ease;
        }

        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-main); color: var(--text-dark); margin: 0; display: flex; height: 100vh; overflow: hidden; }

        /* --- SIDEBAR --- */
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; top: 0; left: 0; background-color: var(--bg-sidebar); border-right: 1px solid var(--border-color); padding: 30px 24px; display: flex; flex-direction: column; z-index: 100; }
        .brand-logo { font-size: 1.5rem; font-weight: 700; color: var(--secondary); text-decoration: none; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
        
        .user-profile-summary { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid var(--border-color); }
        .user-avatar-small { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; background-color: #FEF3C7; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #D97706; font-size: 1.2rem; border: 1px solid #FDE68A; }

        .sidebar-menu { list-style: none; padding: 0; flex-grow: 1; }
        .menu-link { display: flex; align-items: center; gap: 15px; padding: 14px 20px; color: var(--text-dark); font-weight: 500; border-radius: var(--radius-md); text-decoration: none; transition: var(--transition); margin-bottom: 8px; }
        .menu-link.active { background-color: var(--primary); font-weight: 600; }
        .menu-link:hover:not(.active) { background-color: #F5F5F7; }

        .logout-btn { color: #DC3545; font-weight: 600; margin-top: auto; }
        .logout-btn:hover { background-color: #FFF5F5; color: #DC3545;}

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: var(--sidebar-width); padding: 40px; flex-grow: 1; overflow-y: auto; height: 100vh; }
        .page-header { margin-bottom: 30px; }
        .page-title { font-weight: 700; font-size: 2rem; color: var(--text-dark); margin-bottom: 5px; }

        /* --- PROFILE CARDS --- */
        .profile-card { background-color: var(--card-bg); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); border: 1px solid var(--border-color); padding: 30px; margin-bottom: 25px; }
        
        /* Avatar Besar di Kiri */
        .avatar-large-container { text-align: center; padding: 20px 0; }
        .user-avatar-large { width: 120px; height: 120px; border-radius: 50%; background-color: #FEF3C7; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; color: #D97706; font-size: 3rem; border: 2px solid #FDE68A; margin-bottom: 20px; box-shadow: 0 10px 20px rgba(217, 119, 6, 0.15); }
        .badge-role { background-color: var(--primary); color: var(--text-dark); padding: 5px 15px; border-radius: var(--radius-pill); font-size: 0.8rem; font-weight: 600; }

        /* Form styling */
        .form-label { font-weight: 500; color: var(--text-muted); font-size: 0.9rem; margin-bottom: 8px; }
        .form-control { border-radius: 12px; padding: 12px 18px; border: 1px solid #E5E7EB; background-color: #F9FAFB; font-size: 0.95rem; transition: var(--transition); }
        .form-control:focus { background-color: #FFF; box-shadow: 0 0 0 4px rgba(255, 214, 0, 0.15); border-color: var(--primary); outline: none; }
        
        .btn-save { background-color: var(--text-dark); color: #fff; border-radius: var(--radius-pill); padding: 12px 30px; font-weight: 600; transition: var(--transition); border: none; }
        .btn-save:hover { background-color: var(--secondary); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(138, 111, 0, 0.2); }
    </style>
</head>
<body>
    <?php include '../navbar_login.php'; ?>

    <aside class="sidebar">
        <a href="../index.php" class="brand-logo">
            <i class="fas fa-leaf text-warning"></i> PisangKraf
        </a>

        <div class="user-profile-summary">
            <div class="user-avatar-small"><?= $inisial ?></div>
            <div>
                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($nama_lengkap) ?></h6>
                <small class="text-muted">Pelanggan Setia</small>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="menu-link"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="pesanan.php" class="menu-link"><i class="fas fa-shopping-bag"></i> Pesanan Saya</a></li>
            <li><a href="../index.php" class="menu-link"><i class="fas fa-store"></i> Belanja Lagi</a></li>
            <li><a href="profil_user.php" class="menu-link active"><i class="far fa-user"></i> Profil</a></li>
        </ul>

        <a href="../logout.php" class="menu-link logout-btn"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1 class="page-title">Pengaturan Profil</h1>
            <p class="text-muted">Kelola informasi data diri dan kontak Anda di sini.</p>
        </header>

        <?= $pesan ?>

        <div class="row g-4">
            <div class="col-xl-4 col-lg-5">
                <div class="profile-card h-100">
                    <div class="avatar-large-container">
                        <div class="user-avatar-large"><?= $inisial ?></div>
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($nama_lengkap) ?></h4>
                        <p class="text-muted mb-3"><?= htmlspecialchars($email) ?></p>
                        <span class="badge-role"><i class="fas fa-star me-1"></i> Pelanggan Setia</span>
                    </div>
                    
                    <hr class="my-4" style="border-color: #EFEFEF;">
                    
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: var(--secondary); margin-right: 15px;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">Keamanan Akun</h6>
                            <small class="text-muted">Terlindungi</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: var(--secondary); margin-right: 15px;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">Bergabung Sejak</h6>
                            <small class="text-muted"><?= date('F Y', strtotime($user_data['created_at'])) ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="profile-card h-100">
                    <h5 class="fw-bold mb-4 border-bottom pb-3">Detail Informasi Akun</h5>
                    
                    <form action="profil_user.php" method="POST">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($nama_lengkap) ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Alamat Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Nomor Telepon / WhatsApp</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($telepon) ?>" placeholder="Contoh: 08123456789">
                            </div>
                        </div>

                        <div class="p-3 mb-4 rounded" style="background-color: #FEF3C7; border-left: 4px solid var(--primary);">
                            <div class="d-flex">
                                <i class="fas fa-info-circle me-3 mt-1" style="color: var(--secondary);"></i>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                    Pastikan nomor telepon dan email Anda aktif. Kami akan menggunakan informasi ini untuk menghubungi Anda terkait pembaruan pesanan.
                                </p>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>