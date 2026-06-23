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
    <title>Profil Saya | Jajan Pisang</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user.css">

    <style>
        /* --- PROFILE CARDS --- */
        .profile-card { background-color: var(--card-bg); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); border: 1px solid var(--border-color); padding: 30px; margin-bottom: 25px; }
        
        /* Avatar Besar di Kiri */
        .avatar-large-container { text-align: center; padding: 20px 0; }
        .user-avatar-large { width: 120px; height: 120px; border-radius: 50%; background-color: #FEF3C7; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; color: #D97706; font-size: 3rem; border: 2px solid #FDE68A; margin-bottom: 20px; box-shadow: 0 10px 20px rgba(217, 119, 6, 0.15); overflow: hidden; }
        .user-avatar-large img { width: 100%; height: 100%; object-fit: cover; }
        .badge-role { background-color: var(--primary); color: var(--text-dark); padding: 5px 15px; border-radius: var(--radius-pill); font-size: 0.8rem; font-weight: 600; }

        /* Form styling */
        .form-label { font-weight: 500; color: var(--text-muted); font-size: 0.9rem; margin-bottom: 8px; }
        .form-control { border-radius: 12px; padding: 12px 18px; border: 1px solid #E5E7EB; background-color: #F9FAFB; font-size: 0.95rem; transition: var(--transition); }
        .form-control:focus { background-color: #FFF; box-shadow: 0 0 0 4px rgba(255, 214, 0, 0.15); border-color: var(--primary); outline: none; }
        
        .btn-save { background-color: var(--text-dark); color: #fff; border-radius: var(--radius-pill); padding: 12px 30px; font-weight: 600; transition: var(--transition); border: none; }
        .btn-save:hover { background-color: var(--secondary); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(138, 111, 0, 0.2); }

        .border-dashed { border: 2px dashed #E5E7EB !important; }
        .group-hover:hover .btn-danger { opacity: 1; }
        .btn-danger { transition: opacity 0.3s ease; }
    </style>
</head>
<body>
    <?php include '../navbar_login.php'; ?>
    <?php include '../components/user-sidebar.php'; ?>

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
                        <?php if ($user_avatar): ?>
                            <div class="user-avatar-large" style="border: 4px solid var(--primary);">
                                <img src="<?= $user_avatar ?>" alt="User Avatar">
                            </div>
                        <?php else: ?>
                            <div class="user-avatar-large"><?= $inisial ?></div>
                        <?php endif; ?>
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
                            <small class="text-muted"><?= (!empty($user_data['created_at'])) ? date('F Y', strtotime($user_data['created_at'])) : 'Baru Bergabung' ?></small>
                        </div>
                    </div>
                </div>
            </div>

                    <div class="col-xl-8 col-lg-7">
                <div class="profile-card mb-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-3">Galeri Foto Saya</h5>
                    
                    <!-- Form Tambah Foto -->
                    <form action="../proses_profil.php" method="POST" enctype="multipart/form-data" class="mb-4">
                        <div class="input-group">
                            <input type="file" name="foto" class="form-control" accept="image/*" required>
                            <button class="btn btn-warning fw-bold px-4" type="submit">Tambah Foto</button>
                        </div>
                        <small class="text-muted mt-2 d-block">Maksimal 2MB, Format: JPG, JPEG, PNG</small>
                    </form>

                    <!-- List Foto -->
                    <div class="row g-3">
                        <?php
                        $query_foto = mysqli_query($koneksi, "SELECT * FROM foto WHERE user_id = $user_id ORDER BY created_at DESC");
                        if (mysqli_num_rows($query_foto) > 0):
                            while ($f = mysqli_fetch_assoc($query_foto)):
                        ?>
                        <div class="col-md-3 col-6">
                            <div class="position-relative group-hover">
                                <img src="../assets/images/users/<?= $f['nama_foto'] ?>" class="img-fluid rounded border shadow-sm w-100" style="height: 120px; object-fit: cover;">
                                <a href="../proses_profil.php?action=hapus&id=<?= $f['id'] ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle" onclick="return confirm('Hapus foto ini?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <div class="col-12 text-center py-4 bg-light rounded border-dashed">
                            <i class="far fa-image fs-2 text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0">Belum ada foto tambahan.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-card">
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

