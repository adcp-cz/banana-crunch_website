<?php
session_start();
// Pastikan file koneksi sudah benar
require '../database/koneksi.php';

// Ambil semua data produk dari database, urutkan dari yang terbaru
$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);

// TAMPUNG DATA KE DALAM ARRAY AGAR BISA DILOOPING 2 KALI (Tabel & Modal)
$products = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk | PisangKraf Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- DESIGN SYSTEM REUSE --- */
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

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background-color: var(--bg-sidebar);
            border-right: 1px solid #EFEFEF;
            padding: 30px 24px;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        .admin-profile { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; }
        .admin-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); }
        
        .sidebar-menu { list-style: none; padding: 0; flex-grow: 1;}
        .menu-link {
            display: flex; align-items: center; gap: 15px; padding: 14px 20px;
            color: var(--text-dark); font-weight: 500; border-radius: var(--radius-md);
            text-decoration: none; transition: var(--transition);
        }
        .menu-link.active { background-color: var(--primary); font-weight: 600; }
        .menu-link:hover:not(.active) { background-color: #F5F5F7; }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        /* --- TABLE STYLING --- */
        .table-card {
            background-color: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 25px;
            box-shadow: var(--shadow-soft);
            border: none;
        }

        .custom-table thead th {
            background-color: #F9FAFB;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 15px 20px;
            border-bottom: none;
        }

        .custom-table tbody td {
            padding: 18px 20px;
            vertical-align: middle;
            border-bottom: 1px solid #F2F2F7;
            font-size: 0.9rem;
        }

        .product-img-table {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            object-fit: cover;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-available { background-color: #DCFCE7; color: #15803D; }
        .status-empty { background-color: #FEE2E2; color: #B91C1C; }

        /* --- BUTTONS --- */
        .btn-add {
            background-color: var(--text-dark);
            color: #fff;
            border-radius: var(--radius-md);
            padding: 10px 20px;
            font-weight: 500;
            border: none;
            transition: var(--transition);
        }
        .btn-add:hover { background-color: var(--secondary); transform: translateY(-2px); color: #fff;}

        .btn-action {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: var(--transition);
            margin-right: 5px;
            text-decoration: none;
        }
        .btn-edit { background-color: #F2F2F7; color: var(--text-dark); }
        .btn-edit:hover { background-color: var(--primary); color: var(--text-dark);}
        .btn-delete { background-color: #FFF1F1; color: #DC3545; }
        .btn-delete:hover { background-color: #DC3545; color: #fff; }

        /* --- MODAL STYLING --- */
        .modal-content {
            border-radius: var(--radius-lg);
            border: none;
            padding: 15px;
        }
        .modal-header { border-bottom: none; padding-bottom: 0; }
        .modal-footer { border-top: none; }
        
        .form-label { font-weight: 500; font-size: 0.9rem; margin-bottom: 8px; }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #E5E7EB;
            font-size: 0.9rem;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(255, 214, 0, 0.2);
            border-color: var(--primary);
        }
        textarea.form-control { resize: none; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div>
            <div class="admin-profile">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200&auto=format&fit=crop" class="admin-avatar" alt="Admin">
                <div class="admin-info">
                    <h6 class="mb-0 fw-bold">Admin Panel</h6>
                    <small class="text-muted">Manajer Produk</small>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li class="mb-2"><a href="dashboard.html" class="menu-link"><i class="fas fa-th-large"></i> Ringkasan</a></li>
                <li class="mb-2"><a href="products.php" class="menu-link active"><i class="fas fa-box"></i> Produk</a></li>
                <li class="mb-2"><a href="#" class="menu-link"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li class="mb-2"><a href="#" class="menu-link"><i class="fas fa-users"></i> Pengguna</a></li>
            </ul>
        </div>
        <a href="../login.php" class="menu-link text-danger mt-auto"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <h2 class="fw-bold mb-1">Manajemen Produk</h2>
                <p class="text-muted small mb-0">Kelola katalog produk PisangKraf Anda di sini.</p>
            </div>
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i> Tambah Produk
            </button>
        </header>

        <div class="table-card">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $row): ?>
                                <?php 
                                    $status_badge = ($row['stock'] > 0) 
                                        ? '<span class="status-badge status-available">Tersedia</span>' 
                                        : '<span class="status-badge status-empty">Habis</span>';
                                    
                                    $image_path = '../assets/images/products/' . htmlspecialchars($row['image']);
                                ?>
                                <tr>
                                    <td><img src="<?= $image_path ?>" class="product-img-table" alt="Foto Produk" onerror="this.onerror=null; this.src='https://via.placeholder.com/45?text=No+Img'"></td>
                                    <td><span class="fw-semibold"><?= htmlspecialchars($row['name']) ?></span></td>
                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                    <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($row['stock']) ?></td>
                                    <td><?= $status_badge ?></td>
                                    <td>
                                        <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>"><i class="fas fa-pen"></i></button>
                                        <a href="proses_hapus.php?id=<?= $row['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini?');"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada produk yang ditambahkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fw-bold">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" name="name" placeholder="Masukkan nama produk" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Deskripsi singkat produk" required></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="category" required>
                                    <option value="" selected disabled>Pilih Kategori</option>
                                    <option value="Original">Original</option>
                                    <option value="Pedas">Pedas</option>
                                    <option value="Manis">Manis</option>
                                    <option value="Gurih">Gurih</option>
                                    <option value="Paket">Paket</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control" name="price" placeholder="25000" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Berat (Gram)</label>
                                <input type="number" class="form-control" name="weight" placeholder="250" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Stok Awal</label>
                                <input type="number" class="form-control" name="stock" placeholder="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Foto Produk</label>
                                <input type="file" class="form-control" name="image" accept="image/*" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning rounded-3 fw-semibold px-4">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php foreach ($products as $row): ?>
        <?php $image_path = '../assets/images/products/' . htmlspecialchars($row['image']); ?>
        <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="fw-bold">Edit Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="proses_edit.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="gambar_lama" value="<?= $row['image'] ?>">

                            <div class="mb-3 text-center">
                                <img src="<?= $image_path ?>" class="rounded-4 mb-2" style="width:100px; height:100px; object-fit:cover;" onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=No+Img'">
                                <p class="small text-muted">Foto saat ini</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="description" rows="3" required><?= htmlspecialchars($row['description']) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select class="form-select" name="category" required>
                                        <option value="Original" <?= ($row['category'] == 'Original') ? 'selected' : '' ?>>Original</option>
                                        <option value="Pedas" <?= ($row['category'] == 'Pedas') ? 'selected' : '' ?>>Pedas</option>
                                        <option value="Manis" <?= ($row['category'] == 'Manis') ? 'selected' : '' ?>>Manis</option>
                                        <option value="Gurih" <?= ($row['category'] == 'Gurih') ? 'selected' : '' ?>>Gurih</option>
                                        <option value="Paket" <?= ($row['category'] == 'Paket') ? 'selected' : '' ?>>Paket</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Harga (Rp)</label>
                                    <input type="number" class="form-control" name="price" value="<?= $row['price'] ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Berat (Gram)</label>
                                    <input type="number" class="form-control" name="weight" value="<?= $row['weight'] ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stok</label>
                                    <input type="number" class="form-control" name="stock" value="<?= $row['stock'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ganti Foto (Opsional)</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                    <small class="text-muted" style="font-size:0.75rem;">Biarkan kosong jika tidak ingin mengubah foto</small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-dark rounded-3 fw-semibold px-4">Update Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>