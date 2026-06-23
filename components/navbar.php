<?php
$currentPage = basename($_SERVER['PHP_SELF']);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartCount = 0;
$userInitials = '';
$userName = '';

if (isset($_SESSION['user_id'])) {

    require_once __DIR__ . '/../database/koneksi.php';

    $user_id = (int) $_SESSION['user_id'];

    // Ambil data keranjang
    $queryCart = mysqli_query(
        $koneksi,
        "SELECT SUM(qty) AS total_qty
         FROM cart
         WHERE user_id = $user_id"
    );

    if ($queryCart) {
        $dataCart = mysqli_fetch_assoc($queryCart);
        $cartCount = (int) ($dataCart['total_qty'] ?? 0);
    }

    // Ambil data nama user jika tidak ada di session
    if (!isset($_SESSION['user_name'])) {
        $queryUser = mysqli_query($koneksi, "SELECT name FROM users WHERE id = $user_id");
        $userData = mysqli_fetch_assoc($queryUser);
        $_SESSION['user_name'] = $userData['name'];
    }
    
    $userName = $_SESSION['user_name'];
    $nameParts = explode(" ", $userName);
    $userInitials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));

    // Ambil foto profil terbaru
    $queryPhoto = mysqli_query($koneksi, "SELECT nama_foto FROM foto WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 1");
    $userPhotoData = mysqli_fetch_assoc($queryPhoto);
    $userPhoto = !empty($userPhotoData['nama_foto']) ? 'assets/images/users/' . $userPhotoData['nama_foto'] : null;
}
?>

<style>
    .custom-navbar {
        background-color: rgba(255,255,255,0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        padding: 20px 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1030;
        border-bottom: 1px solid rgba(255,255,255,0.3);
    }

    .custom-navbar.scrolled {
        padding: 12px 0;
        background-color: rgba(255,255,255,0.95);
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
    }

    /* Pengaturan Logo pada Navbar */
    .navbar-logo {
        height: 45px; /* Atur ukuran logo di sini */
        width: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
        border-radius: 8px; /* Sudut sedikit melengkung */
    }

    .navbar-brand:hover .navbar-logo {
        transform: scale(1.05); 
    }

    .nav-item {
        margin: 0 12px;
    }

    .nav-link {
        font-family: var(--font-family, 'Open Sans', sans-serif);
        font-weight: 500;
        font-size: 1rem;
        color: #64748B !important;
        position: relative;
        padding: 8px 0 !important;
        transition: all .3s ease;
    }

    .nav-link:hover {
        color: rgb(68, 68, 68) !important;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        left: 50%;
        background-color: #FACC15;
        transition: all .3s ease;
        transform: translateX(-50%);
        border-radius: 999px;
    }

    .nav-link:hover::after,
    .nav-link.active::after {
        width: 100%;
    }

    .nav-link.active {
        color: rgb(68, 68, 68) !important;
        font-weight: 600;
    }

    .nav-icons {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .icon-link {
        color: rgb(68, 68, 68);
        font-size: 1.15rem;
        position: relative;
        transition: all .3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #F8FAFC;
    }

    .icon-link:hover {
        background: #FACC15;
        color: rgb(68, 68, 68);
        transform: translateY(-2px);
    }

    .user-initials-circle {
        width: 40px;
        height: 40px;
        background-color: #FACC15;
        color: #CA8A04;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .user-initials-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .icon-link:hover .user-initials-circle {
        background-color: #CA8A04;
        color: #FACC15;
    }

    .cart-badge {
        position:absolute;
        top:-5px;
        right:-5px;
        min-width:22px;
        height:22px;
        display:flex;
        align-items:center;
        justify-content:center;
        border-radius:50%;
        font-size:12px;
        font-weight:bold;
        background:#CA8A04;
        color:#fff;
        border:2px solid #fff;
    }

    @media (max-width: 991.98px) {
        .custom-navbar {
            padding: 10px 0;
        }
        .nav-icons {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--color-border);
            justify-content: center;
        }
        .nav-link::after {
            display: none;
        }
        .nav-link {
            padding: 10px 15px !important;
            border-radius: var(--radius-md);
        }
        .nav-link.active,
        .nav-link:hover {
            background: rgba(255,214,0,0.1);
        }
    }

    /* Transparent Navbar for index.php */
    .custom-navbar.navbar-transparent {
        background-color: transparent !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        box-shadow: none !important;
        border: none !important;
    }
    
    .custom-navbar.navbar-transparent.scrolled {
        background-color: #ffffff !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08) !important;
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
    }

    .custom-navbar.navbar-transparent:not(.scrolled) .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .custom-navbar.navbar-transparent:not(.scrolled) .nav-link:hover,
    .custom-navbar.navbar-transparent:not(.scrolled) .nav-link.active {
        color: #ffffff !important;
    }

    .custom-navbar.navbar-transparent:not(.scrolled) .nav-link::after {
        background-color: #ffffff;
    }

    .custom-navbar.navbar-transparent:not(.scrolled) .icon-link {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }

    .custom-navbar.navbar-transparent:not(.scrolled) .icon-link:hover {
        background: rgba(255, 255, 255, 0.4);
        color: #ffffff;
    }

    .custom-navbar.navbar-transparent:not(.scrolled) .navbar-toggler i {
        color: #ffffff !important;
    }
</style>

<nav class="navbar navbar-expand-lg fixed-top custom-navbar <?= $currentPage == 'index.php' ? 'navbar-transparent' : '' ?>">
    <div class="container">
        
        <a class="navbar-brand d-flex align-items-center" href="index.php" style="padding: 0;">
            <img src="assets/images/logo-jajanpisang.png" class="navbar-logo" alt="Jajan Pisang">
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarJajanPisang">
            <i class="fas fa-bars fs-4 text-dark"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarJajanPisang">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'products.php' ? 'active' : '' ?>" href="products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'about.php' ? 'active' : '' ?>" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'contact.php' ? 'active' : '' ?>" href="contact.php">Contact</a>
                </li>
            </ul>

            <div class="nav-icons">
                <a href="#" class="icon-link"><i class="fas fa-search"></i></a>
                <a href="chart.php" class="icon-link">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if($cartCount > 0): ?>
                        <span class="cart-badge"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>

                <?php
                $account_link = 'login.php';
                if (isset($_SESSION['user_id'])) {
                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                        $account_link = 'admin/dashboard.php';
                    } else {
                        $account_link = 'user/dashboard.php';
                    }
                }
                ?>
                <a href="<?= $account_link ?>" class="icon-link" title="<?= isset($_SESSION['user_id']) ? htmlspecialchars($userName) : 'Login / Register' ?>">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-initials-circle">
                            <?php if ($userPhoto): ?>
                                <img src="<?= $userPhoto ?>" alt="<?= htmlspecialchars($userName) ?>">
                            <?php else: ?>
                                <?= $userInitials ?>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <i class="far fa-user"></i>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.custom-navbar');
    function updateNavbar() {
        if(window.scrollY > 50){
            navbar.classList.add('scrolled');
        }else{
            navbar.classList.remove('scrolled');
        }
    }
    updateNavbar();
    window.addEventListener('scroll', updateNavbar);
});
</script>

