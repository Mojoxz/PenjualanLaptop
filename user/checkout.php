<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Cek keranjang
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$user = query("SELECT * FROM tb_user WHERE user_id = $user_id")[0];

// Ambil data metode pembayaran
$payments = query("SELECT * FROM tb_pembayaran");

// Hitung total dan ambil detail barang di keranjang
$total = 0;
$cart_items = [];
foreach ($_SESSION['cart'] as $barang_id => $qty) {
    $barang = query("SELECT * FROM tb_barang WHERE barang_id = $barang_id")[0];
    $subtotal = $barang['harga_jual'] * $qty;
    $total += $subtotal;
    
    $cart_items[] = [
        'barang' => $barang,
        'qty' => $qty,
        'subtotal' => $subtotal
    ];
}

// Proses checkout
if (isset($_POST['checkout'])) {
    $pembayaran_id = $_POST['pembayaran_id'];
    $bayar = str_replace(['Rp', '.', ','], '', $_POST['bayar']);
    $kembalian = $bayar - $total;

    // Validasi pembayaran
    if ($bayar < $total) {
        $error = "Pembayaran kurang dari total belanja!";
    } else {
        mysqli_begin_transaction($conn);
        try {
            // Insert pembelian
            $data_pembelian = [
                'user_id' => $user_id,
                'pembayaran_id' => $pembayaran_id,
                'tanggal' => date('Y-m-d'),
                'bayar' => $bayar,
                'jumlah_pembayaran' => $total,
                'kembalian' => $kembalian
            ];

            if (tambah('tb_pembelian', $data_pembelian)) {
                $id_pembelian = mysqli_insert_id($conn);
                
                $data_penjualan = [
                    'admin_id' => 1,
                    'tanggal' => date('Y-m-d H:i:s'),
                    'bayar' => $bayar,
                    'total' => $total,
                    'kembalian' => $kembalian
                ];

                if (tambah('tb_penjualan', $data_penjualan)) {
                    $penjualan_id = mysqli_insert_id($conn);

                    foreach ($cart_items as $item) {
                        $barang = $item['barang'];
                        $qty = $item['qty'];
                        $subtotal = $item['subtotal'];

                        // Detail pembelian
                        tambah('tb_detail_pembelian', [
                            'barang_id' => $barang['barang_id'],
                            'id_pembelian' => $id_pembelian,
                            'jumlah' => $qty,
                            'subtotal' => $subtotal
                        ]);

                        // Detail penjualan
                        tambah('tb_detail_penjualan', [
                            'penjualan_id' => $penjualan_id,
                            'barang_id' => $barang['barang_id'],
                            'jumlah' => $qty,
                            'subtotal' => $subtotal
                        ]);

                        // Update stok
                        $stok_baru = $barang['stok'] - $qty;
                        if ($stok_baru < 0) {
                            throw new Exception("Stok barang {$barang['nama_barang']} tidak mencukupi!");
                        }
                        ubah('tb_barang', ['stok' => $stok_baru], "barang_id = {$barang['barang_id']}");
                    }

                    mysqli_commit($conn);
                    unset($_SESSION['cart']);

                    $_SESSION['success'] = "Pembelian berhasil! Order ID: #$id_pembelian";
                    header("Location: orders.php");
                    exit;
                }
            }
            
            mysqli_rollback($conn);
            $error = "Gagal memproses pembelian!";
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Unesa Laptop</title>
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
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1.2rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table > thead {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        }

        .table > thead th {
            font-weight: 600;
            color: #2c3e50;
            border: none;
            padding: 15px;
        }

        .table > tbody td {
            padding: 15px;
            vertical-align: middle;
        }

        .form-select, 
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
        }

        .input-group-text {
            border: 2px solid #e9ecef;
            background: #f8f9fa;
            border-radius: 10px 0 0 10px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #0dcaf0);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13,110,253,0.2);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
        }

        tfoot {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            font-weight: bold;
        }

        tfoot td {
            padding: 15px !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-laptop me-2"></i>Unesa Laptop
            </a>
        </div>
    </nav>

    <div class="container my-4">
        <h2 class="mb-4 fw-bold">
            <i class="bi bi-credit-card me-2"></i>Checkout
        </h2>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i><?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-person-lines-fill me-2"></i>Detail Pengiriman
                        </h5>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-1"><strong>Nama:</strong> <?= $user['nama']; ?></p>
                            <p class="mb-1"><strong>Alamat:</strong> <?= $user['alamat']; ?></p>
                            <p class="mb-0"><strong>Telepon:</strong> <?= $user['telepon']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-cart-check me-2"></i>Detail Pesanan
                        </h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td><?= $item['barang']['nama_barang']; ?></td>
                                        <td>Rp <?= number_format($item['barang']['harga_jual'], 0, ',', '.'); ?></td>
                                        <td><?= $item['qty']; ?></td>
                                        <td>Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end">Total:</td>
                                        <td>Rp <?= number_format($total, 0, ',', '.'); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-wallet2 me-2"></i>Pembayaran
                        </h5>
                        <form action="" method="post" class="needs-validation" novalidate>
                            <input type="hidden" name="total" value="<?= $total; ?>">

                            <div class="mb-3">
                                <label class="form-label">Jenis Pembayaran</label>
                                <select class="form-select" name="pembayaran_id" required>
                                    <option value="">Pilih Jenis Pembayaran</option>
                                    <?php foreach ($payments as $payment): ?>
                                        <option value="<?= $payment['pembayaran_id']; ?>">
                                            <?= $payment['jenis_pembayaran']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Jumlah Bayar</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control rupiah-input" name="bayar" 
                                           required data-min="<?= $total ?>">
                                </div>
                            </div>

                            <button type="submit" name="checkout" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-2"></i>Proses Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.rupiah-input').forEach(function(input) {
        input.addEventListener('keyup', function(e) {
            let value = this.value.replace(/[^0-9]/g, '');
            this.value = formatRupiah(value);
            
            const min = parseInt(this.dataset.min);
            const current = parseInt(value);
            
            if (current < min) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });

    function formatRupiah(angka) {
        var number_string = angka.toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }
    </script>
</body>
</html>