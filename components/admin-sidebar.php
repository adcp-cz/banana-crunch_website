<?php
// Deteksi halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);

// Ambil data admin jika belum ada (asumsi koneksi database $koneksi sudah tersedia)
if (!isset($admin_name) || !isset($admin_avatar)) {
    $admin_id = $_SESSION['user_id'];
    $query_admin = mysqli_query($koneksi, "SELECT u.name, f.nama_foto 
                                           FROM users u 
                                           LEFT JOIN foto f ON u.id = f.user_id 
                                           WHERE u.id = $admin_id 
                                           ORDER BY f.created_at DESC LIMIT 1");
    $admin_data = mysqli_fetch_assoc($query_admin);
    $admin_name = $admin_data['name'] ?? 'Admin';
    $admin_avatar = !empty($admin_data['nama_foto']) ? '../assets/images/users/' . $admin_data['nama_foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($admin_name) . '&background=FFD600&color=8A6F00';
}
?>

<aside class="sidebar">
    <div>
        <div class="admin-profile">
            <img src="<?= $admin_avatar ?>" alt="Admin Avatar" class="admin-avatar">
            <div class="admin-info">
                <h5><?= htmlspecialchars($admin_name) ?></h5>
                <span>PisangKraf Management</span>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="dashboard.php" class="menu-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i>Dashboard
                </a>
            </li>
            <li class="menu-item">
                <a href="products.php" class="menu-link <?= ($current_page == 'products.php') ? 'active' : '' ?>">
                    <i class="fas fa-box"></i> Produk
                </a>
            </li>
            <li class="menu-item">
                <a href="orders.php" class="menu-link <?= ($current_page == 'orders.php') ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i> Pesanan
                </a>
            </li>
            <li class="menu-item">
                <a href="users.php" class="menu-link <?= ($current_page == 'users.php') ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Pengguna
                </a>
            </li>
            <li class="menu-item">
                <a href="profil_admin.php" class="menu-link <?= ($current_page == 'profil_admin.php') ? 'active' : '' ?>">
                    <i class="far fa-user"></i> Profil
                </a>
            </li>
        </ul>
    </div>
    <a href="../logout.php" class="logout-link">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</aside>
