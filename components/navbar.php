<?php
$currentPage = basename($_SERVER['PHP_SELF']);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartCount = 0;

if (isset($_SESSION['user_id'])) {

    require_once __DIR__ . '/../database/koneksi.php';

    $user_id = (int) $_SESSION['user_id'];

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
}
?>

<style>
    .custom-navbar {
        background-color: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow-sm);
        padding: 15px 0;
        transition: all 0.3s ease;
        z-index: 1030;
    }

    .custom-navbar.scrolled {
        padding: 10px 0;
        box-shadow: var(--shadow-md);
    }

    .navbar-brand {
        font-family: var(--font-family);
        font-weight: var(--weight-bold);
        font-size: var(--text-2xl);
        color: var(--color-primary-dark) !important;
        letter-spacing: -0.5px;
    }

    .nav-item {
        margin: 0 10px;
    }

    .nav-link {
        font-family: var(--font-family);
        font-weight: var(--weight-medium);
        font-size: var(--text-base);
        color: var(--color-text-secondary) !important;
        position: relative;
        padding: 8px 0 !important;
        transition: all .3s ease;
    }

    .nav-link:hover {
        color: var(--color-text) !important;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        left: 50%;
        background-color: var(--color-primary);
        transition: all .3s ease;
        transform: translateX(-50%);
        border-radius: 999px;
    }

    .nav-link:hover::after,
    .nav-link.active::after {
        width: 100%;
    }

    .nav-link.active {
        color: var(--color-text) !important;
        font-weight: var(--weight-semibold);
    }

    .nav-icons {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .icon-link {
        color: var(--color-text);
        font-size: var(--text-lg);
        position: relative;
        transition: all .3s ease;
    }

    .icon-link:hover {
        color: var(--color-primary-dark);
        transform: translateY(-2px);
    }

    .cart-badge{
        position:absolute;
        top:-8px;
        right:-10px;
        min-width:20px;
        height:20px;
        display:flex;
        align-items:center;
        justify-content:center;
        border-radius:50%;
        font-size:11px;
        font-weight:bold;
        background:#FFD600;
        color:#000;
        border:2px solid white;
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
</style>

<nav class="navbar navbar-expand-lg fixed-top custom-navbar">

    <div class="container">

        <a class="navbar-brand" href="index.php">
            Jajan Pisang
        </a>

        <button
            class="navbar-toggler border-0 shadow-none"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarPisangKraf">

            <i class="fas fa-bars fs-4 text-dark"></i>

        </button>

        <div class="collapse navbar-collapse" id="navbarPisangKraf">

            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center">

                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'index.php' ? 'active' : '' ?>"
                       href="index.php">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'products.php' ? 'active' : '' ?>"
                       href="products.php">
                        Products
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'about.php' ? 'active' : '' ?>"
                       href="about.php">
                        About
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'contact.php' ? 'active' : '' ?>"
                       href="contact.php">
                        Contact
                    </a>
                </li>

            </ul>

            <div class="nav-icons">

                <a href="#" class="icon-link">
                    <i class="fas fa-search"></i>
                </a>

                <a href="chart.php" class="icon-link">
                    <i class="fas fa-shopping-cart"></i>

                    <?php if($cartCount > 0): ?>
                        <span class="cart-badge">
                            <?= $cartCount ?>
                        </span>
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
                <a href="<?= $account_link ?>" class="icon-link">
                    <i class="far fa-user"></i>
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
```
