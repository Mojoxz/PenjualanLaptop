<?php
session_start();
require_once '../../config/koneksi.php';

// Cek autentikasi admin
if (!isset($_SESSION['login']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'superadmin')) {
    header("Location: ../auth/adminlogin.php");
    exit;
}

// Filter berdasarkan tanggal jika ada
$where = "";
if (isset($_GET['dari']) && isset($_GET['sampai'])) {
    $dari = $_GET['dari'];
    $sampai = $_GET['sampai'];
    if (!empty($dari) && !empty($sampai)) {
        $where = "WHERE DATE(p.tanggal) BETWEEN '$dari' AND '$sampai'";
    }
}

// Mendapatkan parameter sort dan order jika ada
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'p.tanggal';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Validasi kolom dan order untuk keamanan
$allowed_columns = ['p.tanggal', 'p.penjualan_id', 'u.nama', 'u.telepon', 'pb.jenis_pembayaran', 'p.total', 'p.bayar', 'p.kembalian', 'a.nama'];
$sort_column = in_array($sort_column, $allowed_columns) ? $sort_column : 'p.tanggal';
$sort_order = in_array(strtoupper($sort_order), ['ASC', 'DESC']) ? strtoupper($sort_order) : 'DESC';

// Ambil data penjualan
$query = "SELECT p.*, a.nama as admin_name, u.nama as nama_user, u.telepon, pb.jenis_pembayaran,
          (SELECT SUM(dp.subtotal) FROM tb_detail_penjualan dp WHERE dp.penjualan_id = p.penjualan_id) as total_penjualan 
          FROM tb_penjualan p 
          LEFT JOIN tb_admin a ON p.admin_id = a.admin_id
          LEFT JOIN tb_pembelian pmb ON p.penjualan_id = pmb.id_pembelian
          LEFT JOIN tb_user u ON pmb.user_id = u.user_id
          LEFT JOIN tb_pembayaran pb ON pmb.pembayaran_id = pb.pembayaran_id
          $where
          ORDER BY $sort_column $sort_order";
$penjualan = query($query);

// Hitung total untuk ringkasan
$total_pendapatan = array_sum(array_column($penjualan, 'total'));

// Query untuk mendapatkan total produk terjual
$query_produk = "SELECT COALESCE(SUM(dp.jumlah), 0) as total 
                FROM tb_detail_penjualan dp 
                JOIN tb_penjualan p ON dp.penjualan_id = p.penjualan_id 
                " . (empty($where) ? "" : $where);
$total_produk = query($query_produk)[0]['total'];

// Query untuk mendapatkan total customer
$query_customer = "SELECT COUNT(DISTINCT pmb.user_id) as total 
                  FROM tb_pembelian pmb 
                  JOIN tb_penjualan p ON pmb.id_pembelian = p.penjualan_id 
                  " . (empty($where) ? "" : $where);
$total_customer = query($query_customer)[0]['total'];

// Judul periode laporan
$periode = "Semua Data";
if (!empty($dari) && !empty($sampai)) {
    $periode = "Periode: " . date('d/m/Y', strtotime($dari)) . " - " . date('d/m/Y', strtotime($sampai));
}

// Set header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_penjualan_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        h2, h3 {
            text-align: center;
            margin: 5px 0;
        }
        .summary {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .summary td {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <h2>LAPORAN PENJUALAN</h2>
    <h3><?= $periode ?></h3>
    
    <!-- Ringkasan -->
    <table class="summary" border="0">
        <tr>
            <td width="200">Total Transaksi</td>
            <td>: <?= count($penjualan) ?></td>
        </tr>
        <tr>
            <td>Total Pendapatan</td>
            <td>: Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>Total Produk Terjual</td>
            <td>: <?= $total_produk ?></td>
        </tr>
        <tr>
            <td>Total Customer</td>
            <td>: <?= $total_customer ?></td>
        </tr>
    </table>
    
    <!-- Tabel Data Penjualan -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>ID Penjualan</th>
                <th>Pembeli</th>
                <th>Telepon</th>
                <th>Jenis Pembayaran</th>
                <th>Total</th>
                <th>Bayar</th>
                <th>Kembalian</th>
                <th>Admin</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; $grand_total = 0; foreach ($penjualan as $row) : ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                <td><?= $row['penjualan_id'] ?></td>
                <td><?= $row['nama_user'] ?? 'User tidak ditemukan' ?></td>
                <td><?= $row['telepon'] ?? '-' ?></td>
                <td><?= $row['jenis_pembayaran'] ?? '-' ?></td>
                <td class="text-right">Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($row['bayar'], 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($row['kembalian'], 0, ',', '.') ?></td>
                <td><?= $row['admin_name'] ?></td>
            </tr>
            <?php $grand_total += $row['total']; endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">GRAND TOTAL</th>
                <th class="text-right">Rp <?= number_format($grand_total, 0, ',', '.') ?></th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
    
    <!-- Tanda Tangan -->
    <div style="margin-top: 30px; text-align: right;">
        <p>........................., <?= date('d F Y') ?><br>
        Dibuat oleh,<br><br><br><br>
        <?= $_SESSION['nama'] ?? 'Admin' ?></p>
    </div>
</body>
</html>