<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
   header("Location: ../auth/login.php");
   exit;
}

$user_id = $_SESSION['user_id'];
$user = query("SELECT * FROM tb_user WHERE user_id = $user_id")[0];

$query = "SELECT p.*, pb.jenis_pembayaran 
         FROM tb_pembelian p 
         LEFT JOIN tb_pembayaran pb ON p.pembayaran_id = pb.pembayaran_id 
         WHERE p.user_id = $user_id 
         ORDER BY p.tanggal DESC";
$orders = query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Pesanan Saya - Unesa Laptop</title>
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

       .order-card {
           border-radius: 15px;
           border: none;
           box-shadow: 0 5px 15px rgba(0,0,0,0.05);
           transition: transform 0.3s ease;
           overflow: hidden;
       }

       .order-card:hover {
           transform: translateY(-5px);
           box-shadow: 0 10px 20px rgba(0,0,0,0.1);
       }

       .card-header {
           background: linear-gradient(45deg, #f8f9fa, #e9ecef);
           border-bottom: 1px solid rgba(0,0,0,0.05);
       }

       .btn-detail {
           background: linear-gradient(45deg, #0d6efd, #0dcaf0);
           border: none;
           color: white;
           padding: 8px 16px;
           border-radius: 8px;
           transition: all 0.3s ease;
       }

       .btn-detail:hover {
           transform: translateY(-2px);
           box-shadow: 0 5px 15px rgba(13,110,253,0.2);
           color: white;
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
       }

       .btn-close {
           filter: brightness(0) invert(1);
       }

       .table {
           border-radius: 10px;
           overflow: hidden;
       }

       .table thead {
           background: linear-gradient(45deg, #f8f9fa, #e9ecef);
       }

       .alert {
           border-radius: 12px;
           border: none;
       }

       .product-list-item {
           padding: 10px;
           margin-bottom: 8px;
           background: #f8f9fa;
           border-radius: 8px;
       }

       .order-meta {
           padding: 15px;
           background: #f8f9fa;
           border-radius: 10px;
           margin-top: 15px;
       }
   </style>
</head>
<body>
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
                       <a class="nav-link active" href="orders.php">Pesanan Saya</a>
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
                           <i class="bi bi-person-circle me-1"></i><?= $user['nama']; ?>
                       </a>
                       <ul class="dropdown-menu dropdown-menu-end">
                           <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                           <li><hr class="dropdown-divider"></li>
                           <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                       </ul>
                   </li>
               </ul>
           </div>
       </div>
   </nav>

   <div class="container py-4">
       <h2 class="mb-4 fw-bold"><i class="bi bi-bag-check me-2"></i>Pesanan Saya</h2>

       <?php if (isset($_SESSION['success'])) : ?>
           <div class="alert alert-success alert-dismissible fade show" role="alert">
               <?= $_SESSION['success']; ?>
               <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
           </div>
           <?php unset($_SESSION['success']); ?>
       <?php endif; ?>

       <?php if (empty($orders)) : ?>
           <div class="alert alert-info d-flex align-items-center">
               <i class="bi bi-info-circle me-2"></i>
               Belum ada pesanan. <a href="index.php" class="ms-2">Belanja sekarang</a>
           </div>
       <?php else : ?>
           <div class="row">
               <?php foreach ($orders as $order) : ?>
                   <div class="col-md-6 mb-4">
                       <div class="order-card card">
                           <div class="card-header py-3">
                               <div class="d-flex justify-content-between align-items-center">
                                   <h6 class="mb-0 fw-bold">Order #<?= $order['id_pembelian']; ?></h6>
                                   <span class="badge bg-primary">
                                       <?= date('d F Y', strtotime($order['tanggal'])); ?>
                                   </span>
                               </div>
                           </div>
                           <div class="card-body">
                               <?php
                               $id_pembelian = $order['id_pembelian'];
                               $detail_query = "SELECT dp.*, b.nama_barang, b.harga_jual 
                                              FROM tb_detail_pembelian dp 
                                              JOIN tb_barang b ON dp.barang_id = b.barang_id 
                                              WHERE dp.id_pembelian = $id_pembelian";
                               $details = query($detail_query);
                               ?>
                               
                               <div class="mb-3">
                                   <h6 class="fw-bold mb-3">Detail Produk:</h6>
                                   <?php foreach ($details as $detail) : ?>
                                       <div class="product-list-item">
                                           <div class="d-flex justify-content-between align-items-center">
                                               <span><?= $detail['nama_barang']; ?></span>
                                               <span><?= $detail['jumlah']; ?>x</span>
                                           </div>
                                           <div class="text-primary mt-1">
                                               Rp <?= number_format($detail['harga_jual'], 0, ',', '.'); ?>
                                           </div>
                                       </div>
                                   <?php endforeach; ?>
                               </div>

                               <div class="order-meta">
                                   <div class="row g-3">
                                       <div class="col-6">
                                           <div class="fw-bold mb-1">Jenis Pembayaran</div>
                                           <div><?= $order['jenis_pembayaran']; ?></div>
                                       </div>
                                       <div class="col-6 text-end">
                                           <div class="fw-bold mb-1">Total Bayar</div>
                                           <div class="text-primary fw-bold">
                                               Rp <?= number_format($order['jumlah_pembayaran'], 0, ',', '.'); ?>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                           <div class="card-footer border-0 bg-white text-end py-3">
                               <button type="button" class="btn-detail" data-bs-toggle="modal" data-bs-target="#orderDetail<?= $order['id_pembelian']; ?>">
                                   <i class="bi bi-eye me-2"></i>Detail Pesanan
                               </button>
                           </div>
                       </div>
                   </div>

                   <!-- Modal Detail Pesanan -->
                   <div class="modal fade" id="orderDetail<?= $order['id_pembelian']; ?>" tabindex="-1">
                       <div class="modal-dialog modal-lg modal-dialog-centered">
                           <div class="modal-content">
                               <div class="modal-header">
                                   <h5 class="modal-title">
                                       <i class="bi bi-receipt me-2"></i>
                                       Detail Pesanan #<?= $order['id_pembelian']; ?>
                                   </h5>
                                   <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                               </div>
                               <div class="modal-body">
                                   <div class="table-responsive">
                                       <table class="table">
                                           <thead>
                                               <tr>
                                                   <th>Produk</th>
                                                   <th class="text-end">Harga</th>
                                                   <th class="text-center">Jumlah</th>
                                                   <th class="text-end">Subtotal</th>
                                               </tr>
                                           </thead>
                                           <tbody>
                                               <?php foreach ($details as $detail) : ?>
                                                   <tr>
                                                       <td><?= $detail['nama_barang']; ?></td>
                                                       <td class="text-end">
                                                           Rp <?= number_format($detail['harga_jual'], 0, ',', '.'); ?>
                                                       </td>
                                                       <td class="text-center"><?= $detail['jumlah']; ?></td>
                                                       <td class="text-end">
                                                           Rp <?= number_format($detail['subtotal'], 0, ',', '.'); ?>
                                                       </td>
                                                   </tr>
                                               <?php endforeach; ?>
                                           </tbody>
                                           <tfoot class="table-light">
                                               <tr>
                                                   <td colspan="3" class="text-end fw-bold">Total:</td>
                                                   <td class="text-end fw-bold">
                                                       Rp <?= number_format($order['jumlah_pembayaran'], 0, ',', '.'); ?>
                                                   </td>
                                               </tr>
                                               <tr>
                                                   <td colspan="3" class="text-end">Bayar:</td>
                                                   <td class="text-end">
                                                       Rp <?= number_format($order['bayar'], 0, ',', '.'); ?>
                                                   </td>
                                               </tr>
                                               <tr>
                                                   <td colspan="3" class="text-end">Kembalian:</td>
                                                   <td class="text-end">
                                                       Rp <?= number_format($order['kembalian'], 0, ',', '.'); ?>
                                                   </td>
                                               </tr>
                                           </tfoot>
                                       </table>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               <?php endforeach; ?>
           </div>
       <?php endif; ?>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>