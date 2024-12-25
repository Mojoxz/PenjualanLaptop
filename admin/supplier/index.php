<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Query untuk mendapatkan data supplier dengan detail barang
$supplier = query("SELECT s.*, b.nama_barang, b.barang_id, k.nama_kategori, m.nama_merk, COUNT(b.barang_id) as jumlah_produk 
                  FROM tb_supplier s 
                  LEFT JOIN tb_barang b ON s.barang_id = b.barang_id 
                  LEFT JOIN tb_kategori k ON b.kategori_id = k.kategori_id
                  LEFT JOIN tb_merk m ON b.merk_id = m.merk_id
                  GROUP BY s.supplier_id 
                  ORDER BY s.supplier_id DESC");

include_once '../includes/header.php';
?>

<style>
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
        transition: all 0.3s;
    }

    .breadcrumb-item a:hover {
        color: white;
    }

    .breadcrumb-item.active {
        color: white;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .card-header {
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
        padding: 1rem;
    }

    .table td {
        vertical-align: middle;
        padding: 1rem;
    }

    .badge {
        padding: 0.5em 1em;
        font-weight: 500;
        border-radius: 6px;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
    }

    .btn-primary {
        background: #3498db;
        border: none;
        box-shadow: 0 2px 5px rgba(52, 152, 219, 0.2);
    }

    .btn-primary:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }

    .btn-warning {
        background: #f1c40f;
        border: none;
        color: #2c3e50;
    }

    .btn-warning:hover {
        background: #f39c12;
        color: white;
    }

    .btn-danger {
        background: #e74c3c;
        border: none;
    }

    .btn-danger:hover {
        background: #c0392b;
    }

    .alert {
        border: none;
        border-radius: 10px;
        padding: 1rem 1.5rem;
    }

    .address-cell {
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .address-cell:hover {
        white-space: normal;
        overflow: visible;
        background: #f8f9fa;
        position: relative;
        z-index: 1;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .product-info {
        max-width: 250px;
    }

    .product-info .fw-medium {
        color: #2c3e50;
        font-size: 0.95rem;
    }

    .product-info small {
        font-size: 0.85rem;
    }

    .badge.bg-warning.text-dark {
        background-color: #fef3c7 !important;
        color: #92400e !important;
        font-weight: 500;
        padding: 0.5em 1em;
    }

    .supplier-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #e3f2fd;
    }

    .product-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .refresh-btn {
        transition: transform 0.3s ease;
    }

    .refresh-btn:hover {
        transform: rotate(180deg);
    }
</style>

<div class="container-fluid px-4">
    <div class="page-header">
        <h1 class="mb-2">Data Supplier</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Supplier</li>
            </ol>
        </nav>
    </div>

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Tambah Supplier
        </a>
        <div class="d-flex align-items-center">
            <i class="bi bi-clock-history text-muted me-2"></i>
            <span class="text-muted">Last updated: <?= date('d M Y H:i') ?></span>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-truck text-primary me-2"></i>
                    <span class="fw-bold">Data Supplier</span>
                </div>
                <button class="btn btn-sm btn-light refresh-btn" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="80">No</th>
                            <th>Nama Supplier</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Barang di-Supply</th>
                            <th>Status</th>
                            <th width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($supplier as $row) : ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="supplier-icon me-2">
                                        <i class="bi bi-building text-primary"></i>
                                    </div>
                                    <span class="fw-medium"><?= htmlspecialchars($row['nama']); ?></span>
                                </div>
                            </td>
                            <td class="address-cell">
                                <i class="bi bi-geo-alt text-muted me-1"></i>
                                <?= htmlspecialchars($row['alamat']); ?>
                            </td>
                            <td>
                                <i class="bi bi-telephone text-muted me-1"></i>
                                <?= htmlspecialchars($row['telepon']); ?>
                            </td>
                            <td>
                                <?php if ($row['nama_barang']): ?>
                                    <div class="product-info">
                                        <div class="fw-medium"><?= htmlspecialchars($row['nama_barang']); ?></div>
                                        <small class="text-muted">
                                            <i class="bi bi-tag me-1"></i>
                                            <?= htmlspecialchars($row['nama_merk']); ?> - 
                                            <?= htmlspecialchars($row['nama_kategori']); ?>
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        Belum ada barang
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    <i class="bi bi-box me-1"></i>
                                    <?= $row['jumlah_produk']; ?> Produk
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="edit.php?id=<?= $row['supplier_id']; ?>" 
                                       class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>
                                    <a href="#" 
                                       onclick="confirmDelete('hapus.php?id=<?= $row['supplier_id']; ?>', '<?= htmlspecialchars($row['nama']); ?>')" 
                                       class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash me-1"></i> Hapus
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
        ]
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

function confirmDelete(url, nama) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus supplier "${nama}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#95a5a6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>

<?php include_once '../includes/footer.php'; ?>