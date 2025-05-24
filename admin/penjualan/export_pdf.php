<?php
session_start();
require_once '../../config/koneksi.php';

// Cek autentikasi admin
if (!isset($_SESSION['login']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'superadmin')) {
    header("Location: ../auth/adminlogin.php");
    exit;
}

// Include library TCPDF atau FPDF (pilih salah satu)
// Untuk TCPDF (lebih canggih, mendukung UTF-8):
// require_once '../../vendor/tecnickcom/tcpdf/tcpdf.php';

// Untuk FPDF (lebih ringan):
require_once '../../libs/fpdf/fpdf.php';

// Inisialisasi variabel filtering
$where = "";
$dari = "";
$sampai = "";
$periode_text = "Semua Data";

// Filter berdasarkan tanggal jika ada
if (isset($_GET['dari']) && isset($_GET['sampai'])) {
    $dari = $_GET['dari'];
    $sampai = $_GET['sampai'];
    if (!empty($dari) && !empty($sampai)) {
        $where = "WHERE DATE(p.tanggal) BETWEEN '$dari' AND '$sampai'";
        $periode_text = "Periode: " . date('d/m/Y', strtotime($dari)) . " - " . date('d/m/Y', strtotime($sampai));
    }
}

// Ambil parameter sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'p.tanggal';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Validasi kolom sorting untuk keamanan
$allowed_sort_columns = ['p.tanggal', 'u.nama', 'u.telepon', 'pb.jenis_pembayaran', 'p.total', 'p.bayar', 'p.kembalian', 'a.nama'];
if (!in_array($sort, $allowed_sort_columns)) {
    $sort = 'p.tanggal';
}

// Validasi order untuk keamanan
if (!in_array(strtoupper($order), ['ASC', 'DESC'])) {
    $order = 'DESC';
}

// Query untuk mendapatkan data penjualan
$query = "SELECT p.*, a.nama as admin_name, u.nama as nama_user, u.telepon, pb.jenis_pembayaran,
          (SELECT SUM(dp.subtotal) FROM tb_detail_penjualan dp WHERE dp.penjualan_id = p.penjualan_id) as total_penjualan 
          FROM tb_penjualan p 
          LEFT JOIN tb_admin a ON p.admin_id = a.admin_id
          LEFT JOIN tb_pembelian pmb ON p.id_pembelian = pmb.id_pembelian
          LEFT JOIN tb_user u ON pmb.user_id = u.user_id
          LEFT JOIN tb_pembayaran pb ON pmb.pembayaran_id = pb.pembayaran_id
          $where
          ORDER BY $sort $order";

$penjualan = query($query);

// Hitung statistik
$total_transaksi = count($penjualan);
$total_pendapatan = array_sum(array_column($penjualan, 'total'));

// Query untuk mendapatkan total produk terjual
$query_produk = "SELECT COALESCE(SUM(dp.jumlah), 0) as total 
                FROM tb_detail_penjualan dp 
                JOIN tb_penjualan p ON dp.penjualan_id = p.penjualan_id 
                " . (empty($where) ? "" : str_replace('WHERE', 'WHERE', $where));
$total_produk = query($query_produk)[0]['total'];

// Query untuk mendapatkan total customer
$query_customer = "SELECT COUNT(DISTINCT pmb.user_id) as total 
                  FROM tb_pembelian pmb 
                  JOIN tb_penjualan p ON pmb.id_pembelian = p.id_pembelian
                  " . (empty($where) ? "" : str_replace('WHERE', 'WHERE', $where));
$total_customer = query($query_customer)[0]['total'];

// Kelas PDF dengan header dan footer custom
class PDF extends FPDF
{
    private $title_text;
    private $periode_text;
    
    function __construct($title = '', $periode = '') {
        parent::__construct();
        $this->title_text = $title;
        $this->periode_text = $periode;
    }
    
    // Header halaman
    function Header()
    {
        // Logo (jika ada)
        // $this->Image('../../assets/images/logo.png', 10, 6, 30);
        
        // Font untuk header
        $this->SetFont('Arial', 'B', 16);
        
        // Judul
        $this->Cell(0, 10, 'LAPORAN DATA PENJUALAN', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 8, 'Sistem Informasi Penjualan', 0, 1, 'C');
        $this->Cell(0, 8, $this->periode_text, 0, 1, 'C');
        
        // Garis horizontal
        $this->SetLineWidth(0.5);
        $this->Line(10, 35, 200, 35);
        
        // Spasi
        $this->Ln(10);
    }
    
    // Footer halaman
    function Footer()
    {
        // Posisi 1.5 cm dari bawah
        $this->SetY(-15);
        
        // Garis horizontal
        $this->SetLineWidth(0.2);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        
        // Font untuk footer
        $this->SetFont('Arial', 'I', 8);
        
        // Nomor halaman dan tanggal
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . ' | Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 0, 'C');
    }
    
    // Fungsi untuk membuat tabel statistik
    function CreateStatsTable($total_transaksi, $total_pendapatan, $total_produk, $total_customer)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'RINGKASAN STATISTIK', 0, 1, 'L');
        $this->Ln(5);
        
        // Header tabel statistik
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(52, 58, 64); // Warna abu-abu gelap
        $this->SetTextColor(255, 255, 255); // Teks putih
        
        $this->Cell(95, 8, 'KETERANGAN', 1, 0, 'C', true);
        $this->Cell(95, 8, 'JUMLAH', 1, 1, 'C', true);
        
        // Reset warna teks
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 10);
        
        // Data statistik
        $stats = [
            ['Total Transaksi', number_format($total_transaksi, 0, ',', '.')],
            ['Total Pendapatan', 'Rp ' . number_format($total_pendapatan, 0, ',', '.')],
            ['Total Produk Terjual', number_format($total_produk, 0, ',', '.')],
            ['Total Customer', number_format($total_customer, 0, ',', '.')]
        ];
        
        $fill = false;
        foreach ($stats as $stat) {
            $this->SetFillColor(248, 249, 250); // Warna abu-abu muda
            $this->Cell(95, 8, $stat[0], 1, 0, 'L', $fill);
            $this->Cell(95, 8, $stat[1], 1, 1, 'R', $fill);
            $fill = !$fill;
        }
        
        $this->Ln(10);
    }
    
    // Fungsi untuk membuat header tabel data
    function CreateTableHeader()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'DETAIL DATA PENJUALAN', 0, 1, 'L');
        $this->Ln(5);
        
        // Header tabel
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(52, 58, 64); // Warna abu-abu gelap
        $this->SetTextColor(255, 255, 255); // Teks putih
        
        $this->Cell(10, 8, 'No', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Tanggal', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Pembeli', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Telepon', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Bayar', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Total', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Kembalian', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Admin', 1, 1, 'C', true);
        
        // Reset warna teks
        $this->SetTextColor(0, 0, 0);
    }
}

// Buat instance PDF
$pdf = new PDF('Laporan Penjualan', $periode_text);
$pdf->AddPage();

// Buat tabel statistik
$pdf->CreateStatsTable($total_transaksi, $total_pendapatan, $total_produk, $total_customer);

// Buat header tabel data
$pdf->CreateTableHeader();

// Isi tabel data
$pdf->SetFont('Arial', '', 7);
$no = 1;
$fill = false;

foreach ($penjualan as $row) {
    // Cek apakah perlu halaman baru
    if ($pdf->GetY() > 250) {
        $pdf->AddPage();
        $pdf->CreateTableHeader();
    }
    
    // Warna background bergantian
    if ($fill) {
        $pdf->SetFillColor(248, 249, 250);
    }
    
    // Data baris
    $pdf->Cell(10, 6, $no++, 1, 0, 'C', $fill);
    $pdf->Cell(25, 6, date('d/m/Y', strtotime($row['tanggal'])), 1, 0, 'C', $fill);
    
    // Potong nama jika terlalu panjang
    $nama = substr($row['nama_user'] ?? 'N/A', 0, 15);
    if (strlen($row['nama_user'] ?? '') > 15) $nama .= '...';
    $pdf->Cell(30, 6, $nama, 1, 0, 'L', $fill);
    
    // Potong telepon jika terlalu panjang
    $telepon = substr($row['telepon'] ?? '-', 0, 12);
    if (strlen($row['telepon'] ?? '') > 12) $telepon .= '...';
    $pdf->Cell(25, 6, $telepon, 1, 0, 'C', $fill);
    
    $pdf->Cell(20, 6, number_format($row['bayar'], 0, ',', '.'), 1, 0, 'R', $fill);
    $pdf->Cell(25, 6, number_format($row['total'], 0, ',', '.'), 1, 0, 'R', $fill);
    $pdf->Cell(25, 6, number_format($row['kembalian'], 0, ',', '.'), 1, 0, 'R', $fill);
    
    // Potong nama admin jika terlalu panjang
    $admin = substr($row['admin_name'] ?? 'N/A', 0, 10);
    if (strlen($row['admin_name'] ?? '') > 10) $admin .= '...';
    $pdf->Cell(20, 6, $admin, 1, 1, 'C', $fill);
    
    $fill = !$fill;
}

// Jika tidak ada data
if (empty($penjualan)) {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 20, 'Tidak ada data penjualan untuk ditampilkan.', 0, 1, 'C');
}

// Output PDF
$filename = 'Laporan_Penjualan_' . date('Y-m-d_H-i-s') . '.pdf';
$pdf->Output('D', $filename); // 'D' untuk download, 'I' untuk tampil di browser
exit;
?>