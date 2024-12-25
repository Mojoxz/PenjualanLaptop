<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Cek parameter id
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$penjualan_id = $_GET['id'];

// Query untuk mendapatkan detail penjualan dengan info user dan pembayaran
$query = "SELECT p.*, a.nama as admin_name, u.nama as nama_user, u.alamat, u.telepon, 
          pb.jenis_pembayaran, GROUP_CONCAT(DISTINCT b.nama_barang SEPARATOR ', ') as produk_dibeli
          FROM tb_penjualan p 
          LEFT JOIN tb_admin a ON p.admin_id = a.admin_id
          LEFT JOIN tb_pembelian pmb ON p.penjualan_id = pmb.id_pembelian
          LEFT JOIN tb_user u ON pmb.user_id = u.user_id
          LEFT JOIN tb_pembayaran pb ON pmb.pembayaran_id = pb.pembayaran_id
          LEFT JOIN tb_detail_penjualan dp ON p.penjualan_id = dp.penjualan_id
          LEFT JOIN tb_barang b ON dp.barang_id = b.barang_id
          WHERE p.penjualan_id = $penjualan_id
          GROUP BY p.penjualan_id";
$penjualan = query($query)[0];

// Ambil detail produk yang dibeli
$detail_query = "SELECT dp.*, b.nama_barang, b.harga_jual, b.gambar,
                k.nama_kategori, m.nama_merk 
                FROM tb_detail_penjualan dp 
                JOIN tb_barang b ON dp.barang_id = b.barang_id 
                LEFT JOIN tb_kategori k ON b.kategori_id = k.kategori_id 
                LEFT JOIN tb_merk m ON b.merk_id = m.merk_id 
                WHERE dp.penjualan_id = $penjualan_id";
$details = query($detail_query);

include_once '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Penjualan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Penjualan</a></li>
        <li class="breadcrumb-item active">Detail Penjualan</li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-info-circle me-1"></i>
                            Detail Transaksi #<?= $penjualan_id ?>
                        </div>
                        <div>
                            <a href="cetak.php?id=<?= $penjualan_id ?>" target="_blank" class="btn btn-primary btn-sm">
                                <i class="bi bi-printer"></i> Cetak Nota
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <!-- Informasi Pembeli -->
                        <div class="col-sm-4">
                            <h6 class="mb-3">Data Pembeli:</h6>
                            <div class="mb-2">
                                <strong>Nama:</strong><br>
                                <?= htmlspecialchars($penjualan['nama_user'] ?? 'User tidak ditemukan'); ?>
                            </div>
                            <div class="mb-2">
                                <strong>Telepon:</strong><br>
                                <?= htmlspecialchars($penjualan['telepon'] ?? '-'); ?>
                            </div>
                            <div class="mb-2">
                                <strong>Alamat:</strong><br>
                                <?= htmlspecialchars($penjualan['alamat'] ?? '-'); ?>
                            </div>
                        </div>

                        <!-- Informasi Transaksi -->
                        <div class="col-sm-4">
                            <h6 class="mb-3">Detail Transaksi:</h6>
                            <div class="mb-2">
                                <strong>Tanggal:</strong><br>
                                <?= date('d/m/Y H:i', strtotime($penjualan['tanggal'])); ?>
                            </div>
                            <div class="mb-2">
                                <strong>Admin:</strong><br>
                                <?= htmlspecialchars($penjualan['admin_name']); ?>
                            </div>
                            <div class="mb-2">
                                <strong>Metode Pembayaran:</strong><br>
                                <?= htmlspecialchars($penjualan['jenis_pembayaran'] ?? '-'); ?>
                            </div>
                        </div>

                        <!-- Informasi Pembayaran -->
                        <div class="col-sm-4">
                            <h6 class="mb-3">Pembayaran:</h6>
                            <div class="mb-2">
                                <strong>Total:</strong><br>
                                Rp <?= number_format($penjualan['total'], 0, ',', '.'); ?>
                            </div>
                            <div class="mb-2">
                                <strong>Bayar:</strong><br>
                                Rp <?= number_format($penjualan['bayar'], 0, ',', '.'); ?>
                            </div>
                            <div class="mb-2">
                                <strong>Kembalian:</strong><br>
                                Rp <?= number_format($penjualan['kembalian'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Produk -->
                    <h6 class="mb-3">Detail Produk:</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Merk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($details as $item) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <?php if ($item['gambar'] && file_exists("../../assets/img/barang/" . $item['gambar'])) : ?>
                                            <img src="../../assets/img/barang/<?= $item['gambar']; ?>" 
                                                 alt="<?= $item['nama_barang']; ?>" 
                                                 class="img-thumbnail"
                                                 style="max-width: 50px;">
                                        <?php else : ?>
                                            <img src="../../assets/img/no-image.jpg" 
                                                 alt="No Image" 
                                                 class="img-thumbnail"
                                                 style="max-width: 50px;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($item['nama_barang']); ?></td>
                                    <td><?= htmlspecialchars($item['nama_kategori']); ?></td>
                                    <td><?= htmlspecialchars($item['nama_merk']); ?></td>
                                    <td>Rp <?= number_format($item['harga_jual'], 0, ',', '.'); ?></td>
                                    <td><?= $item['jumlah']; ?></td>
                                    <td>Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>Rp <?= number_format($penjualan['total'], 0, ',', '.'); ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-end">Bayar:</td>
                                    <td>Rp <?= number_format($penjualan['bayar'], 0, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-end">Kembalian:</td>
                                    <td>Rp <?= number_format($penjualan['kembalian'], 0, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="index.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>