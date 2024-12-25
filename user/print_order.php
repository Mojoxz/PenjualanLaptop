<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Cek parameter id
if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil data pembelian
$order = query("SELECT p.*, pb.jenis_pembayaran, u.nama as nama_user, u.alamat, u.telepon 
               FROM tb_pembelian p
               LEFT JOIN tb_pembayaran pb ON p.pembayaran_id = pb.pembayaran_id
               LEFT JOIN tb_user u ON p.user_id = u.user_id 
               WHERE p.id_pembelian = $order_id AND p.user_id = $user_id")[0];

// Ambil detail pembelian
$details = query("SELECT dp.*, b.nama_barang, b.harga_jual 
                 FROM tb_detail_pembelian dp 
                 JOIN tb_barang b ON dp.barang_id = b.barang_id 
                 WHERE dp.id_pembelian = $order_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian #<?= $order_id ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .invoice-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .store-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px dashed #ddd;
        }
        .totals {
            text-align: right;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #000;
        }
        @media print {
            @page {
                margin: 0;
                size: 80mm 297mm;
            }
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-title">
        <h2>TOKO LAPTOP</h2>
    </div>

    <div class="store-info">
        Jl. Contoh No. 123<br>
        Telp: (021) 12345678<br>
        Email: info@tokolaptop.com
    </div>

    <div class="invoice-details">
        <div>No. Invoice: #<?= $order_id ?></div>
        <div>Tanggal: <?= date('d/m/Y H:i', strtotime($order['tanggal'])) ?></div>
        <div>Kasir: Admin</div>
    </div>

    <div class="customer-info">
        <div>Pembeli: <?= $order['nama_user'] ?></div>
        <div>Telp: <?= $order['telepon'] ?></div>
        <div>Alamat: <?= $order['alamat'] ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $item): ?>
            <tr>
                <td><?= $item['nama_barang'] ?></td>
                <td><?= $item['jumlah'] ?></td>
                <td>Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <div>Total: Rp <?= number_format($order['jumlah_pembayaran'], 0, ',', '.') ?></div>
        <div>Bayar: Rp <?= number_format($order['bayar'], 0, ',', '.') ?></div>
        <div>Kembali: Rp <?= number_format($order['kembalian'], 0, ',', '.') ?></div>
    </div>

    <div class="footer">
        Terima kasih telah berbelanja<br>
        Barang yang sudah dibeli tidak dapat ditukar/dikembalikan
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>