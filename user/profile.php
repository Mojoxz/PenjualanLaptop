<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = query("SELECT * FROM tb_user WHERE user_id = $user_id")[0];

// Proses update profile
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    
    // Jika ada password baru
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $data = [
            'nama' => $nama,
            'password' => $password,
            'alamat' => $alamat,
            'telepon' => $telepon
        ];
    } else {
        $data = [
            'nama' => $nama,
            'alamat' => $alamat,
            'telepon' => $telepon
        ];
    }
    
    if (ubah('tb_user', $data, "user_id = $user_id")) {
        $_SESSION['success'] = 'Profile berhasil diupdate!';
        header("Location: profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Unesa Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
:root {
    --primary-color: #4361ee;
    --primary-gradient: linear-gradient(135deg, #4361ee, #3a0ca3);
    --secondary-color: #3a0ca3;
    --accent-color: #4cc9f0;
    --hover-color: #3b82f6;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #3b82f6;
    --light-color: #f8fafc;
    --card-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    --card-hover-shadow: 0 15px 30px rgba(67, 97, 238, 0.15);
    --border-radius: 16px;
    --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

body {
    background-color: #f8fafc;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    padding-bottom: 30px;
    color: #334155;
}

/* Navbar Enhanced */
.navbar {
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
    background: var(--primary-gradient) !important;
    padding: 15px 0;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.navbar-brand {
    font-weight: 700;
    font-size: 1.35rem;
    letter-spacing: -0.5px;
    position: relative;
}

.navbar-brand::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 50%;
    height: 3px;
    background: white;
    border-radius: 5px;
    opacity: 0;
    transform: translateY(5px);
    transition: var(--transition);
}

.navbar-brand:hover::after {
    opacity: 1;
    transform: translateY(0);
    width: 100%;
}

.navbar-nav .nav-link {
    font-weight: 500;
    padding: 8px 16px;
    margin: 0 3px;
    border-radius: 8px;
    transition: var(--transition);
}

.navbar-nav .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.navbar-nav .nav-link.active {
    background: rgba(255, 255, 255, 0.2);
    font-weight: 600;
}

/* Badge Enhancement */
.badge {
    font-weight: 600;
    padding: 0.4em 0.75em;
    border-radius: 6px;
    position: relative;
    top: -2px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
}

.badge.bg-danger {
    background: var(--danger-color) !important;
    animation: pulse 2s infinite;
}

.badge.bg-info {
    background: var(--info-color) !important;
}

.badge.bg-success {
    background: var(--success-color) !important;
}

.badge.bg-warning {
    background: var(--warning-color) !important;
    color: #7c2d12 !important;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Card Enhanced */
.card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    overflow: hidden;
    position: relative;
    z-index: 1;
    margin-bottom: 2rem;
}

.card::before {
    content: '';
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    z-index: -1;
    background: var(--primary-gradient);
    border-radius: calc(var(--border-radius) + 5px);
    opacity: 0;
    transition: var(--transition);
    transform: scale(0.98);
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: var(--card-hover-shadow);
}

.card:hover::before {
    opacity: 0.3;
    transform: scale(1);
}

.card-header {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
}

.card-header h5 {
    margin: 0;
    font-weight: 700;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
}

.card-header h5 i {
    margin-right: 0.75rem;
    font-size: 1.2rem;
}

.card-body {
    padding: 1.75rem;
}

/* Alert Styling */
.alert {
    border-radius: 16px;
    border: none;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
    animation: fadeIn 0.5s ease;
    display: flex;
    align-items: center;
}

.alert-success {
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    color: #065f46;
}

.alert i {
    font-size: 1.2rem;
    margin-right: 0.75rem;
}

.btn-close {
    margin-left: auto;
    opacity: 0.8;
    transition: var(--transition);
}

.btn-close:hover {
    opacity: 1;
    transform: rotate(90deg);
}

/* Form Elements */
.form-label {
    color: #475569;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.form-label i {
    color: var(--primary-color);
    margin-right: 0.5rem;
    transition: var(--transition);
}

.form-control, .form-select, textarea.form-control {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: var(--transition);
    color: #1e293b;
    background-color: #fff;
}

.form-control:focus, 
.form-select:focus,
textarea.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
}

.input-group .form-control {
    border-right: none;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.input-group .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border: 2px solid #e2e8f0;
    border-left: none;
}

.input-group .btn:hover {
    background-color: #f8fafc;
    color: var(--primary-color);
}

.input-group .btn:focus {
    box-shadow: none;
}

small.text-muted {
    color: #64748b !important;
    font-size: 0.85rem;
    margin-top: 0.5rem;
    display: block;
}

/* Button Styles */
.btn {
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    font-weight: 600;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transition: var(--transition);
    z-index: -1;
    opacity: 0;
}

.btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.btn:hover::before {
    opacity: 1;
}

.btn-primary {
    background: var(--primary-gradient);
    border: none;
    color: white;
}

.btn-primary::before {
    background: linear-gradient(135deg, #3a0ca3, #4361ee);
}

.btn-primary:hover {
    box-shadow: 0 8px 15px rgba(67, 97, 238, 0.25);
}

.btn-light {
    background-color: #f1f5f9;
    border: none;
    color: #475569;
}

.btn-light:hover {
    background-color: #e2e8f0;
    color: #1e293b;
}

.btn-outline-primary {
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
    background: transparent;
}

.btn-outline-primary::before {
    background: var(--primary-gradient);
}

.btn-outline-primary:hover {
    color: white;
    border-color: transparent;
}

.btn i {
    transition: var(--transition);
}

.btn-primary:hover i, 
.btn-outline-primary:hover i {
    transform: translateX(3px);
}

.btn-light:hover i.bi-arrow-left {
    transform: translateX(-3px);
}

/* Stats Card */
.stats-card {
    text-align: center;
    padding: 1.5rem;
    border-radius: 12px;
    background: #fff;
    transition: var(--transition);
    border: 2px solid #f1f5f9;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    border-color: var(--primary-color);
}

.stats-card i {
    display: inline-block;
    padding: 1rem;
    border-radius: 50%;
    background: rgba(67, 97, 238, 0.1);
    color: var(--primary-color);
    margin-bottom: 1rem;
    transition: var(--transition);
}

.stats-card:hover i {
    transform: scale(1.1);
    background: var(--primary-gradient);
    color: white;
}

.stats-card h4 {
    color: #1e293b;
    font-weight: 700;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.stats-card p {
    color: #64748b;
    margin: 0;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Table Styling */
.table-responsive {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: linear-gradient(45deg, #f1f5f9, #e2e8f0);
    color: #475569;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    padding: 1.25rem 1rem;
    border: none;
}

.table tbody td {
    padding: 1.25rem 1rem;
    border-color: #f1f5f9;
    vertical-align: middle;
}

.table tbody tr {
    transition: var(--transition);
}

.table tbody tr:hover {
    background-color: rgba(67, 97, 238, 0.03);
}

.order-id {
    font-weight: 700;
    color: var(--primary-color);
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: rgba(67, 97, 238, 0.1);
    border-radius: 6px;
    transition: var(--transition);
}

tr:hover .order-id {
    background: rgba(67, 97, 238, 0.2);
    transform: translateX(3px);
}

/* Empty State */
.text-center.py-4 {
    padding: 2rem 0;
}

.text-center.py-4 i {
    font-size: 3rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.text-center.py-4 p {
    color: #64748b;
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
}

.text-end.mt-4 {
    margin-top: 1.5rem;
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

.row.g-4 > * {
    opacity: 0;
    animation: fadeIn 0.5s ease forwards;
}

.row.g-4 > *:nth-child(1) { animation-delay: 0.1s; }
.row.g-4 > *:nth-child(2) { animation-delay: 0.2s; }
.row.g-4 > *:nth-child(3) { animation-delay: 0.3s; }

.table tbody tr {
    opacity: 0;
    animation: fadeIn 0.5s ease forwards;
}

.table tbody tr:nth-child(1) { animation-delay: 0.1s; }
.table tbody tr:nth-child(2) { animation-delay: 0.2s; }
.table tbody tr:nth-child(3) { animation-delay: 0.3s; }
.table tbody tr:nth-child(4) { animation-delay: 0.4s; }
.table tbody tr:nth-child(5) { animation-delay: 0.5s; }

/* Responsive Adjustments */
@media (max-width: 767.98px) {
    .card-body {
        padding: 1.25rem;
    }
    
    .table thead th,
    .table tbody td {
        padding: 0.75rem;
    }
    
    .stats-card {
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .stats-card h4 {
        font-size: 1.25rem;
    }
    
    .btn {
        padding: 0.6rem 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between .btn {
        width: 100%;
    }
}

/* Toggle Password Button */
#togglePassword {
    background-color: #f1f5f9;
    border-color: #e2e8f0;
    color: #64748b;
    transition: var(--transition);
}

#togglePassword:hover {
    background-color: #e2e8f0;
    color: var(--primary-color);
}

/* Password Field Effects */
#password:focus + #togglePassword {
    border-color: var(--primary-color);
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
                        <a class="nav-link" href="index.php">Home</a>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">
                            <i class="bi bi-person-circle me-1"></i>Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Profile Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-person-circle me-2"></i>Profile Saya
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])) : ?>
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <form action="" method="post" id="profileForm">
                            <div class="mb-3">
                                <label for="nama" class="form-label">
                                    <i class="bi bi-person me-2"></i>Nama Lengkap
                                </label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       value="<?= htmlspecialchars($user['nama']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-2"></i>Password Baru
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Kosongkan jika tidak ingin mengubah password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">
                                    <i class="bi bi-geo-alt me-2"></i>Alamat
                                </label>
                                <textarea class="form-control" id="alamat" name="alamat" 
                                          rows="3" required><?= htmlspecialchars($user['alamat']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="telepon" class="form-label">
                                    <i class="bi bi-telephone me-2"></i>Nomor Telepon
                                </label>
                                <input type="tel" class="form-control" id="telepon" name="telepon" 
                                       value="<?= htmlspecialchars($user['telepon']); ?>" required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="index.php" class="btn btn-light">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" name="update" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up me-2"></i>Statistik Pembelian
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $total_orders = query("SELECT COUNT(*) as total FROM tb_pembelian WHERE user_id = $user_id")[0]['total'];
                        $total_products = query("SELECT COALESCE(SUM(dp.jumlah), 0) as total 
                                               FROM tb_pembelian p 
                                               LEFT JOIN tb_detail_pembelian dp ON p.id_pembelian = dp.id_pembelian 
                                               WHERE p.user_id = $user_id")[0]['total'];
                        $total_spent = query("SELECT COALESCE(SUM(jumlah_pembayaran), 0) as total 
                                            FROM tb_pembelian 
                                            WHERE user_id = $user_id")[0]['total'];
                        ?>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="stats-card">
                                    <i class="bi bi-cart-check fs-3 text-primary mb-2"></i>
                                    <h4><?= $total_orders; ?></h4>
                                    <p>Total Pesanan</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card">
                                    <i class="bi bi-box-seam fs-3 text-primary mb-2"></i>
                                    <h4><?= $total_products; ?></h4>
                                    <p>Produk Dibeli</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card">
                                    <i class="bi bi-wallet2 fs-3 text-primary mb-2"></i>
                                    <h4>Rp <?= number_format($total_spent, 0, ',', '.'); ?></h4>
                                    <p>Total Pengeluaran</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>Riwayat Pembelian Terbaru
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $recent_orders = query("SELECT p.*, pb.jenis_pembayaran 
                                             FROM tb_pembelian p 
                                             LEFT JOIN tb_pembayaran pb ON p.pembayaran_id = pb.pembayaran_id 
                                             WHERE p.user_id = $user_id 
                                             ORDER BY p.tanggal DESC LIMIT 5");
                        ?>

                        <?php if (empty($recent_orders)) : ?>
                            <div class="text-center py-4">
                                <i class="bi bi-bag-x fs-1 text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Belum ada riwayat pembelian</p>
                            </div>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Pembayaran</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_orders as $order) : ?>
                                            <tr>
                                                <td>
                                                    <span class="order-id">
                                                        #<?= htmlspecialchars($order['id_pembelian']); ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($order['tanggal'])); ?></td>
                                                <td>Rp <?= number_format($order['jumlah_pembayaran'], 0, ',', '.'); ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-credit-card me-1"></i>
                                                        <?= htmlspecialchars($order['jenis_pembayaran'] ?? 'Tidak Diketahui'); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    // Ambil status dari database atau logika bisnis Anda
                                                    $status = $order['status'] ?? 'Selesai';
                                                    $statusClass = 'success';
                                                    $statusIcon = 'check-circle';
                                                    
                                                    switch(strtolower($status)) {
                                                        case 'pending':
                                                            $statusClass = 'warning';
                                                            $statusIcon = 'clock';
                                                            break;
                                                        case 'proses':
                                                            $statusClass = 'info';
                                                            $statusIcon = 'arrow-repeat';
                                                            break;
                                                        case 'batal':
                                                            $statusClass = 'danger';
                                                            $statusIcon = 'x-circle';
                                                            break;
                                                        default:
                                                            $statusClass = 'success';
                                                            $statusIcon = 'check-circle';
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>">
                                                        <i class="bi bi-<?= $statusIcon ?> me-1"></i>
                                                        <?= htmlspecialchars($status); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if (count($recent_orders) >= 5) : ?>
                                <div class="text-end mt-4">
                                    <a href="orders.php" class="btn btn-outline-primary">
                                        <i class="bi bi-list-ul me-2"></i>Lihat Semua Pesanan
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const passwordType = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', passwordType);

            // Toggle the icon
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
            
            // Change button style
            this.classList.toggle('active');
        });
    }
    
    // Form validation and enhanced form behavior
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        // Add focus effect to form fields
        const formFields = profileForm.querySelectorAll('.form-control, .form-select');
        formFields.forEach(field => {
            field.addEventListener('focus', function() {
                const label = this.previousElementSibling;
                if (label && label.classList.contains('form-label')) {
                    label.querySelector('i').classList.add('icon-active');
                }
            });
            
            field.addEventListener('blur', function() {
                const label = this.previousElementSibling;
                if (label && label.classList.contains('form-label')) {
                    label.querySelector('i').classList.remove('icon-active');
                }
            });
        });
        
        // Enhanced form submission
        profileForm.addEventListener('submit', function(e) {
            // Start loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2 spin"></i>Memperbarui...';
            submitBtn.classList.add('processing');
            
            // Let the form submit normally
            // This is just for visual feedback
            setTimeout(() => {
                // If for some reason the form doesn't actually submit after 3 seconds,
                // we reset the button to allow resubmission
                if (!this.classList.contains('submitted')) {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.classList.remove('processing');
                }
            }, 3000);
            
            this.classList.add('submitted');
        });
        
        // Password validation
        const passwordField = document.getElementById('password');
        if (passwordField) {
            passwordField.addEventListener('input', function() {
                const value = this.value.trim();
                
                if (value && value.length < 6) {
                    this.classList.add('is-invalid');
                    
                    // Check if feedback element exists, if not create it
                    let feedback = this.parentNode.parentNode.querySelector('.invalid-feedback');
                    if (!feedback) {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.style.display = 'block';
                        this.parentNode.parentNode.appendChild(feedback);
                    }
                    
                    feedback.innerText = 'Password harus minimal 6 karakter';
                } else {
                    this.classList.remove('is-invalid');
                    
                    // Remove any existing feedback
                    const feedback = this.parentNode.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.remove();
                    }
                    
                    // Add valid feedback if password is entered and valid
                    if (value && value.length >= 6) {
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                    }
                }
            });
        }
    }
    
    // Alert dismiss animation
    document.querySelectorAll('.alert .btn-close').forEach(btn => {
        btn.addEventListener('click', function() {
            const alert = this.closest('.alert');
            alert.classList.add('fade-out');
            setTimeout(() => {
                alert.remove();
            }, 300);
        });
    });
    
    // Enhance Statistics Card interactions
    document.querySelectorAll('.stats-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('card-hover');
            animate(this.querySelector('h4'));
        });
        
        card.addEventListener('mouseleave', function() {
            this.classList.remove('card-hover');
        });
    });
    
    // Add hover effects to table rows
    document.querySelectorAll('.table tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.classList.add('row-hover');
        });
        
        row.addEventListener('mouseleave', function() {
            this.classList.remove('row-hover');
        });
    });
    
    // Fancy animation for statistics
    function animate(element) {
        if (element && !element.dataset.animated) {
            element.dataset.animated = true;
            
            const text = element.textContent;
            let value;
            
            // Check if it contains currency symbol
            if (text.includes('Rp')) {
                value = parseInt(text.replace(/\D/g, ''));
                if (!isNaN(value)) {
                    animateValue(element, 0, value, 1000, true);
                }
            } else {
                value = parseInt(text);
                if (!isNaN(value)) {
                    animateValue(element, 0, value, 1000, false);
                }
            }
        }
    }
    
    function animateValue(element, start, end, duration, isCurrency) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            
            if (isCurrency) {
                element.textContent = 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            } else {
                element.textContent = value;
            }
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
});

// Add dynamic CSS for enhanced interactions
(function() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-out {
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }
        
        .icon-active {
            transform: scale(1.2);
            color: var(--primary-color) !important;
        }
        
        .processing {
            background: linear-gradient(135deg, #10b981, #3b82f6) !important;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .spin {
            animation: spin 1s linear infinite;
            display: inline-block;
        }
        
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.85rem;
            color: var(--danger-color);
        }
        
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
        
        .is-invalid {
            border-color: var(--danger-color) !important;
        }
        
        .is-valid {
            border-color: var(--success-color) !important;
        }
        
        .card-hover {
            z-index: 10;
        }
        
        .row-hover {
            background-color: rgba(67, 97, 238, 0.05) !important;
        }
        
        .card-hover h4 {
            color: var(--primary-color);
        }
        
        #togglePassword.active {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }
    `;
    document.head.appendChild(style);
})();
</script>
