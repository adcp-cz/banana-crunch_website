<?php
session_start();
// Hubungkan ke database
require '../database/koneksi.php';

// Ambil semua data pengguna dari database, urutkan dari yang terbaru
$query = "SELECT * FROM users ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);

$users = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna | Admin Panel</title>
    
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
            --radius-lg: 20px;
            --radius-md: 12px;
            --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.03);
            --sidebar-width: 280px;
            --transition: all 0.3s ease;
        }

        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-main); color: var(--text-dark); }

        /* --- SIDEBAR --- */
        .sidebar {
    width: var(--sidebar-width);
    /* Kurangi tinggi layar tepat sebesar ukuran navbar atas (80px) */
    height: calc(100vh - 80px); 
    position: fixed;
    /* Turunkan sejauh ukuran navbar (80px) dan JANGAN LUPA 'px' */
    top: 80px; 
    left: 0;
    background-color: var(--bg-sidebar);
    border-right: 1px solid #EFEFEF;
    padding: 30px 24px;
    display: flex;
    flex-direction: column;
    z-index: 100;
    /* Tambahan agar jika layarnya kecil, sidebarnya bisa di-scroll ke bawah */
    overflow-y: auto; 
}
        .admin-profile { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; }
        .admin-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); }
        .sidebar-menu { list-style: none; padding: 0; flex-grow: 1;}
        .menu-link { display: flex; align-items: center; gap: 15px; padding: 14px 20px; color: var(--text-dark); font-weight: 500; border-radius: var(--radius-md); text-decoration: none; transition: var(--transition); }
        .menu-link.active { background-color: var(--primary); font-weight: 600; }
        .menu-link:hover:not(.active) { background-color: #F5F5F7; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: var(--sidebar-width); padding: 40px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .table-card { background-color: var(--card-bg); border-radius: var(--radius-lg); padding: 25px; box-shadow: var(--shadow-soft); border: none; }
        .custom-table thead th { background-color: #F9FAFB; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; padding: 15px 20px; border-bottom: none; }
        .custom-table tbody td { padding: 18px 20px; vertical-align: middle; border-bottom: 1px solid #F2F2F7; font-size: 0.9rem; }

        /* --- BADGES --- */
        .status-badge { padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; }
        .role-admin { background-color: #FEF3C7; color: #D97706; }
        .role-user { background-color: #F3F4F6; color: #4B5563; }
        .status-active { background-color: #DCFCE7; color: #15803D; }
        .status-inactive { background-color: #FEE2E2; color: #B91C1C; }

        .btn-action { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: none; transition: var(--transition); text-decoration: none; }
        .btn-edit { background-color: #F2F2F7; color: var(--text-dark); }
        .btn-edit:hover { background-color: var(--primary); color: var(--text-dark);}
    </style>
</head>
<body>
    <?php include '../navbar_login.php'; ?>

    <aside class="sidebar">
        <div>
            <div class="admin-profile">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200&auto=format&fit=crop" class="admin-avatar" alt="Admin">
                <div class="admin-info">
                    <h6 class="mb-0 fw-bold">Admin Panel</h6>
                    <small class="text-muted">Manajemen Pengguna</small>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li class="mb-2"><a href="dashboard.php" class="menu-link"><i class="fas fa-th-large"></i> Ringkasan</a></li>
                <li class="mb-2"><a href="products.php" class="menu-link"><i class="fas fa-box"></i> Produk</a></li>
                <li class="mb-2"><a href="orders.php" class="menu-link"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li class="mb-2"><a href="users.php" class="menu-link active"><i class="fas fa-users"></i> Pengguna</a></li>
            </ul>
        </div>
        <a href="../logout.php" class="menu-link text-danger mt-auto"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <h2 class="fw-bold mb-1">Daftar Pengguna</h2>
                <p class="text-muted small mb-0">Kelola akun admin dan pelanggan yang terdaftar.</p>
            </div>
        </header>

        <div class="table-card">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $row): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #E5E7EB; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #6B7280; margin-right: 15px;">
                                                <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                            </div>
                                            <span class="fw-semibold"><?= htmlspecialchars($row['name']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td>
                                        <?php if ($row['role'] == 'admin'): ?>
                                            <span class="status-badge role-admin"><i class="fas fa-shield-alt me-1"></i> Admin</span>
                                        <?php else: ?>
                                            <span class="status-badge role-user"><i class="far fa-user me-1"></i> User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['is_active'] == 1): ?>
                                            <span class="status-badge status-active">Aktif</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">Non-Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#modalUser<?= $row['id'] ?>" title="Edit Pengguna">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada pengguna yang terdaftar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php foreach ($users as $row): ?>
        <div class="modal fade" id="modalUser<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 20px; border: none; padding: 10px;">
                    <div class="modal-header border-0">
                        <h5 class="fw-bold">Edit Pengguna</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="proses_user.php" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted">Nama Lengkap</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" readonly style="background-color: #F9FAFB;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium mb-2">Ubah Hak Akses (Role)</label>
                                <select class="form-select" name="role" style="border-radius: 12px; padding: 12px;" required>
                                    <option value="user" <?= ($row['role'] == 'user') ? 'selected' : '' ?>>User (Pelanggan)</option>
                                    <option value="admin" <?= ($row['role'] == 'admin') ? 'selected' : '' ?>>Admin (Pengelola)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium mb-2">Status Akun</label>
                                <select class="form-select" name="is_active" style="border-radius: 12px; padding: 12px;" required>
                                    <option value="1" <?= ($row['is_active'] == 1) ? 'selected' : '' ?>>Aktif (Bisa Login)</option>
                                    <option value="0" <?= ($row['is_active'] == 0) ? 'selected' : '' ?>>Non-Aktif (Diblokir)</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>