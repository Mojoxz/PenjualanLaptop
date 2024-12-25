<?php
session_start();
require_once '../../config/koneksi.php';

// Existing PHP code remains unchanged
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// All existing query logic remains the same
$where = "";
$where_date = "";
if (isset($_GET['dari']) && isset($_GET['sampai'])) {
    $dari = $_GET['dari'];
    $sampai = $_GET['sampai'];
    if (!empty($dari) && !empty($sampai)) {
        $where = "WHERE DATE(p.tanggal) BETWEEN '$dari' AND '$sampai'";
        $where_date = "WHERE DATE(tanggal) BETWEEN '$dari' AND '$sampai'";
    }
}

// All other queries remain unchanged
$query = "SELECT p.*, a.nama as admin_name, u.nama as nama_user, u.telepon, pb.jenis_pembayaran,
          (SELECT SUM(dp.subtotal) FROM tb_detail_penjualan dp WHERE dp.penjualan_id = p.penjualan_id) as total_penjualan 
          FROM tb_penjualan p 
          LEFT JOIN tb_admin a ON p.admin_id = a.admin_id
          LEFT JOIN tb_pembelian pmb ON p.penjualan_id = pmb.id_pembelian
          LEFT JOIN tb_user u ON pmb.user_id = u.user_id
          LEFT JOIN tb_pembayaran pb ON pmb.pembayaran_id = pb.pembayaran_id
          $where
          ORDER BY p.tanggal DESC";
$penjualan = query($query);

// Keep other queries
$query_produk = "SELECT COALESCE(SUM(dp.jumlah), 0) as total 
                 FROM tb_detail_penjualan dp 
                 JOIN tb_penjualan p ON dp.penjualan_id = p.penjualan_id 
                 " . str_replace('p.tanggal', 'p.tanggal', $where);
$total_produk = query($query_produk)[0]['total'];

$query_customer = "SELECT COUNT(DISTINCT pmb.user_id) as total 
                  FROM tb_pembelian pmb 
                  JOIN tb_penjualan p ON pmb.id_pembelian = p.penjualan_id 
                  " . str_replace('p.tanggal', 'p.tanggal', $where);
$total_customer = query($query_customer)[0]['total'];

include_once '../includes/header.php';
?>

<style>
    /* Modern Dashboard Styles */
    .page-header {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }

    .breadcrumb-item a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: white;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .filter-card {
        background: white;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        padding: 1.5rem;
    }

    .summary-card .bi {
        opacity: 0.8;
    }

    .table-card {
        overflow: hidden;
    }

    .table-card .card-header {
        background: white;
        border-bottom: 2px solid #f8f9fa;
        padding: 1rem 1.5rem;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background: #2c3e50;
        color: white;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table td {
        vertical-align: middle;
        padding: 1rem;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #3498db;
        border: none;
    }

    .btn-primary:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #95a5a6;
        border: none;
    }

    .btn-success {
        background: #2ecc71;
        border: none;
    }

    .btn-info {
        background: #3498db;
        border: none;
        color: white;
    }

    .form-control {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        border: 1px solid #dee2e6;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .card-stats {
        padding: 1rem;
        text-align: center;
        color: white;
    }

    .stats-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .stats-value {
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    /* DataTables Custom Styling */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 0.375rem 0.75rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px;
        margin: 0 2px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3498db;
        border-color: #3498db;
        color: white !important;
    }
</style>

<div class="container-fluid px-4">
    <div class="page-header">
        <h1 class="mb-2">Data Penjualan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Penjualan</li>
            </ol>
        </nav>
    </div>

    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Filter Card -->
    <div class="card filter-card mb-4">
        <form action="" method="get" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-calendar-event me-1"></i>
                    Dari Tanggal
                </label>
                <input type="date" class="form-control" name="dari" 
                       value="<?= isset($_GET['dari']) ? $_GET['dari'] : ''; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-calendar-event me-1"></i>
                    Sampai Tanggal
                </label>
                <input type="date" class="form-control" name="sampai" 
                       value="<?= isset($_GET['sampai']) ? $_GET['sampai'] : ''; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <?php if (isset($_GET['dari']) || isset($_GET['sampai'])) : ?>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </a>
                        <button type="button" class="btn btn-success" onclick="exportExcel()">
                            <i class="bi bi-file-excel me-1"></i> Export
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Transaksi -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary summary-card">
                <div class="card-stats">
                    <div class="stats-icon">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="stats-value"><?= count($penjualan); ?></div>
                    <div class="stats-label">Total Transaksi</div>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success summary-card">
                <div class="card-stats">
                    <div class="stats-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stats-value">
                        Rp <?= number_format(array_sum(array_column($penjualan, 'total')), 0, ',', '.'); ?>
                    </div>
                    <div class="stats-label">Total Pendapatan</div>
                </div>
            </div>
        </div>

        <!-- Total Produk -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning summary-card">
                <div class="card-stats">
                    <div class="stats-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="stats-value"><?= $total_produk; ?></div>
                    <div class="stats-label">Total Produk Terjual</div>
                </div>
            </div>
        </div>

        <!-- Total Customer -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger summary-card">
                <div class="card-stats">
                    <div class="stats-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stats-value"><?= $total_customer; ?></div>
                    <div class="stats-label">Total Customer</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card table-card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-table text-primary me-2"></i>
                    <span class="fw-bold">Data Penjualan</span>
                </div>
                <button class="btn btn-sm btn-light" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Refresh
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pembeli</th>
                            <th>Telepon</th>
                            <th>Jenis Pembayaran</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Kembalian</th>
                            <th>Admin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($penjualan as $row) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <i class="bi bi-calendar2 text-muted me-1"></i>
                                <?= date('d/m/Y H:i', strtotime($row['tanggal'])); ?>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($row['nama_user'] ?? 'User tidak ditemukan'); ?></div>
                            </td>
                            <td>
                                <i class="bi bi-telephone text-muted me-1"></i>
                                <?= htmlspecialchars($row['telepon'] ?? '-'); ?>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?= htmlspecialchars($row['jenis_pembayaran'] ?? '-'); ?>
                                </span>
                            </td>
                            <td class="fw-bold text-success">
                                Rp <?= number_format($row['total'], 0, ',', '.'); ?>
                            </td>
                            <td>
                                Rp <?= number_format($row['bayar'], 0, ',', '.'); ?>
                            </td>
                            <td>
                                Rp <?= number_format($row['kembalian'], 0, ',', '.'); ?>
                            </td>
                            <td>
                                <i class="bi bi-person text-muted me-1"></i>
                                <?= htmlspecialchars($row['admin_name']); ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="detail.php?id=<?= $row['penjualan_id']; ?>" 
                                       class="btn btn-info btn-sm" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="cetak.php?id=<?= $row['penjualan_id']; ?>" 
                                       target="_blank" 
                                       class="btn btn-secondary btn-sm" 
                                       title="Cetak">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="bi bi-file-earmark-excel me-1"></i>Export Excel',
                className: 'btn btn-success btn-sm me-2'
            },
            {
                extend: 'pdf',
                text: '<i class="bi bi-file-earmark-pdf me-1"></i>Export PDF',
                className: 'btn btn-danger btn-sm'
            }
        ],
        "order": [[1, "desc"]],
        "pageLength": 25,
        "columnDefs": [
            { "width": "5%", "targets": 0 },
            { "width": "15%", "targets": 1 },
            { "width": "10%", "targets": [3, 4, 8] },
            { "width": "12%", "targets": [5, 6, 7] },
            { "width": "8%", "targets": 9 },
            { "orderable": false, "targets": 9 }
        ]
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add animation to statistics cards
    function animateValue(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerHTML = Math.floor(progress * (end - start) + start).toLocaleString();
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Animate statistics on page load
    document.querySelectorAll('.stats-value').forEach(element => {
        const value = parseInt(element.innerText.replace(/\D/g,''));
        if (!isNaN(value)) {
            element.innerText = '0';
            animateValue(element, 0, value, 1000);
        }
    });
});

function exportExcel() {
    let params = new URLSearchParams(window.location.search);
    let dari = params.get('dari') || '';
    let sampai = params.get('sampai') || '';
    window.location.href = `export.php?dari=${dari}&sampai=${sampai}`;
}

// Add print functionality
function printInvoice(id) {
    window.open(`cetak.php?id=${id}`, '_blank', 'width=800,height=600');
}
</script>

<?php include_once '../includes/footer.php'; ?>