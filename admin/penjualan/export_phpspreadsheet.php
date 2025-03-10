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

// Cek autentikasi admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
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

// Ambil data penjualan
$query = "SELECT p.*, a.nama as admin_name, u.nama as nama_user, u.telepon, pb.jenis_pembayaran,
          (SELECT SUM(dp.subtotal) FROM tb_detail_penjualan dp WHERE dp.penjualan_id = p.penjualan_id) as total_penjualan 
          FROM tb_penjualan p 
          LEFT JOIN tb_admin a ON p.admin_id = a.admin_id
          LEFT JOIN tb_pembelian pmb ON p.penjualan_id = pmb.id_pembelian
          LEFT JOIN tb_user u ON pmb.user_id = u.user_id
          LEFT JOIN tb_pembayaran pb ON pmb.pembayaran_id = pb.pembayaran_id
          $where
          ORDER BY p.tanggal DESC";
$penjualan = query($query);

// Hitung total untuk ringkasan
$total_pendapatan = array_sum(array_column($penjualan, 'total'));

// Query untuk mendapatkan total produk terjual
$query_produk = "SELECT COALESCE(SUM(dp.jumlah), 0) as total 
                 FROM tb_detail_penjualan dp 
                 JOIN tb_penjualan p ON dp.penjualan_id = p.penjualan_id 
                 " . str_replace('p.tanggal', 'p.tanggal', $where);
$total_produk = query($query_produk)[0]['total'];

// Query untuk mendapatkan total customer
$query_customer = "SELECT COUNT(DISTINCT pmb.user_id) as total 
                  FROM tb_pembelian pmb 
                  JOIN tb_penjualan p ON pmb.id_pembelian = p.penjualan_id 
                  " . str_replace('p.tanggal', 'p.tanggal', $where);
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
    $sheet->setCellValue('D' . $row, $data['nama_user'] ?? 'User tidak ditemukan');
    $sheet->setCellValue('E' . $row, $data['telepon'] ?? '-');
    $sheet->setCellValue('F' . $row, $data['jenis_pembayaran'] ?? '-');
    $sheet->setCellValue('G' . $row, $data['total']);
    $sheet->setCellValue('H' . $row, $data['bayar']);
    $sheet->setCellValue('I' . $row, $data['kembalian']);
    $sheet->setCellValue('J' . $row, $data['admin_name']);
    
    // Format angka
    $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');
    
    $grand_total += $data['total'];
    $row++;
}

// Style borders untuk data
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

// Tanda tangan
$row += 2;
$sheet->setCellValue('H' . $row, '........................., ' . date('d F Y'));
$row++;
$sheet->setCellValue('H' . $row, 'Dibuat oleh,');
$row += 4;
$sheet->setCellValue('H' . $row, $_SESSION['nama'] ?? 'Admin');

// Atur header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_penjualan_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

// Output file Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;