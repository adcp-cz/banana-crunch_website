<?php
session_start();
// Hubungkan ke database
require '../database/koneksi.php';

// 1. PROTEKSI HALAMAN ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

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
    <link rel="stylesheet" href="../assets/css/admin.css">

    <style>
        /* --- USERS SPECIFIC STYLES --- */
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

    <?php include '../components/admin-sidebar.php'; ?>

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