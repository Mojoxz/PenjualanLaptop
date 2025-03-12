<?php
session_start();
require_once '../../config/koneksi.php';

// Cek kebutuhan composer - tambahkan jika belum ada
if (!file_exists('../../vendor/autoload.php')) {
    echo "PhpSpreadsheet belum diinstal. Silakan instal dengan composer.";
    echo "<br><pre>composer require phpoffice/phpspreadsheet</pre>";
    exit;
}

require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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

// Cek apakah semua penjualan memiliki id_pembelian
$query_check = "SELECT COUNT(*) as count FROM tb_penjualan WHERE id_pembelian IS NULL";
$has_null_pembelian = query($query_check)[0]['count'] > 0;

// Ambil data penjualan dengan JOIN yang benar, mengatasi masalah missing user
// Pendekatan ini menggunakan LEFT JOIN untuk memastikan semua penjualan ditampilkan
// bahkan jika tidak ada user yang terkait
$query = "SELECT p.*, 
          a.nama as admin_name, 
          u.nama as nama_user, 
          u.telepon, 
          u.user_id,
          pb.jenis_pembayaran,
          (SELECT SUM(dp.subtotal) FROM tb_detail_penjualan dp WHERE dp.penjualan_id = p.penjualan_id) as total_penjualan
          FROM tb_penjualan p 
          LEFT JOIN tb_admin a ON p.admin_id = a.admin_id
          LEFT JOIN tb_pembelian pmb ON p.id_pembelian = pmb.id_pembelian
          LEFT JOIN tb_user u ON pmb.user_id = u.user_id
          LEFT JOIN tb_pembayaran pb ON pmb.pembayaran_id = pb.pembayaran_id
          $where
          ORDER BY $sort_column $sort_order";
$penjualan = query($query);

// Hitung total untuk ringkasan
$total_pendapatan = 0;
foreach ($penjualan as $data) {
    $total_pendapatan += ($data['total_penjualan'] ?? $data['total'] ?? 0);
}

// Query untuk mendapatkan total produk terjual
$query_produk = "SELECT COALESCE(SUM(dp.jumlah), 0) as total 
                FROM tb_detail_penjualan dp 
                JOIN tb_penjualan p ON dp.penjualan_id = p.penjualan_id 
                " . (empty($where) ? "" : $where);
$total_produk = query($query_produk)[0]['total'];

// Query untuk mendapatkan total customer
$query_customer = "SELECT COUNT(DISTINCT pmb.user_id) as total 
                  FROM tb_pembelian pmb 
                  JOIN tb_penjualan p ON pmb.id_pembelian = p.id_pembelian 
                  " . (empty($where) ? "" : $where);
$total_customer = query($query_customer)[0]['total'];

// Judul periode laporan
$periode = "Semua Data";
if (!empty($dari) && !empty($sampai)) {
    $periode = "Periode: " . date('d/m/Y', strtotime($dari)) . " - " . date('d/m/Y', strtotime($sampai));
}

// Buat objek spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Penjualan');

// Set lebar kolom
$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(18);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(25);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(18);
$sheet->getColumnDimension('G')->setWidth(18);
$sheet->getColumnDimension('H')->setWidth(18);
$sheet->getColumnDimension('I')->setWidth(18);
$sheet->getColumnDimension('J')->setWidth(20);

// Judul laporan
$sheet->setCellValue('A1', 'LAPORAN PENJUALAN');
$sheet->mergeCells('A1:J1');
$sheet->setCellValue('A2', $periode);
$sheet->mergeCells('A2:J2');

// Style judul
$styleJudul = [
    'font' => [
        'bold' => true,
        'size' => 14,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];
$sheet->getStyle('A1:J2')->applyFromArray($styleJudul);

// Ringkasan
$sheet->setCellValue('A4', 'Total Transaksi');
$sheet->setCellValue('B4', ': ' . count($penjualan));
$sheet->setCellValue('A5', 'Total Pendapatan');
$sheet->setCellValue('B5', ': Rp ' . number_format($total_pendapatan, 0, ',', '.'));
$sheet->setCellValue('A6', 'Total Produk Terjual');
$sheet->setCellValue('B6', ': ' . $total_produk);
$sheet->setCellValue('A7', 'Total Customer');
$sheet->setCellValue('B7', ': ' . $total_customer);

// Tampilkan informasi jika ada penjualan tanpa user
if ($has_null_pembelian) {
    $sheet->setCellValue('A8', 'Catatan');
    $sheet->setCellValue('B8', ': Beberapa penjualan mungkin merupakan transaksi langsung tanpa akun pengguna.');
    $sheet->getStyle('A8')->applyFromArray($styleRingkasan);
}

// Style ringkasan
$styleRingkasan = [
    'font' => [
        'bold' => true,
    ],
];
$sheet->getStyle('A4:A7')->applyFromArray($styleRingkasan);

// Header tabel
$sheet->setCellValue('A9', 'No');
$sheet->setCellValue('B9', 'Tanggal');
$sheet->setCellValue('C9', 'ID Penjualan');
$sheet->setCellValue('D9', 'Pembeli');
$sheet->setCellValue('E9', 'Telepon');
$sheet->setCellValue('F9', 'Jenis Pembayaran');
$sheet->setCellValue('G9', 'Total');
$sheet->setCellValue('H9', 'Bayar');
$sheet->setCellValue('I9', 'Kembalian');
$sheet->setCellValue('J9', 'Admin');

// Style header tabel
$styleHeader = [
    'font' => [
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'D9D9D9',
        ],
    ],
];
$sheet->getStyle('A9:J9')->applyFromArray($styleHeader);

// Isi tabel
$row = 10;
$no = 1;
$grand_total = 0;

foreach ($penjualan as $data) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($data['tanggal'])));
    $sheet->setCellValue('C' . $row, $data['penjualan_id']);
    
    // Tampilkan "Penjualan Langsung" atau "Customer Walk-in" untuk user yang tidak ditemukan
    // sebagai alternatif label yang lebih deskriptif
    if (empty($data['nama_user']) || $data['nama_user'] === null) {
        $sheet->setCellValue('D' . $row, 'Penjualan Langsung');
        $sheet->setCellValue('E' . $row, '-');
    } else {
        $sheet->setCellValue('D' . $row, $data['nama_user']);
        $sheet->setCellValue('E' . $row, $data['telepon'] ?? '-');
    }
    
    $sheet->setCellValue('F' . $row, $data['jenis_pembayaran'] ?? 'Tunai');
    
    // Gunakan total_penjualan jika tersedia, jika tidak gunakan total dari tabel penjualan
    $total_sale = $data['total_penjualan'] ?? $data['total'] ?? 0;
    $sheet->setCellValue('G' . $row, $total_sale);
    $sheet->setCellValue('H' . $row, $data['bayar'] ?? 0);
    $sheet->setCellValue('I' . $row, $data['kembalian'] ?? 0);
    $sheet->setCellValue('J' . $row, $data['admin_name'] ?? '-');
    
    // Format angka untuk nilai rupiah
    $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');
    
    // Alignment
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('G' . $row . ':I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    
    $grand_total += $total_sale;
    $row++;
}

// Style borders untuk semua data
$styleData = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];
$sheet->getStyle('A10:J' . ($row - 1))->applyFromArray($styleData);

// Grand Total
$sheet->setCellValue('A' . $row, 'GRAND TOTAL');
$sheet->mergeCells('A' . $row . ':F' . $row);
$sheet->setCellValue('G' . $row, $grand_total);
$sheet->mergeCells('H' . $row . ':J' . $row);

// Style Grand Total
$styleGrandTotal = [
    'font' => [
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'F2F2F2',
        ],
    ],
];
$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($styleGrandTotal);
$sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
$sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Tanda tangan
$row += 2;
$sheet->setCellValue('H' . $row, '........................., ' . date('d F Y'));
$row++;
$sheet->setCellValue('H' . $row, 'Dibuat oleh,');
$row += 4;
$sheet->setCellValue('H' . $row, $_SESSION['nama'] ?? 'Admin');

// Auto filter untuk header tabel
$sheet->setAutoFilter('A9:J9');

// Freeze pane pada header tabel
$sheet->freezePane('A10');

// Atur header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_penjualan_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

// Output file Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;