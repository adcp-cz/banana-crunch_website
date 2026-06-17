<?php
// Deteksi halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);

// Path prefix untuk gambar jika di dalam folder user
$path_prefix = (strpos($_SERVER['PHP_SELF'], '/user/') !== false) ? '../' : '';

// Ambil data user jika belum ada (asumsi koneksi database $koneksi sudah tersedia)
if (!isset($nama_lengkap) || !isset($user_avatar)) {
    if (!isset($koneksi)) {
        require_once $path_prefix . 'database/koneksi.php';
    }
    $user_id = $_SESSION['user_id'];
    $query_sidebar_user = mysqli_query($koneksi, "SELECT u.name, f.nama_foto 
                                          FROM users u 
                                          LEFT JOIN foto f ON u.id = f.user_id 
                                          WHERE u.id = $user_id 
                                          ORDER BY f.created_at DESC LIMIT 1");
    $sidebar_user_data = mysqli_fetch_assoc($query_sidebar_user);
    $nama_lengkap = $sidebar_user_data['name'] ?? 'User';
    
    // Inisial Nama
    $kata = explode(" ", $nama_lengkap);
    $inisial = strtoupper(substr($kata[0], 0, 1) . (isset($kata[1]) ? substr($kata[1], 0, 1) : ''));
    
    $user_avatar = !empty($sidebar_user_data['nama_foto']) ? $path_prefix . 'assets/images/users/' . $sidebar_user_data['nama_foto'] : null;
}
?>

<aside class="sidebar">
    <a href="<?= $path_prefix ?>index.php" class="brand-logo mb-4 d-flex align-items-center gap-2 text-decoration-none">
        <i class="fas fa-leaf text-warning fs-3"></i>
        <span class="fw-bold fs-4" style="color: var(--secondary);">PisangKraf</span>
    </a>

    <div class="user-profile-summary">
        <div class="user-avatar-sidebar">
            <?php if ($user_avatar): ?>
                <img src="<?= $user_avatar ?>" alt="User Avatar">
            <?php else: ?>
                <?= $inisial ?>
            <?php endif; ?>
        </div>
        <div>
            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($nama_lengkap) ?></h6>
            <small class="text-muted">Pelanggan Setia</small>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="menu-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="pesanan.php" class="menu-link <?= ($current_page == 'pesanan.php') ? 'active' : '' ?>">
                <i class="fas fa-shopping-bag"></i> Pesanan Saya
            </a>
        </li>
        <li>
            <a href="<?= $path_prefix ?>index.php" class="menu-link">
                <i class="fas fa-store"></i> Belanja Lagi
            </a>
        </li>
        <li>
            <a href="profil_user.php" class="menu-link <?= ($current_page == 'profil_user.php') ? 'active' : '' ?>">
                <i class="far fa-user"></i> Profil
            </a>
        </li>
    </ul>

    <a href="<?= $path_prefix ?>logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</aside>
