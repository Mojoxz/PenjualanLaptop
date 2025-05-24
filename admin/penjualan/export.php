<?php
session_start();
require_once '../../config/koneksi.php';

// Cek autentikasi admin
if (!isset($_SESSION['login']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'superadmin')) {
    header("Location: ../auth/adminlogin.php");
    exit;
}

// Inisialisasi variabel filtering
$where = "";
$dari = "";
$sampai = "";

// Filter berdasarkan tanggal jika ada
if (isset($_GET['dari']) && isset($_GET['sampai'])) {
    $dari = $_GET['dari'];
    $sampai = $_GET['sampai'];
    if (!empty($dari) && !empty($sampai)) {
        $where = "WHERE DATE(p.tanggal) BETWEEN '$dari' AND '$sampai'";
    }
}

// Ambil parameter sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'p.tanggal';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Validasi kolom sorting untuk keamanan
$allowedSortColumns = [
    'p.tanggal', 'u.nama', 'u.telepon', 'pb.jenis_pembayaran', 
    'p.total', 'p.bayar', 'p.kembalian', 'a.nama'
];

if (!in_array($sort, $allowedSortColumns)) {
    $sort = 'p.tanggal';
}

if (!in_array(strtoupper($order), ['ASC', 'DESC'])) {
    $order = 'DESC';
}

// Query untuk mendapatkan data penjualan
$query = "SELECT p.*, a.nama as admin_name, u.nama as nama_user, u.telepon, pb.jenis_pembayaran
          FROM tb_penjualan p 
          LEFT JOIN tb_admin a ON p.admin_id = a.admin_id
          LEFT JOIN tb_pembelian pmb ON p.id_pembelian = pmb.id_pembelian
          LEFT JOIN tb_user u ON pmb.user_id = u.user_id
          LEFT JOIN tb_pembayaran pb ON pmb.pembayaran_id = pb.pembayaran_id
          $where
          ORDER BY $sort $order";

$penjualan = query($query);

// Set header untuk download file Excel
$filename = "Data_Penjualan_" . date('Y-m-d_H-i-s') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Buat periode untuk judul laporan
$periode = "";
if (!empty($dari) && !empty($sampai)) {
    $periode = "Periode: " . date('d/m/Y', strtotime($dari)) . " - " . date('d/m/Y', strtotime($sampai));
} else {
    $periode = "Semua Data";
}

// Hitung statistik
$total_transaksi = count($penjualan);
$total_pendapatan = array_sum(array_column($penjualan, 'total'));

// Query untuk mendapatkan total produk terjual
$query_produk = "SELECT COALESCE(SUM(dp.jumlah), 0) as total 
                FROM tb_detail_penjualan dp 
                JOIN tb_penjualan p ON dp.penjualan_id = p.penjualan_id 
                " . (empty($where) ? "" : $where);
$result_produk = query($query_produk);
$total_produk = $result_produk[0]['total'];

// Query untuk mendapatkan total customer
$query_customer = "SELECT COUNT(DISTINCT pmb.user_id) as total 
                  FROM tb_pembelian pmb 
                  JOIN tb_penjualan p ON pmb.id_pembelian = p.id_pembelian
                  " . (empty($where) ? "" : $where);
$result_customer = query($query_customer);
$total_customer = $result_customer[0]['total'];

// Output HTML untuk Excel
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .company-name { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .report-title { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .period { font-size: 12px; margin-bottom: 15px; }
        .stats { margin-bottom: 20px; }
        .stats-table { border-collapse: collapse; margin-bottom: 15px; }
        .stats-table td { padding: 5px 10px; border: 1px solid #ccc; }
        .stats-label { font-weight: bold; background-color: #f0f0f0; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .currency { text-align: right; }
        .footer { margin-top: 20px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <!-- Header Laporan -->
    <div class="header">
        <div class="company-name">SISTEM PENJUALAN</div>
        <div class="report-title">LAPORAN DATA PENJUALAN</div>
        <div class="period"><?= $periode ?></div>
        <div style="font-size: 10px;">Dicetak pada: <?= date('d/m/Y H:i:s') ?></div>
    </div>

    <!-- Statistik -->
    <div class="stats">
        <table class="stats-table">
            <tr>
                <td class="stats-label">Total Transaksi</td>
                <td><?= number_format($total_transaksi, 0, ',', '.') ?></td>
                <td class="stats-label">Total Pendapatan</td>
                <td>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td class="stats-label">Total Produk Terjual</td>
                <td><?= number_format($total_produk, 0, ',', '.') ?></td>
                <td class="stats-label">Total Customer</td>
                <td><?= number_format($total_customer, 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>

    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
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
            <?php $no = 1; foreach ($penjualan as $row) : ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td class="text-center"><?= date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                <td><?= htmlspecialchars($row['nama_user'] ?? 'User tidak ditemukan'); ?></td>
                <td class="text-center"><?= htmlspecialchars($row['telepon'] ?? '-'); ?></td>
                <td class="text-center"><?= htmlspecialchars($row['jenis_pembayaran'] ?? '-'); ?></td>
                <td class="currency">Rp <?= number_format($row['total'], 0, ',', '.'); ?></td>
                <td class="currency">Rp <?= number_format($row['bayar'], 0, ',', '.'); ?></td>
                <td class="currency">Rp <?= number_format($row['kembalian'], 0, ',', '.'); ?></td>
                <td class="text-center"><?= htmlspecialchars($row['admin_name']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="5" class="text-center">TOTAL</td>
                <td class="currency">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh sistem pada <?= date('d/m/Y H:i:s') ?></p>
    </div>
</body>
</html>