<?php
session_start();
require_once '../../config/koneksi.php';

// Existing PHP code remains unchanged
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$query = "SELECT b.*, k.nama_kategori, m.nama_merk 
          FROM tb_barang b 
          LEFT JOIN tb_kategori k ON b.kategori_id = k.kategori_id 
          LEFT JOIN tb_merk m ON b.merk_id = m.merk_id
          ORDER BY b.barang_id DESC";
$barang = query($query);

include_once '../includes/header.php';
?>

<!-- Custom CSS -->
<style>
    .page-header {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 2rem;
        border-radius: 10px;
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
    }

    .table td {
        vertical-align: middle;
        padding: 1rem;
    }

    .img-thumbnail {
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .img-thumbnail:hover {
        transform: scale(1.05);
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
        padding: 0.25rem 0.5rem;
    }

    .btn-primary {
        background: #3498db;
        border: none;
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

    .modal-content {
        border: none;
        border-radius: 12px;
    }

    .modal-header {
        background: #f8f9fa;
        border-bottom: 2px solid #eee;
    }

    /* DataTables customization */
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
        <h1 class="mb-2">Data Laptop</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Laptop</li>
            </ol>
        </nav>
    </div>

    <!-- Tombol Tambah & Pesan -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Tambah Laptop
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
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Tabel Barang -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-laptop text-primary me-2"></i>
                    <span class="fw-bold">Data Laptop</span>
                </div>
                <button class="btn btn-sm btn-light" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise"></i>
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
                            <th>Gambar</th>
                            <th>Nama Laptop</th>
                            <th>Merk</th>
                            <th>Kategori</th>
                            <th>Spesifikasi</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($barang as $row) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <?php if ($row['gambar'] && file_exists("../../assets/img/barang/" . $row['gambar'])) : ?>
                                    <div class="position-relative">
                                        <img src="../../assets/img/barang/<?= $row['gambar']; ?>" 
                                             alt="<?= htmlspecialchars($row['nama_barang']); ?>" 
                                             class="img-thumbnail"
                                             style="max-width: 100px; max-height: 100px; object-fit: cover;"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#imageModal<?= $row['barang_id']; ?>">
                                        <div class="position-absolute top-0 end-0">
                                            <span class="badge bg-info">
                                                <i class="bi bi-zoom-in"></i>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Modal Preview Gambar -->
                                    <div class="modal fade" id="imageModal<?= $row['barang_id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="bi bi-laptop me-2"></i>
                                                        <?= htmlspecialchars($row['nama_barang']); ?>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center p-4">
                                                    <img src="../../assets/img/barang/<?= $row['gambar']; ?>" 
                                                         alt="<?= htmlspecialchars($row['nama_barang']); ?>" 
                                                         class="img-fluid rounded">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <img src="../../assets/img/no-image.jpg" 
                                         alt="No Image" 
                                         class="img-thumbnail"
                                         style="max-width: 100px;">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($row['nama_barang']); ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <?= htmlspecialchars($row['nama_merk']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info text-white">
                                    <?= htmlspecialchars($row['nama_kategori']); ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= htmlspecialchars($row['jenis_barang']); ?>
                                </small>
                            </td>
                            <td>
                                <div class="text-muted">
                                    Rp <?= number_format($row['harga_beli'], 0, ',', '.'); ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-success">
                                    Rp <?= number_format($row['harga_jual'], 0, ',', '.'); ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if ($row['stok'] <= 5) : ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <?= $row['stok']; ?>
                                    </span>
                                <?php else : ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        <?= $row['stok']; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="edit.php?id=<?= $row['barang_id']; ?>" 
                                       class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="hapus.php?id=<?= $row['barang_id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus laptop <?= htmlspecialchars($row['nama_barang']); ?>?')">
                                        <i class="bi bi-trash"></i> Hapus
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

<!-- DataTables Script with enhanced configuration -->
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        },
        pageLength: 10,
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
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [1, 9] }
        ]
    });
});

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
</script>

<?php include_once '../includes/footer.php'; ?>