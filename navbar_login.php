<?php
// Logika cerdas untuk mendeteksi lokasi file.
// Jika navbar ini dipanggil dari dalam folder 'admin' ATAU 'user', 
// sistem akan otomatis menambahkan '../' agar link "Kembali" tidak error.
$path_prefix = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? '../' : '';

// Jika sudah login, ambil data foto profil
$user_photo = null;
if (isset($_SESSION['user_id'])) {
    if (!isset($koneksi)) {
        require_once __DIR__ . '/database/koneksi.php';
    }
    $uid = $_SESSION['user_id'];
    $q_foto = mysqli_query($koneksi, "SELECT nama_foto FROM foto WHERE user_id = $uid ORDER BY created_at DESC LIMIT 1");
    if ($q_foto && mysqli_num_rows($q_foto) > 0) {
        $f_data = mysqli_fetch_assoc($q_foto);
        $user_photo = $path_prefix . 'assets/images/users/' . $f_data['nama_foto'];
    }
}
?>

<nav class="navbar navbar-expand-lg fixed-top" style="padding: 15px 0; background: #FFFFFF; border-bottom: 1px solid #EFEFEF; box-shadow: 0 4px 20px rgba(0,0,0,0.02); z-index: 1050;">
    <div class="container">
        <div class="d-flex align-items-center">
            <!-- Sidebar Toggle (Mobile only) -->
            <?php if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/user/') !== false): ?>
            <button class="btn border-0 d-lg-none me-2 p-0" id="sidebarToggle" type="button">
                <i class="fas fa-bars fs-4 text-dark"></i>
            </button>
            <?php endif; ?>
            
            <a class="navbar-brand fw-bold" href="<?= $path_prefix ?>index.php" style="color: #8A6F00 !important; font-size: 1.5rem; letter-spacing: -0.5px;">
                Jajan Pisang
            </a>
        </div>
        
        <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarLogin" aria-controls="navbarLogin" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarLogin">
            <ul class="navbar-nav ms-auto align-items-center mt-3 mt-lg-0">
                <?php if ($user_photo): ?>
                <li class="nav-item me-lg-3 mb-3 mb-lg-0 d-none d-lg-block">
                    <img src="<?= $user_photo ?>" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #FFD600;">
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-4 py-2" href="<?= $path_prefix ?>index.php" 
                       style="color: rgb(68, 68, 68); background-color: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 50px; transition: all 0.3s ease;"
                       onmouseover="this.style.backgroundColor='#FFD600'; this.style.borderColor='#FFD600';"
                       onmouseout="this.style.backgroundColor='#F9FAFB'; this.style.borderColor='#E5E7EB';">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar Overlay (Mobile only) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar && overlay) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        });
    }
});
</script>

<div style="height: 80px;"></div>
