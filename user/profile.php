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

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(45deg, #0d6efd, #0dcaf0);
            color: white;
            border: none;
            padding: 20px;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .card-body {
            padding: 25px;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #0dcaf0);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13,110,253,0.2);
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px;
        }

        .stats-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background: #fff;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .stats-card h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .stats-card p {
            color: var(--secondary-color);
            margin: 0;
            font-size: 0.9rem;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: #f8f9fa;
            border: none;
            padding: 15px;
            font-weight: 600;
        }

        .table tbody td {
            padding: 15px;
            border-color: #f1f3f5;
            vertical-align: middle;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 500;
        }

        .order-id {
            font-weight: 600;
            color: var(--primary-color);
        }

        .nav-link {
            padding: 10px 15px;
            font-weight: 500;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .text-gradient {
            background: linear-gradient(45deg, #0d6efd, #0dcaf0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const passwordType = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', passwordType);

        // Toggle the icon
        const icon = this.querySelector('i');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
</script>
