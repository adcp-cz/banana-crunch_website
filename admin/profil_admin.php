<?php
session_start();
require '../database/koneksi.php';

// 1. PROTEKSI HALAMAN ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$pesan = '';

// 2. PROSES UPDATE PROFIL
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_baru = mysqli_real_escape_string($koneksi, $_POST['name']);
    $email_baru = mysqli_real_escape_string($koneksi, $_POST['email']);
    $telepon_baru = mysqli_real_escape_string($koneksi, $_POST['phone']);

    $query_update = "UPDATE users SET name = '$nama_baru', email = '$email_baru', phone = '$telepon_baru' WHERE id = $admin_id";
    
    if (mysqli_query($koneksi, $query_update)) {
        $pesan = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <i class='fas fa-check-circle me-2'></i> Profil Admin berhasil diperbarui!
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
    } else {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <i class='fas fa-exclamation-circle me-2'></i> Gagal memperbarui profil: " . mysqli_error($koneksi) . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
    }
}

// 3. AMBIL DATA ADMIN TERBARU
$query_admin = mysqli_query($koneksi, "SELECT u.*, f.nama_foto 
                                       FROM users u 
                                       LEFT JOIN foto f ON u.id = f.user_id 
                                       WHERE u.id = $admin_id 
                                       ORDER BY f.created_at DESC LIMIT 1");
$admin_data = mysqli_fetch_assoc($query_admin);
$admin_name = $admin_data['name'];
$admin_email = $admin_data['email'];
$admin_phone = $admin_data['phone'] ?? '';
$admin_avatar = !empty($admin_data['nama_foto']) ? '../assets/images/users/' . $admin_data['nama_foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($admin_name) . '&background=FFD600&color=8A6F00';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin | Jajan Pisang</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

    <style>
        /* --- PROFIL SPECIFIC STYLES --- */
        .dashboard-panel-card { background-color: var(--card-bg); border-radius: var(--radius-lg); padding: 30px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); border: none; margin-bottom: 30px; }
        .panel-card-title { font-weight: 700; font-size: 1.2rem; color: var(--text-dark); }
        
        .form-label { font-weight: 500; color: var(--text-muted); font-size: 0.9rem; }
        .form-control { border-radius: 12px; padding: 12px 18px; border: 1px solid #E5E7EB; background-color: #F9FAFB; }
        .form-control:focus { background-color: #FFF; box-shadow: 0 0 0 4px rgba(255, 214, 0, 0.15); border-color: var(--primary); outline: none; }
        .btn-save { background-color: var(--text-dark); color: #fff; border-radius: 50px; padding: 12px 30px; font-weight: 600; border: none; transition: var(--transition); }
        .btn-save:hover { background-color: var(--secondary); transform: translateY(-2px); }
    </style>
</head>
<body>
    <?php include '../navbar_login.php'; ?>

    <?php include '../components/admin-sidebar.php'; ?>

    <main class="main-content">
        <div class="dash-header mb-4">
            <h1 style="font-weight: 700; font-size: 2rem;">Profil Admin</h1>
            <p class="text-muted">Kelola informasi profil dan galeri foto Anda.</p>
        </div>

        <?= $pesan ?>

        <div class="row g-4">
            <!-- Form Profil -->
            <div class="col-lg-7">
                <div class="dashboard-panel-card">
                    <h2 class="panel-card-title mb-4 border-bottom pb-3">Detail Akun Admin</h2>
                    <form action="profil_admin.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($admin_name) ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin_email) ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($admin_phone) ?>" placeholder="08xxxx">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn-save"><i class="fas fa-save me-2"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Galeri Foto (Pindahan dari Dashboard) -->
            <div class="col-lg-5">
                <div class="dashboard-panel-card">
                    <h2 class="panel-card-title mb-4 border-bottom pb-3">Galeri Foto Admin</h2>
                    
                    <form action="../proses_profil.php" method="POST" enctype="multipart/form-data" class="mb-4">
                        <div class="input-group">
                            <input type="file" name="foto" class="form-control" accept="image/*" required>
                            <button class="btn btn-warning fw-bold" type="submit">Unggah</button>
                        </div>
                    </form>

                    <div class="row g-3">
                        <?php
                        $query_foto = mysqli_query($koneksi, "SELECT * FROM foto WHERE user_id = $admin_id ORDER BY created_at DESC");
                        if (mysqli_num_rows($query_foto) > 0):
                            while ($f = mysqli_fetch_assoc($query_foto)):
                        ?>
                        <div class="col-4">
                            <div class="position-relative">
                                <img src="../assets/images/users/<?= $f['nama_foto'] ?>" class="img-fluid rounded border w-100" style="height: 80px; object-fit: cover;">
                                <a href="../proses_profil.php?action=hapus&id=<?= $f['id'] ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle" style="padding: 2px 6px; font-size: 10px;" onclick="return confirm('Hapus foto ini?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <div class="col-12 text-center py-4 bg-light rounded border-dashed">
                            <p class="text-muted small mb-0">Belum ada foto.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

