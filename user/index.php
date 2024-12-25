<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Query utama untuk laptop dengan filter
$query = "SELECT b.*, k.nama_kategori, m.nama_merk 
          FROM tb_barang b 
          LEFT JOIN tb_kategori k ON b.kategori_id = k.kategori_id 
          LEFT JOIN tb_merk m ON b.merk_id = m.merk_id 
          WHERE b.stok > 0";

// Filter kategori
if (isset($_GET['kategori']) && $_GET['kategori'] != '') {
    $kategori_id = $_GET['kategori'];
    $query .= " AND b.kategori_id = $kategori_id";
}

// Filter merk
if (isset($_GET['merk']) && $_GET['merk'] != '') {
    $merk_id = $_GET['merk'];
    $query .= " AND b.merk_id = $merk_id";
}

// Eksekusi query
$laptops = query($query);
$categories = query("SELECT * FROM tb_kategori");
$brands = query("SELECT * FROM tb_merk");
$user_id = $_SESSION['user_id'];
$user = query("SELECT * FROM tb_user WHERE user_id = $user_id")[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unesa Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --hover-color: #0a58ca;
        }

        body {
            background-color: #f8f9fa;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background: linear-gradient(45deg, #0d6efd, #0dcaf0) !important;
        }

        .navbar-brand {
            font-weight: 600;
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border: none;
            padding: 15px;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .product-card {
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
        }

        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #0dcaf0);
            border: none;
            border-radius: 10px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13,110,253,0.2);
        }

        .badge {
            font-weight: 500;
            padding: 8px 12px;
        }

        .input-group-text {
            border: 2px solid #e9ecef;
            background: #f8f9fa;
            border-radius: 10px 0 0 10px;
        }

        .input-group .form-control {
            border: 2px solid #e9ecef;
            border-radius: 0 10px 10px 0;
        }

        .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(45deg, #0d6efd, #0dcaf0);
            color: white;
            border: none;
            padding: 15px 20px;
        }

        .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-body img {
            border-radius: 15px;
            max-height: 80vh;
            object-fit: contain;
        }

        .dropdown-menu {
            border-radius: 12px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* Styles untuk description container */
        .description-container {
            position: relative;
            margin-bottom: 1rem;
        }

        .description-text {
            transition: max-height 0.3s ease-out;
            overflow: hidden;
            line-height: 1.5;
        }

        .description-text.collapsed {
            max-height: 4.5em; /* Sekitar 3 baris */
        }

        .description-text.expanded {
            max-height: none;
        }

        .btn-link {
            color: var(--primary-color);
            padding: 0;
            font-size: 0.9rem;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            display: inline-block;
            margin-top: 5px;
        }

        .btn-link:hover {
            color: var(--hover-color);
            text-decoration: underline;
        }

        .card-body {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-laptop me-2"></i>Unesa Laptop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Pesanan Saya</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item me-3">
                        <a class="nav-link" href="cart.php">
                            <i class="bi bi-cart-fill me-1"></i>Keranjang
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) : ?>
                                <span class="badge bg-danger rounded-pill"><?= count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($user['nama']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-4">
        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="get" class="row g-3">
                            <div class="col-md-5">
                                <select name="kategori" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($categories as $category) : ?>
                                        <option value="<?= $category['kategori_id']; ?>" 
                                                <?= (isset($_GET['kategori']) && $_GET['kategori'] == $category['kategori_id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($category['nama_kategori']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <select name="merk" class="form-select">
                                    <option value="">Semua Merk</option>
                                    <?php foreach ($brands as $brand) : ?>
                                        <option value="<?= $brand['merk_id']; ?>"
                                                <?= (isset($_GET['merk']) && $_GET['merk'] == $brand['merk_id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($brand['nama_merk']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel me-2"></i>Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<!-- Products Grid -->
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($laptops as $laptop) : ?>
            <div class="col">
                <div class="card h-100 product-card">
                    <div class="position-relative">
                        <img src="../assets/img/barang/<?= htmlspecialchars($laptop['gambar'] ?: 'no-image.jpg'); ?>" 
                             class="product-image" 
                             alt="<?= htmlspecialchars($laptop['nama_barang']); ?>" 
                             onclick="showImageModal('<?= htmlspecialchars($laptop['nama_barang'], ENT_QUOTES); ?>', '../assets/img/barang/<?= htmlspecialchars($laptop['gambar'] ?: 'no-image.jpg'); ?>')">
                        <?php if ($laptop['stok'] <= 5) : ?>
                            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-3">
                                Stok Terbatas: <?= $laptop['stok']; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-truncate"><?= htmlspecialchars($laptop['nama_barang']); ?></h5>
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="bi bi-tag me-1"></i><?= htmlspecialchars($laptop['nama_merk']); ?> | 
                                <i class="bi bi-laptop me-1"></i><?= htmlspecialchars($laptop['nama_kategori']); ?>
                            </small>
                        </div>
                        <div class="description-container">
                            <div class="description-text collapsed" id="desc-<?= $laptop['barang_id']; ?>">
                                <?= htmlspecialchars($laptop['jenis_barang']); ?>
                            </div>
                            <?php if (strlen($laptop['jenis_barang']) > 70) : ?>
                                <button type="button" 
                                        class="btn btn-link p-0 toggle-description" 
                                        data-id="<?= $laptop['barang_id']; ?>"
                                        data-expanded="false">
                                    Selengkapnya
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="mt-auto">
                            <h6 class="fw-bold text-primary mb-3">
                                Rp <?= number_format($laptop['harga_jual'], 0, ',', '.'); ?>
                            </h6>
                            <?php if ($laptop['stok'] > 0) : ?>
                                <form action="cart.php" method="post" class="cart-form">
                                    <input type="hidden" name="barang_id" value="<?= $laptop['barang_id']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Jumlah</span>
                                        <input type="number" name="qty" value="1" min="1" 
                                               max="<?= $laptop['stok']; ?>" class="form-control"
                                               required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-cart-plus me-2"></i>Tambah ke Keranjang
                                    </button>
                                </form>
                            <?php else : ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="bi bi-x-circle me-2"></i>Stok Habis
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

<!-- Modal Image -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imageModalImg" src="" class="img-fluid rounded" alt="">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function untuk menampilkan modal gambar
        function showImageModal(title, src) {
            const modalLabel = document.getElementById('imageModalLabel');
            const modalImg = document.getElementById('imageModalImg');
            
            modalLabel.textContent = title;
            modalImg.src = src;
            modalImg.alt = title;
            
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        }

        // Event listener untuk toggle description
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-description').forEach(button => {
                button.addEventListener('click', function() {
                    const descId = this.getAttribute('data-id');
                    const descElement = document.getElementById('desc-' + descId);
                    const isExpanded = this.getAttribute('data-expanded') === 'true';
                    
                    if (!isExpanded) {
                        // Expand
                        descElement.classList.remove('collapsed');
                        descElement.classList.add('expanded');
                        this.textContent = 'Lihat lebih sedikit';
                        this.setAttribute('data-expanded', 'true');
                    } else {
                        // Collapse
                        descElement.classList.remove('expanded');
                        descElement.classList.add('collapsed');
                        this.textContent = 'Selengkapnya';
                        this.setAttribute('data-expanded', 'false');
                    }
                });
            });
        });

        // Event listener untuk validasi form
        document.querySelectorAll('.cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const qtyInput = this.querySelector('input[name="qty"]');
                const qty = parseInt(qtyInput.value);
                const max = parseInt(qtyInput.getAttribute('max'));

                if (qty < 1) {
                    e.preventDefault();
                    alert('Jumlah minimal pembelian adalah 1');
                    qtyInput.value = 1;
                } else if (qty > max) {
                    e.preventDefault();
                    alert(`Jumlah maksimal pembelian adalah ${max}`);
                    qtyInput.value = max;
                }
            });
        });
    </script>
</body>
</html>

