<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
   header("Location: ../auth/login.php");
   exit;
}

if (!isset($_SESSION['cart'])) {
   $_SESSION['cart'] = [];
}

// Proses tambah ke keranjang
if (isset($_POST['action']) && $_POST['action'] == 'add') {
   $barang_id = $_POST['barang_id'];
   $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
   
   $barang = query("SELECT stok FROM tb_barang WHERE barang_id = $barang_id")[0];
   if ($qty > $barang['stok']) {
       $_SESSION['error'] = "Stok tidak mencukupi! Stok tersedia: " . $barang['stok'];
   } else {
       if (isset($_SESSION['cart'][$barang_id])) {
           $_SESSION['cart'][$barang_id] += $qty;
       } else {
           $_SESSION['cart'][$barang_id] = $qty;
       }
       $_SESSION['success'] = 'Produk berhasil ditambahkan ke keranjang';
   }
   header("Location: cart.php");
   exit;
}

// Proses update jumlah
if (isset($_POST['action']) && $_POST['action'] == 'update') {
   $barang_id = $_POST['barang_id'];
   $qty = (int)$_POST['qty'];
   
   $barang = query("SELECT stok FROM tb_barang WHERE barang_id = $barang_id")[0];
   if ($qty > $barang['stok']) {
       $_SESSION['error'] = "Stok tidak mencukupi! Stok tersedia: " . $barang['stok'];
   } else {
       if ($qty > 0) {
           $_SESSION['cart'][$barang_id] = $qty;
       } else {
           unset($_SESSION['cart'][$barang_id]);
       }
   }
   header("Location: cart.php");
   exit;
}

// Proses hapus item
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
   $barang_id = $_GET['id'];
   unset($_SESSION['cart'][$barang_id]);
   
   $_SESSION['success'] = 'Produk berhasil dihapus dari keranjang';
   header("Location: cart.php");
   exit;
}

// Ambil data keranjang
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
   $barang_ids = array_keys($_SESSION['cart']);
   $barang_ids_str = implode(',', $barang_ids);
   
   $cart_items = query("SELECT b.*, k.nama_kategori, m.nama_merk 
                       FROM tb_barang b 
                       LEFT JOIN tb_kategori k ON b.kategori_id = k.kategori_id 
                       LEFT JOIN tb_merk m ON b.merk_id = m.merk_id 
                       WHERE b.barang_id IN ($barang_ids_str)");
   
   foreach ($cart_items as $item) {
       $total += $item['harga_jual'] * $_SESSION['cart'][$item['barang_id']];
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Keranjang - Unesa Laptop</title>
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

       .cart-card {
           border-radius: 15px;
           border: none;
           box-shadow: 0 5px 15px rgba(0,0,0,0.05);
           background: white;
           margin-bottom: 20px;
       }

       .product-image {
           width: 100px;
           height: 100px;
           object-fit: contain;
           border-radius: 10px;
           padding: 10px;
           background: #f8f9fa;
           transition: transform 0.3s ease;
       }

       .product-image:hover {
           transform: scale(1.05);
       }

       .table > tbody > tr > td {
           vertical-align: middle;
           padding: 1rem;
       }

       .table > thead > tr > th {
           background: linear-gradient(45deg, #f8f9fa, #e9ecef);
           padding: 1rem;
           font-weight: 600;
       }

       .table > tbody > tr:hover {
           background-color: rgba(13,110,253,0.02);
       }

       .btn-primary {
           background: linear-gradient(45deg, #0d6efd, #0dcaf0);
           border: none;
           padding: 8px 20px;
           border-radius: 8px;
           transition: all 0.3s ease;
           font-weight: 500;
       }

       .btn-primary:hover {
           transform: translateY(-2px);
           box-shadow: 0 5px 15px rgba(13,110,253,0.2);
       }

       .btn-secondary {
           background: linear-gradient(45deg, #6c757d, #495057);
           border: none;
           padding: 8px 20px;
           border-radius: 8px;
           transition: all 0.3s ease;
           font-weight: 500;
       }

       .btn-secondary:hover {
           transform: translateY(-2px);
           box-shadow: 0 5px 15px rgba(108,117,125,0.2);
       }

       .btn-danger {
           background: linear-gradient(45deg, #dc3545, #b02a37);
           border: none;
           border-radius: 8px;
           transition: all 0.3s ease;
           padding: 0.5rem;
           display: inline-flex;
           align-items: center;
           justify-content: center;
       }

       .btn-danger:hover {
           transform: translateY(-2px);
           box-shadow: 0 5px 15px rgba(220,53,69,0.2);
       }

       .alert {
           border-radius: 12px;
           border: none;
           box-shadow: 0 2px 10px rgba(0,0,0,0.05);
       }

       .product-details {
           margin-left: 1rem;
       }

       .product-details h6 {
           margin-bottom: 0.5rem;
           font-weight: 600;
           color: #2c3e50;
       }

       .qty-input {
           max-width: 100px;
           border: 2px solid #e9ecef;
           border-radius: 8px;
           padding: 0.5rem;
           transition: border-color 0.3s ease;
       }

       .qty-input:focus {
           border-color: var(--primary-color);
           box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
       }

       .total-row {
           background: linear-gradient(45deg, #f8f9fa, #e9ecef);
           font-weight: bold;
       }

       .badge {
           padding: 0.5em 0.8em;
           border-radius: 6px;
       }

       .text-muted {
           font-size: 0.875rem;
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
                       <a class="nav-link" href="orders.php">Pesanan Saya</a>
                   </li>
               </ul>
               <ul class="navbar-nav">
                   <li class="nav-item">
                       <a class="nav-link active" href="cart.php">
                           <i class="bi bi-cart-fill me-1"></i>Keranjang
                           <?php if (count($_SESSION['cart']) > 0) : ?>
                               <span class="badge bg-danger rounded-pill"><?= count($_SESSION['cart']); ?></span>
                           <?php endif; ?>
                       </a>
                   </li>
               </ul>
           </div>
       </div>
   </nav>

   <div class="container py-4">
       <h2 class="mb-4 fw-bold">
           <i class="bi bi-cart-check me-2"></i>Keranjang Belanja
       </h2>

       <?php if (isset($_SESSION['success'])) : ?>
           <div class="alert alert-success alert-dismissible fade show" role="alert">
               <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success']; ?>
               <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
           </div>
           <?php unset($_SESSION['success']); ?>
       <?php endif; ?>

       <?php if (isset($_SESSION['error'])) : ?>
           <div class="alert alert-danger alert-dismissible fade show" role="alert">
               <i class="bi bi-exclamation-circle me-2"></i><?= $_SESSION['error']; ?>
               <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
           </div>
           <?php unset($_SESSION['error']); ?>
       <?php endif; ?>

       <?php if (empty($cart_items)) : ?>
           <div class="alert alert-info d-flex align-items-center">
               <i class="bi bi-info-circle me-2"></i>
               Keranjang belanja kosong. <a href="index.php" class="ms-2">Belanja sekarang</a>
           </div>
       <?php else : ?>
           <div class="cart-card">
               <div class="card-body">
                   <div class="table-responsive">
                       <table class="table table-hover mb-0">
                           <thead>
                               <tr>
                                   <th>Produk</th>
                                   <th>Harga</th>
                                   <th>Jumlah</th>
                                   <th>Subtotal</th>
                                   <th>Aksi</th>
                               </tr>
                           </thead>
                           <tbody>
                               <?php foreach ($cart_items as $item) : ?>
                               <tr>
                                   <td>
                                       <div class="d-flex align-items-center">
                                           <?php if ($item['gambar'] && file_exists("../assets/img/barang/" . $item['gambar'])) : ?>
                                               <img src="../assets/img/barang/<?= $item['gambar']; ?>" 
                                                    alt="<?= $item['nama_barang']; ?>"
                                                    class="product-image">
                                           <?php else : ?>
                                               <img src="../assets/img/no-image.jpg" 
                                                    alt="No Image"
                                                    class="product-image">
                                           <?php endif; ?>
                                           <div class="product-details">
                                               <h6><?= $item['nama_barang']; ?></h6>
                                               <small class="text-muted">
                                                   <i class="bi bi-tag me-1"></i><?= $item['nama_merk']; ?> | 
                                                   <i class="bi bi-laptop me-1"></i><?= $item['nama_kategori']; ?>
                                               </small>
                                           </div>
                                       </div>
                                   </td>
                                   <td class="text-primary fw-bold">
                                       Rp <?= number_format($item['harga_jual'], 0, ',', '.'); ?>
                                   </td>
                                   <td>
                                       <form action="" method="post" class="d-flex align-items-center">
                                           <input type="hidden" name="action" value="update">
                                           <input type="hidden" name="barang_id" value="<?= $item['barang_id']; ?>">
                                           <input type="number" class="form-control qty-input" name="qty" 
                                                  value="<?= $_SESSION['cart'][$item['barang_id']]; ?>" 
                                                  min="1" max="<?= $item['stok']; ?>"
                                                  onchange="this.form.submit()">
                                       </form>
                                   </td>
                                   <td class="text-primary fw-bold">
                                       Rp <?= number_format($item['harga_jual'] * $_SESSION['cart'][$item['barang_id']], 0, ',', '.'); ?>
                                   </td>
                                   <td>
                                       <a href="?action=remove&id=<?= $item['barang_id']; ?>" 
                                          class="btn btn-danger btn-sm"
                                          onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                           <i class="bi bi-trash"></i>
                                       </a>
                                   </td>
                               </tr>
                               <?php endforeach; ?>
                               <tr class="table-light fw-bold">
                                   <td colspan="3" class="text-end">Total:</td>
                                   <td class="text-primary">Rp <?= number_format($total, 0, ',', '.'); ?></td>
                                   <td></td>
                               </tr>
                           </tbody>
                       </table>
                   </div>

                   <div class="d-flex justify-content-between mt-4">
                       <a href="index.php" class="btn btn-secondary">
                           <i class="bi bi-arrow-left me-2"></i>Lanjut Belanja
                       </a>
                       <a href="checkout.php" class="btn btn-primary">
                           Checkout<i class="bi bi-arrow-right ms-2"></i>
                           </a>
                   </div>
               </div>
           </div>
       <?php endif; ?>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   <script>
       // Auto submit qty form on change
       document.querySelectorAll('.qty-input').forEach(input => {
           input.addEventListener('change', function() {
               this.form.submit();
           });
       });
   </script>
</body>
</html>