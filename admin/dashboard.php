<?php
session_start();
require_once '../config/koneksi.php';

// Existing PHP code remains the same until the HTML part
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// All existing queries remain the same
$total_produk = query("SELECT COUNT(*) as total FROM tb_barang")[0]['total'];
$total_penjualan = query("SELECT COUNT(*) as total FROM tb_penjualan")[0]['total'];
$total_user = query("SELECT COUNT(*) as total FROM tb_user")[0]['total'];
$pendapatan = query("SELECT SUM(total) as total FROM tb_penjualan")[0]['total'];
$produk_terlaris = query("SELECT b.nama_barang, SUM(dp.jumlah) as total_terjual 
                         FROM tb_detail_penjualan dp 
                         JOIN tb_barang b ON dp.barang_id = b.barang_id 
                         GROUP BY dp.barang_id 
                         ORDER BY total_terjual DESC 
                         LIMIT 5");
$penjualan_terbaru = query("SELECT p.*, u.nama as nama_user 
                           FROM tb_penjualan p 
                           JOIN tb_user u ON p.user_id = u.user_id 
                           ORDER BY p.tanggal DESC 
                           LIMIT 5");
$stok_menipis = query("SELECT b.*, k.nama_kategori 
                      FROM tb_barang b 
                      JOIN tb_kategori k ON b.kategori_id = k.kategori_id 
                      WHERE b.stok < 5");

include_once '../includes/header.php';
?>

<!-- Add custom CSS -->
<style>
    .dashboard-card {
        transition: transform 0.2s;
        border: none;
        border-radius: 10px;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    .card-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.2);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    .stat-card {
        padding: 1.5rem;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin: 0.5rem 0;
    }
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4 mb-0">Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active">Dashboard Overview</li>
                </ol>
            </nav>
        </div>
        <div class="text-end">
            <small class="text-muted">Last updated: <?= date('d M Y, H:i') ?></small>
        </div>
    </div>

    <!-- Cards -->
    <div class="row g-4">
        <!-- Total Produk -->
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card bg-primary text-white mb-4 h-100">
                <div class="card-body stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Produk</div>
                            <div class="stat-value"><?= $total_produk ?></div>
                        </div>
                        <div class="card-icon">
                            <i class="bi bi-box-seam fs-3"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-white" style="width: 70%"></div>
                    </div>
                </div>
                <div class="card-footer border-0 bg-primary-dark py-3">
                    <a class="small text-white text-decoration-none d-flex justify-content-between align-items-center" 
                       href="produk/index.php">
                        <span>Lihat Detail</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Penjualan -->
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card bg-success text-white mb-4 h-100">
                <div class="card-body stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Penjualan</div>
                            <div class="stat-value"><?= $total_penjualan ?></div>
                        </div>
                        <div class="card-icon">
                            <i class="bi bi-cart-check fs-3"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-white" style="width: 85%"></div>
                    </div>
                </div>
                <div class="card-footer border-0 bg-success-dark py-3">
                    <a class="small text-white text-decoration-none d-flex justify-content-between align-items-center" 
                       href="penjualan/index.php">
                        <span>Lihat Detail</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total User -->
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card bg-warning text-white mb-4 h-100">
                <div class="card-body stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total User</div>
                            <div class="stat-value"><?= $total_user ?></div>
                        </div>
                        <div class="card-icon">
                            <i class="bi bi-people fs-3"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-white" style="width: 60%"></div>
                    </div>
                </div>
                <div class="card-footer border-0 bg-warning-dark py-3">
                    <a class="small text-white text-decoration-none d-flex justify-content-between align-items-center" 
                       href="user/index.php">
                        <span>Lihat Detail</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card bg-danger text-white mb-4 h-100">
                <div class="card-body stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Pendapatan</div>
                            <div class="stat-value">Rp <?= number_format($pendapatan, 0, ',', '.') ?></div>
                        </div>
                        <div class="card-icon">
                            <i class="bi bi-cash-stack fs-3"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-white" style="width: 75%"></div>
                    </div>
                </div>
                <div class="card-footer border-0 bg-danger-dark py-3">
                    <a class="small text-white text-decoration-none d-flex justify-content-between align-items-center" 
                       href="laporan/index.php">
                        <span>Lihat Laporan</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4">
        <!-- Produk Terlaris -->
        <div class="col-xl-6">
            <div class="card dashboard-card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-bar-chart text-primary me-2"></i>
                            <span class="fw-bold">Produk Terlaris</span>
                        </div>
                        <button class="btn btn-sm btn-light" title="Refresh">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Total Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produk_terlaris as $produk) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded me-2">
                                                <i class="bi bi-box text-primary"></i>
                                            </div>
                                            <?= $produk['nama_barang'] ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">
                                            <?= $produk['total_terjual'] ?> unit
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="col-xl-6">
            <div class="card dashboard-card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                            <span class="fw-bold">Stok Menipis</span>
                        </div>
                        <button class="btn btn-sm btn-light" title="Refresh">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th class="text-end">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stok_menipis as $stok) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded me-2">
                                                <i class="bi bi-box text-warning"></i>
                                            </div>
                                            <?= $stok['nama_barang'] ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <?= $stok['nama_kategori'] ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-danger">
                                            <?= $stok['stok'] ?> unit
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Penjualan Terbaru -->
    <div class="card dashboard-card shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-clock-history text-success me-2"></i>
                    <span class="fw-bold">Penjualan Terbaru</span>
                </div>
                <button class="btn btn-sm btn-light" title="Refresh">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($penjualan_terbaru as $penjualan) : ?>
                        <tr>
                            <td>
                                <span class="fw-bold">#<?= $penjualan['penjualan_id'] ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar3 text-muted me-2"></i>
                                    <?= date('d/m/Y H:i', strtotime($penjualan['tanggal'])) ?>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light p-2 rounded me-2">
                                        <i class="bi bi-person text-primary"></i></div>
                                    <?= $penjualan['nama_user'] ?>
                                </div>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold">
                                    Rp <?= number_format($penjualan['total'], 0, ',', '.') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Selesai
                                </span>
                            </td>
                            <td>
                                <a href="penjualan/detail.php?id=<?= $penjualan['penjualan_id'] ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    Menampilkan 5 penjualan terbaru
                </span>
                <a href="penjualan/index.php" class="btn btn-sm btn-primary">
                    Lihat Semua Penjualan
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Add smooth animation for cards
document.addEventListener('DOMContentLoaded', function() {
    // Animate numbers in stat cards
    const animateValue = (element, start, end, duration) => {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            element.innerHTML = value.toLocaleString();
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };

    // Apply animation to stat values
    document.querySelectorAll('.stat-value').forEach(element => {
        const value = parseInt(element.innerText.replace(/[^0-9]/g, ''));
        if (!isNaN(value)) {
            element.innerText = '0';
            animateValue(element, 0, value, 1000);
        }
    });

    // Add hover effect to tables
    document.querySelectorAll('.table-hover tr').forEach(row => {
        row.addEventListener('mouseover', function() {
            this.style.transition = 'background-color 0.3s';
        });
    });
});
</script>

<?php include_once '../includes/footer.php'; ?>