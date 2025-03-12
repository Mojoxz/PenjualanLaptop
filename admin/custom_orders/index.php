<?php
session_start();
require_once '../../config/koneksi.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek login
if (!isset($_SESSION['login']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'superadmin')) {
    header("Location: ../../auth/adminlogin.php");
    exit;
}

// Filter status jika ada
$status_filter = '';
if (isset($_GET['status']) && in_array($_GET['status'], ['pending', 'processing', 'completed', 'cancelled'])) {
    $status_filter = "WHERE co.status = '{$_GET['status']}'";
} else {
    $status_filter = "";
}

// Query untuk mengambil semua custom order
$query = "SELECT co.*, u.nama as user_name, u.telepon as user_phone 
          FROM tb_custom_orders co 
          JOIN tb_user u ON co.user_id = u.user_id 
          $status_filter
          ORDER BY co.created_at DESC";
$custom_orders_result = mysqli_query($conn, $query);
$custom_orders = [];

if ($custom_orders_result && mysqli_num_rows($custom_orders_result) > 0) {
    while ($row = mysqli_fetch_assoc($custom_orders_result)) {
        $custom_orders[] = $row;
    }
}

// Hitung jumlah order berdasarkan status
$count_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_custom_orders WHERE status = 'pending'"))['total'];
$count_processing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_custom_orders WHERE status = 'processing'"))['total'];
$count_completed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_custom_orders WHERE status = 'completed'"))['total'];
$count_cancelled = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_custom_orders WHERE status = 'cancelled'"))['total'];

// Status label mapping
$status_labels = [
    'pending' => [
        'label' => 'Menunggu',
        'class' => 'bg-warning text-dark',
        'icon' => 'clock'
    ],
    'processing' => [
        'label' => 'Diproses',
        'class' => 'bg-info text-white',
        'icon' => 'arrow-repeat'
    ],
    'completed' => [
        'label' => 'Selesai',
        'class' => 'bg-success text-white',
        'icon' => 'check-circle'
    ],
    'cancelled' => [
        'label' => 'Dibatalkan',
        'class' => 'bg-danger text-white',
        'icon' => 'x-circle'
    ]
];

include_once '../includes/header.php';
?>

<style>
:root {
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    --secondary-color: #475569;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #3b82f6;
    --border-radius: 0.75rem;
    --box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
    --transition: all 0.3s ease;
}

/* Header styling */
.page-header {
    background: linear-gradient(135deg, var(--primary-color), #3b82f6);
    color: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    box-shadow: var(--box-shadow);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    bottom: -50%;
    left: -50%;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
    transform: rotate(45deg);
    z-index: 0;
}

.page-header * {
    position: relative;
    z-index: 1;
}

.breadcrumb-item a {
    color: rgba(255,255,255,0.9);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
}

.breadcrumb-item a:hover {
    color: white;
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: white;
    font-weight: 400;
}

/* Button styling */
.btn {
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.875rem;
}

.btn-primary {
    background: var(--primary-color);
    border: none;
    color: white;
}

.btn-primary:hover, .btn-primary:focus {
    background: var(--primary-hover);
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.5), 0 2px 4px -2px rgba(37, 99, 235, 0.5);
    transform: translateY(-2px);
}

.btn-light {
    background: white;
    border: 1px solid #e2e8f0;
    color: var(--secondary-color);
}

.btn-light:hover, .btn-light:focus {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1e293b;
}

.btn-success {
    background: var(--success-color);
    border: none;
    color: white;
}

.btn-success:hover, .btn-success:focus {
    background: #059669;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.5), 0 2px 4px -2px rgba(16, 185, 129, 0.5);
    transform: translateY(-2px);
}

.btn-info {
    background: var(--info-color);
    border: none;
    color: white;
}

.btn-info:hover, .btn-info:focus {
    background: #2563eb;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.5), 0 2px 4px -2px rgba(59, 130, 246, 0.5);
    transform: translateY(-2px);
}

.btn-danger {
    background: var(--danger-color);
    border: none;
    color: white;
}

.btn-danger:hover, .btn-danger:focus {
    background: #dc2626;
    box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.5), 0 2px 4px -2px rgba(239, 68, 68, 0.5);
    transform: translateY(-2px);
}

/* Alert styles */
.alert {
    border: none;
    border-radius: var(--border-radius);
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background-color: #ecfdf5;
    color: #065f46;
}

.alert-danger {
    background-color: #fef2f2;
    color: #991b1b;
}

/* Card styling */
.card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.card:hover {
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.card-header {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    padding: 1.25rem 1.5rem;
}

.card-body {
    padding: 0;
}

/* Stats cards */
.stats-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    background: white;
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.stats-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
}

.stats-card.pending { border-left: 5px solid var(--warning-color); }
.stats-card.processing { border-left: 5px solid var(--info-color); }
.stats-card.completed { border-left: 5px solid var(--success-color); }
.stats-card.cancelled { border-left: 5px solid var(--danger-color); }

.stats-card .card-body {
    padding: 1.75rem;
}

.stats-card .stats-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    transition: var(--transition);
    font-size: 1.5rem;
}

.stats-card.pending .stats-icon { background: rgba(245, 158, 11, 0.1); color: var(--warning-color); }
.stats-card.processing .stats-icon { background: rgba(59, 130, 246, 0.1); color: var(--info-color); }
.stats-card.completed .stats-icon { background: rgba(16, 185, 129, 0.1); color: var(--success-color); }
.stats-card.cancelled .stats-icon { background: rgba(239, 68, 68, 0.1); color: var(--danger-color); }

.stats-card:hover .stats-icon {
    transform: scale(1.1);
}

.stats-card .stats-value {
    font-size: 2rem;
    font-weight: 700;
    margin: 0.6rem 0;
    color: #1e293b;
    letter-spacing: -0.5px;
}

.stats-card .stats-label {
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Filter buttons */
.filter-container {
    margin-bottom: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.filter-btn {
    padding: 0.75rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    border: 1px solid #e2e8f0;
}

.filter-btn i {
    font-size: 1.1rem;
}

.filter-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.filter-btn-all {
    background-color: white;
    color: var(--secondary-color);
}

.filter-btn-all.active {
    background-color: #1e293b;
    color: white;
    border-color: #1e293b;
}

.filter-btn-pending {
    background-color: rgba(245, 158, 11, 0.1);
    color: var(--warning-color);
    border-color: rgba(245, 158, 11, 0.3);
}

.filter-btn-pending.active {
    background-color: var(--warning-color);
    color: white;
    border-color: var(--warning-color);
}

.filter-btn-processing {
    background-color: rgba(59, 130, 246, 0.1);
    color: var(--info-color);
    border-color: rgba(59, 130, 246, 0.3);
}

.filter-btn-processing.active {
    background-color: var(--info-color);
    color: white;
    border-color: var(--info-color);
}

.filter-btn-completed {
    background-color: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
    border-color: rgba(16, 185, 129, 0.3);
}

.filter-btn-completed.active {
    background-color: var(--success-color);
    color: white;
    border-color: var(--success-color);
}

.filter-btn-cancelled {
    background-color: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
    border-color: rgba(239, 68, 68, 0.3);
}

.filter-btn-cancelled.active {
    background-color: var(--danger-color);
    color: white;
    border-color: var(--danger-color);
}

/* Table styles */
.custom-table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.custom-table thead th {
    background: #1e293b;
    color: white;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border: none;
}

.custom-table thead th:first-child {
    border-top-left-radius: 0.5rem;
}

.custom-table thead th:last-child {
    border-top-right-radius: 0.5rem;
}

.custom-table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border-top: none;
    border-bottom: 1px solid #e2e8f0;
    transition: var(--transition);
}

.custom-table tr:hover td {
    background-color: #f8fafc;
}

.custom-table tr:last-child td {
    border-bottom: none;
}

.custom-table tr:last-child td:first-child {
    border-bottom-left-radius: 0.5rem;
}

.custom-table tr:last-child td:last-child {
    border-bottom-right-radius: 0.5rem;
}

/* Badge styling */
.badge {
    padding: 0.5em 1em;
    font-weight: 500;
    border-radius: 2rem;
    letter-spacing: 0.5px;
    transition: var(--transition);
}

.badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
}

/* Status badge colors */
.bg-warning {
    background-color: #fff7ed !important;
    color: #9a3412 !important;
}

.bg-info {
    background-color: #eff6ff !important;
    color: #1e40af !important;
}

.bg-success {
    background-color: #ecfdf5 !important;
    color: #065f46 !important;
}

.bg-danger {
    background-color: #fef2f2 !important;
    color: #991b1b !important;
}

/* Spec badges styling */
.spec-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.35rem 0.6rem;
    background-color: #f1f5f9;
    color: #475569;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    transition: var(--transition);
}

.spec-badge i {
    color: var(--primary-color);
}

.spec-badge:hover {
    background-color: #e2e8f0;
    transform: translateY(-1px);
}

/* User info styling */
.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 600;
    color: #1e293b;
}

.user-phone {
    font-size: 0.8rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin-top: 0.25rem;
}

/* Budget styling */
.budget-value {
    font-weight: 600;
    color: var(--success-color);
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
}

.empty-state i {
    font-size: 3rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.empty-state h5 {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 1.5rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

/* Animation */
@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(20px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

/* Pulse animation for badges */
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
    }
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    .filter-container {
        justify-content: center;
    }
    
    .filter-btn {
        flex: 1 1 auto;
        text-align: center;
        justify-content: center;
    }
}

/* Action buttons */
.btn-action {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-action i {
    font-size: 1rem;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
}
</style>

<div class="container-fluid px-4">
    <div class="page-header">
        <h1 class="mb-2 fw-bold">Custom Orders</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Custom Orders</li>
            </ol>
        </nav>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>
                <strong>Berhasil!</strong> <?= $_SESSION['success']; ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <strong>Gagal!</strong> <?= $_SESSION['error']; ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Pending Orders -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card pending h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">Menunggu</div>
                            <div class="stats-value"><?= $count_pending ?></div>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                    </div>
                    <a href="index.php?status=pending" class="btn btn-sm btn-warning w-100 mt-3">
                        <i class="bi bi-eye me-2"></i>Lihat Order
                    </a>
                </div>
            </div>
        </div>

        <!-- Processing Orders -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card processing h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">Diproses</div>
                            <div class="stats-value"><?= $count_processing ?></div>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                    </div>
                    <a href="index.php?status=processing" class="btn btn-sm btn-info w-100 mt-3 text-white">
                        <i class="bi bi-eye me-2"></i>Lihat Order
                    </a>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card completed h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">Selesai</div>
                            <div class="stats-value"><?= $count_completed ?></div>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                    <a href="index.php?status=completed" class="btn btn-sm btn-success w-100 mt-3">
                        <i class="bi bi-eye me-2"></i>Lihat Order
                    </a>
                </div>
            </div>
        </div>

        <!-- Cancelled Orders -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card cancelled h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">Dibatalkan</div>
                            <div class="stats-value"><?= $count_cancelled ?></div>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                    <a href="index.php?status=cancelled" class="btn btn-sm btn-danger w-100 mt-3">
                        <i class="bi bi-eye me-2"></i>Lihat Order
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="mb-4 filter-container">
        <a href="index.php" class="filter-btn filter-btn-all <?= !isset($_GET['status']) ? 'active' : '' ?>">
            <i class="bi bi-grid-3x3-gap"></i> Semua Order
        </a>
        <a href="index.php?status=pending" class="filter-btn filter-btn-pending <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'active' : '' ?>">
            <i class="bi bi-clock"></i> Menunggu
        </a>
        <a href="index.php?status=processing" class="filter-btn filter-btn-processing <?= (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'active' : '' ?>">
            <i class="bi bi-arrow-repeat"></i> Diproses
        </a>
        <a href="index.php?status=completed" class="filter-btn filter-btn-completed <?= (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'active' : '' ?>">
            <i class="bi bi-check-circle"></i> Selesai
        </a>
        <a href="index.php?status=cancelled" class="filter-btn filter-btn-cancelled <?= (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'active' : '' ?>">
            <i class="bi bi-x-circle"></i> Dibatalkan
        </a>
    </div>

    <!-- Custom Orders Table -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-list-ul text-primary me-2"></i>
                    <span class="fw-bold">Daftar Custom Order</span>
                </div>
                <button class="btn btn-light btn-sm" onclick="window.location.reload()">
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
                            <th width="5%">ID</th>
                            <th width="15%">Customer</th>
                            <th width="30%">Spesifikasi</th>
                            <th width="10%">Budget</th>
                            <th width="15%">Tanggal</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($custom_orders)) : ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Belum ada custom order</span>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($custom_orders as $order) : ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">#<?= $order['order_id'] ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($order['user_name']) ?></div>
                                        <small class="text-muted">
                                            <i class="bi bi-telephone me-1"></i>
                                            <?= htmlspecialchars($order['user_phone']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="mb-1">
                                            <span class="spec-badge">
                                                <i class="bi bi-cpu me-1"></i>
                                                <?= htmlspecialchars($order['processor']) ?>
                                            </span>
                                            <span class="spec-badge">
                                                <i class="bi bi-memory me-1"></i>
                                                <?= htmlspecialchars($order['ram']) ?>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="spec-badge">
                                                <i class="bi bi-device-ssd me-1"></i>
                                                <?= htmlspecialchars($order['storage']) ?>
                                            </span>
                                            <span class="spec-badge">
                                                <i class="bi bi-gpu-card me-1"></i>
                                                <?= substr(htmlspecialchars($order['vga']), 0, 20) . (strlen($order['vga']) > 20 ? '...' : '') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">
                                            Rp <?= number_format($order['budget'], 0, ',', '.') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="bi bi-calendar-event text-muted me-1"></i>
                                            <?= date('d M Y', strtotime($order['created_at'])) ?>
                                        </div>
                                        <small class="text-muted">
                                            <?= date('H:i', strtotime($order['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge <?= $status_labels[$order['status']]['class'] ?>">
                                            <i class="bi bi-<?= $status_labels[$order['status']]['icon'] ?> me-1"></i>
                                            <?= $status_labels[$order['status']]['label'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="detail.php?id=<?= $order['order_id'] ?>" class="btn btn-primary btn-action">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        
                                        <?php if ($order['status'] == 'pending') : ?>
                                        <a href="detail.php?id=<?= $order['order_id'] ?>" class="btn btn-info btn-action text-white" title="Proses Order">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($order['status'] == 'processing') : ?>
                                        <a href="detail.php?id=<?= $order['order_id'] ?>" class="btn btn-success btn-action" title="Selesaikan Order">
                                            <i class="bi bi-check-circle"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi untuk Cards Statistik
    const animateStatsCards = () => {
        const statsCards = document.querySelectorAll('.stats-card');
        
        statsCards.forEach((card, index) => {
            // Set initial state (invisible and translated down)
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            
            // Animate with staggered delay
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (index * 100));
            
            // Add hover effect
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px)';
                card.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
                
                // Also animate the icon inside
                const icon = card.querySelector('.stats-icon');
                if (icon) {
                    icon.style.transform = 'scale(1.2) rotate(5deg)';
                }
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';
                
                // Reset icon animation
                const icon = card.querySelector('.stats-icon');
                if (icon) {
                    icon.style.transform = 'scale(1) rotate(0)';
                }
            });
        });
    };

    // Animasi untuk Tabel Data
    const animateTableRows = () => {
        const tableRows = document.querySelectorAll('.custom-table tbody tr');
        
        tableRows.forEach((row, index) => {
            // Set initial state
            row.style.opacity = '0';
            row.style.transform = 'translateX(-10px)';
            row.style.transition = 'all 0.3s ease-out';
            
            // Animate with staggered delay
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateX(0)';
            }, 300 + (index * 50)); // Slightly faster than cards
            
            // Add hover effect
            row.addEventListener('mouseenter', () => {
                row.style.backgroundColor = 'rgba(79, 70, 229, 0.05)';
                row.style.transform = 'translateX(5px)';
                
                // Highlight action buttons
                const actionBtns = row.querySelectorAll('.btn-action');
                actionBtns.forEach(btn => {
                    btn.style.transform = 'translateY(-3px)';
                    btn.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
                });
                
                // Highlight spec badges
                const badges = row.querySelectorAll('.spec-badge');
                badges.forEach(badge => {
                    badge.style.backgroundColor = 'rgba(79, 70, 229, 0.1)';
                });
            });
            
            row.addEventListener('mouseleave', () => {
                row.style.backgroundColor = '';
                row.style.transform = 'translateX(0)';
                
                // Reset action buttons
                const actionBtns = row.querySelectorAll('.btn-action');
                actionBtns.forEach(btn => {
                    btn.style.transform = '';
                    btn.style.boxShadow = '';
                });
                
                // Reset spec badges
                const badges = row.querySelectorAll('.spec-badge');
                badges.forEach(badge => {
                    badge.style.backgroundColor = '';
                });
            });
        });
    };

    // Efek klik untuk Filter Buttons
    const setupFilterButtons = () => {
        const filterButtons = document.querySelectorAll('.filter-btn');
        
        filterButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Add click effect
                btn.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    btn.style.transform = '';
                }, 150);
                
                // Tambahkan efek "Loading"
                const originalContent = btn.innerHTML;
                
                // Hanya tambahkan loading jika button tidak active
                if (!btn.classList.contains('active')) {
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
                    
                    // Reset setelah navigasi (tetapi biarkan navigasi terjadi dulu)
                    setTimeout(() => {
                        btn.innerHTML = originalContent;
                    }, 300);
                }
            });
        });
    };

    // Implementasi fitur pencarian cepat di tabel
    const setupQuickSearch = () => {
        // Tambahkan search box sebelum tabel
        const tableCard = document.querySelector('.table-card');
        const tableHeader = tableCard.querySelector('.card-header');
        
        const searchDiv = document.createElement('div');
        searchDiv.className = 'mt-3';
        searchDiv.innerHTML = `
            <div class="input-group">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="orderSearchInput" class="form-control border-0 bg-light" 
                       placeholder="Cari custom order..." style="border-radius: 8px;">
            </div>
        `;
        
        tableHeader.appendChild(searchDiv);
        
        // Implementasi fungsi pencarian
        const searchInput = document.getElementById('orderSearchInput');
        
        searchInput.addEventListener('keyup', () => {
            const searchTerm = searchInput.value.toLowerCase();
            const tableRows = document.querySelectorAll('.custom-table tbody tr');
            
            tableRows.forEach(row => {
                const textContent = row.textContent.toLowerCase();
                
                if (textContent.includes(searchTerm)) {
                    row.style.display = '';
                    // Highlight the search term
                    highlightSearchTerm(row, searchTerm);
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show empty state if no results
            const visibleRows = document.querySelectorAll('.custom-table tbody tr[style=""]').length;
            const tbody = document.querySelector('.custom-table tbody');
            
            if (visibleRows === 0 && searchTerm !== '') {
                // Check if we already have a no-results row
                let noResultsRow = document.getElementById('no-search-results');
                
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'no-search-results';
                    noResultsRow.innerHTML = `
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-search fs-1 d-block mb-2 text-muted"></i>
                            <span class="text-muted">Tidak ada hasil untuk pencarian "<strong>${searchTerm}</strong>"</span>
                        </td>
                    `;
                    tbody.appendChild(noResultsRow);
                } else {
                    noResultsRow.querySelector('strong').textContent = searchTerm;
                    noResultsRow.style.display = '';
                }
            } else {
                const noResultsRow = document.getElementById('no-search-results');
                if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
            }
        });
    };
    
    // Helper function untuk highlight search term
    const highlightSearchTerm = (row, term) => {
        if (term === '') return;
        
        // Reset any existing highlights first
        const existingHighlights = row.querySelectorAll('span.search-highlight');
        existingHighlights.forEach(el => {
            el.outerHTML = el.innerHTML;
        });
        
        // Only highlight text content, not in attributes
        const textNodes = [];
        const walk = document.createTreeWalker(row, NodeFilter.SHOW_TEXT, null, false);
        
        let node;
        while(node = walk.nextNode()) {
            if (node.nodeValue.trim() !== '') {
                textNodes.push(node);
            }
        }
        
        textNodes.forEach(textNode => {
            const content = textNode.nodeValue;
            const lowerContent = content.toLowerCase();
            const index = lowerContent.indexOf(term);
            
            if (index >= 0) {
                const spanNode = document.createElement('span');
                spanNode.className = 'search-highlight';
                spanNode.style.backgroundColor = 'rgba(79, 70, 229, 0.2)';
                spanNode.style.borderRadius = '2px';
                spanNode.style.padding = '0 2px';
                
                const before = document.createTextNode(content.substring(0, index));
                const match = document.createTextNode(content.substring(index, index + term.length));
                const after = document.createTextNode(content.substring(index + term.length));
                
                spanNode.appendChild(match);
                
                const fragment = document.createDocumentFragment();
                fragment.appendChild(before);
                fragment.appendChild(spanNode);
                fragment.appendChild(after);
                
                textNode.parentNode.replaceChild(fragment, textNode);
            }
        });
    };

    // Efek loading pada tombol refresh
    const setupRefreshButton = () => {
        const refreshBtn = document.querySelector('.card-header .btn');
        
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                const icon = refreshBtn.querySelector('.bi-arrow-clockwise');
                
                // Add rotation animation
                icon.style.transition = 'transform 1s';
                icon.style.transform = 'rotate(360deg)';
                
                // Change button text
                refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Memuat...';
                
                // Simulate reload delay
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            });
        }
    };
    
    // Alert toast handler
    const setupAlertToasts = () => {
        // Create toast container if doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        // Convert existing alerts to toasts
        const alerts = document.querySelectorAll('.alert');
        
        alerts.forEach((alert, index) => {
            // Get alert content
            const content = alert.innerHTML;
            const isSuccess = alert.classList.contains('alert-success');
            const isDanger = alert.classList.contains('alert-danger');
            
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast align-items-center ${isSuccess ? 'bg-success' : ''} ${isDanger ? 'bg-danger' : ''} text-white border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            toast.style.transition = 'all 0.3s ease';
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${content}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            // Add to container
            toastContainer.appendChild(toast);
            
            // Show with delay
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
                
                const bsToast = new bootstrap.Toast(toast, {
                    autohide: true,
                    delay: 5000
                });
                
                bsToast.show();
                
                // Remove original alert
                alert.style.display = 'none';
            }, 300 + (index * 100));
        });
    };
    
    // Animasi untuk nilai statistik
    const animateStatValues = () => {
        const statValues = document.querySelectorAll('.stats-value');
        
        statValues.forEach(element => {
            const finalValue = parseInt(element.innerText.replace(/\D/g, ''));
            
            if (!isNaN(finalValue)) {
                // Start from 0
                element.innerText = '0';
                
                // Animate to final value
                let startValue = 0;
                const duration = 1500; // ms
                const incrementInterval = 20; // ms
                const totalIncrements = duration / incrementInterval;
                const incrementAmount = finalValue / totalIncrements;
                
                const intervalId = setInterval(() => {
                    startValue += incrementAmount;
                    
                    if (startValue >= finalValue) {
                        element.innerText = finalValue;
                        clearInterval(intervalId);
                    } else {
                        element.innerText = Math.floor(startValue);
                    }
                }, incrementInterval);
            }
        });
    };
    
    // Tambahkan efek pulsing badge
    const animateStatusBadges = () => {
        const statusBadges = document.querySelectorAll('.badge');
        
        statusBadges.forEach(badge => {
            // Tambahkan pulsing effect hanya untuk status Pending
            if (badge.classList.contains('bg-warning')) {
                badge.style.animation = 'pulse 2s infinite';
            }
        });
    };
    
    // Tambahkan CSS untuk efek pulse
    const addPulseAnimation = () => {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
                }
                70% {
                    box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
                }
                100% {
                    box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
                }
            }
        `;
        document.head.appendChild(style);
    };
    
    // Eksekusi semua fungsi
    const initializeInteractiveElements = () => {
        // Add pulse animation CSS
        addPulseAnimation();
        
        // Execute animations and interactions
        animateStatsCards();
        animateTableRows();
        setupFilterButtons();
        setupQuickSearch();
        setupRefreshButton();
        setupAlertToasts();
        animateStatValues();
        animateStatusBadges();
        
        // Log initialization complete
        console.log('Interactive elements initialized');
    };
    
    // Start initialization
    initializeInteractiveElements();
});
</script>

<?php include_once '../includes/footer.php'; ?>