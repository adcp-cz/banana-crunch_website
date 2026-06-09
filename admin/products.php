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

        /* --- SIDEBAR (Consistent with Dashboard) --- */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background-color: var(--bg-sidebar);
            border-right: 1px solid #EFEFEF;
            padding: 30px 24px;
            z-index: 100;
        }
        .admin-profile { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; }
        .admin-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); }
        
        .sidebar-menu { list-style: none; padding: 0; }
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
        .btn-add:hover { background-color: var(--secondary); transform: translateY(-2px); }

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
        }
        .btn-edit { background-color: #F2F2F7; color: var(--text-dark); }
        .btn-edit:hover { background-color: var(--primary); }
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
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(255, 214, 0, 0.2);
            border-color: var(--primary);
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div>
            <div class="admin-profile">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200&auto=format&fit=crop" class="admin-avatar">
                <div class="admin-info">
                    <h6 class="mb-0 fw-bold">Admin Panel</h6>
                    <small class="text-muted">Manajer Produk</small>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li class="mb-2"><a href="dashboard.html" class="menu-link"><i class="fas fa-th-large"></i> Ringkasan</a></li>
                <li class="mb-2"><a href="#" class="menu-link active"><i class="fas fa-box"></i> Produk</a></li>
                <li class="mb-2"><a href="#" class="menu-link"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li class="mb-2"><a href="#" class="menu-link"><i class="fas fa-users"></i> Pengguna</a></li>
            </ul>
        </div>
        <a href="../login.html" class="menu-link text-danger mt-auto"><i class="fas fa-sign-out-alt"></i> Keluar</a>
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
                        <tr>
                            <td><img src="https://images.unsplash.com/photo-1621306354894-39908c6fa2cb?q=80&w=200" class="product-img-table"></td>
                            <td><span class="fw-semibold">Pisang Goreng Madu</span></td>
                            <td>Kue Basah</td>
                            <td>Rp 25.000</td>
                            <td>42</td>
                            <td><span class="status-badge status-available">Tersedia</span></td>
                            <td>
                                <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit"><i class="fas fa-pen"></i></button>
                                <button class="btn-action btn-delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td><img src="https://images.unsplash.com/photo-1598215439218-f79af39da1ce?q=80&w=200" class="product-img-table"></td>
                            <td><span class="fw-semibold">Keripik Pisang Ori</span></td>
                            <td>Cemilan</td>
                            <td>Rp 15.000</td>
                            <td>120</td>
                            <td><span class="status-badge status-available">Tersedia</span></td>
                            <td>
                                <button class="btn-action btn-edit"><i class="fas fa-pen"></i></button>
                                <button class="btn-action btn-delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td><img src="https://images.unsplash.com/photo-1614145121029-83a9f7b68bf4?q=80&w=200" class="product-img-table"></td>
                            <td><span class="fw-semibold">Bolu Pisang</span></td>
                            <td>Kue</td>
                            <td>Rp 45.000</td>
                            <td>0</td>
                            <td><span class="status-badge status-empty">Habis</span></td>
                            <td>
                                <button class="btn-action btn-edit"><i class="fas fa-pen"></i></button>
                                <button class="btn-action btn-delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fw-bold">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" placeholder="Masukkan nama produk">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <select class="form-select">
                                    <option selected>Pilih Kategori</option>
                                    <option>Cemilan</option>
                                    <option>Kue Basah</option>
                                    <option>Olahan Tradisional</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control" placeholder="25000">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok Awal</label>
                            <input type="number" class="form-control" placeholder="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Produk</label>
                            <input type="file" class="form-control">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning rounded-3 fw-semibold px-4">Simpan Produk</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fw-bold">Edit Informasi Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3 text-center">
                            <img src="https://images.unsplash.com/photo-1621306354894-39908c6fa2cb?q=80&w=200" class="rounded-4 mb-2" style="width:100px; height:100px; object-fit:cover;">
                            <p class="small text-muted">Foto saat ini</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" value="Pisang Goreng Madu">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga</label>
                                <input type="number" class="form-control" value="25000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" class="form-control" value="42">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-dark rounded-3 fw-semibold px-4">Update Produk</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>