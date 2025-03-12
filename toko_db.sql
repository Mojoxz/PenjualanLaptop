-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2025 at 12:34 PM
-- Server version: 10.6.15-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','superadmin') NOT NULL DEFAULT 'admin',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`admin_id`, `username`, `password`, `nama`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', 'mojo', 'mojo@admin.com', 'superadmin', 1, '2025-03-10 13:53:02', '2025-03-10 14:02:14'),
(2, 'firman', 'admin123', 'firman', 'firman@admin.com', 'admin', 1, '2025-03-10 13:53:02', '2025-03-10 14:01:50');

-- --------------------------------------------------------

--
-- Table structure for table `tb_barang`
--

CREATE TABLE `tb_barang` (
  `barang_id` int(11) NOT NULL,
  `merk_id` int(11) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `jenis_barang` varchar(255) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `harga_beli` decimal(10,2) DEFAULT NULL,
  `harga_jual` decimal(10,2) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_barang`
--

INSERT INTO `tb_barang` (`barang_id`, `merk_id`, `kategori_id`, `nama_barang`, `jenis_barang`, `gambar`, `harga_beli`, `harga_jual`, `stok`) VALUES
(1, 1, 1, 'ASUS ROG ZEPHYRUS G16 GA605WV-R946OL7G-OM RYZEN AI 9HX 370', 'Processor Onboard : AMD Ryzen™ AI 9 HX 370 Processor 2.0GHz (36MB Cache, up to 5.1GHz, 12 cores, 24 Threads) AMD Ryzen™ AI up to 81 TOPs\r\nMemori Standar : 32GB LPDDR5X\r\nTipe Grafis : NVIDIA® GeForce RTX™ 4050 Laptop GPU 6GB GDDR6\r\nROG Boost : ROG Boost: 1', '1733467296.jpg', 25000000.00, 30000000.00, 1),
(2, 3, 1, 'HP PAVILION GAMING LAPTOP 15-EC2047AX RYZEN 5-5600H', 'Ryzen™ processor\r\nWindows 10 Home Single Language 64\r\n15.6″ diagonal, FHD (1920 x 1080), 144 Hz, IPS, micro-edge, anti-glare, 250 nits, 45% NTSC\r\n16 GB DDR4-3200 MHz RAM (2 x 8 GB)\r\n512 GB PCIe® NVMe™ M.2 SSD\r\nGame Ready. Performance Ready.\r\nNVIDIA® GeFor', '1733467232.jpg', 9000000.00, 10000000.00, 2),
(3, 2, 1, 'LENOVO LAPTOP IDEAPAD GAMING 3-B7ID RYZEN 7-6800', 'Processor Onboard : AMD Ryzen™ 7 6800H Mobile Processor (8-core/16-thread, 20MB cache, up to 4.7 GHz max boost)\r\nDisplay : 15.6″ FHD (1920×1080) IPS 300nits Anti-glare, 165Hz, 100% sRGB, DC dimmer\r\nMemori Standar : 8GB DDR5 4800Mhz\r\nHard Disk : 512GB SSD ', '1733467131.jpg', 15990000.00, 16499999.00, 5),
(4, 1, 2, 'ASUS ZENBOOK S UX5304MA-OLEDS712', 'Processor Onboard : Intel® Core™ Ultra 7 Processor 155U 1.7 GHz (12MB Cache, up to 4.8 GHz, 12 cores, 14 Threads), Intel® AI Boost NPU\r\nMemori Standar : 32GB LPDDR5X on board\r\nHard Disk : 1TB M.2 NVMe™ PCIe® 4.0 SSD\r\nTipe Grafis : Intel® Graphics\r\nUkuran ', '1733467041.jpg', 10000000.00, 12599900.00, 5),
(6, 4, 2, 'Laptop Xiaomi Redmibook 15', 'Prosesor	Intel Core i3-1115G4 Dual Core up to 4.10 GHz\r\nVGA	Integrated Intel UHD Graphics\r\nRAM	8GB DDR4 3200 MHz\r\nStorage	SSD 256 GB/512 GB\r\nLayar	TN Panel 15.6 inci Full HD 1920 x 1080\r\nSpeaker	Stereo 2x 2W Audio DTS\r\nWebcam	HD 720p\r\nBaterai	46 Wh\r\nKeybo', '1733304650.jpg', 5260000.00, 6000000.00, 11),
(7, 5, 1, 'Axioo Pongo 725', '🌟 Spesifikasi Produk :\r\n✅ Processor: Intel Core I7 12650H (3.50GHz UPTO MAX 4.70GHz)\r\n✅ Ram : 16GB I 32GB I 64GB DDR4\r\n✅ Storage : 512GB I 1TB SSD M.2 2280 PCIe® NVMe®\r\n✅ Graphics : Nvidia Geforce RTX2050-4GB\r\n✅ Display : 15.6 Full HD IPS (1920 x 1080) re', '1733304391.jpg', 8000000.00, 10000000.00, 1),
(8, 1, 1, 'ASUS LAPTOP ROG STRIX-G G513IH-R765B6T', 'Processor Onboard : AMD Ryzen™ 7 4800H Processor 2.9 GHz (8M Cache, up to 4.2 GHz)\r\nMemori Standar : 8 GB DDR4 3200MHz\r\nTipe Grafis : NVIDIA® GeForce RTX™ 1650 Laptop GPU 4GB GDDR6 With ROG Boost up to 1615MHz at 50W (65W with Dynamic Boost)\r\nUkuran Layar', '1733465947.jpg', 10000000.00, 12000000.00, 10),
(11, 3, 3, 'HP LAPTOP 250-G8 [3V356PA] i3-1115G4', 'Processor Onboard : Intel® Core™ i3-1115G4 Processor (6MB Cache, up to 4.1 GHz)\r\nMemori Standar : 4 GB DDR4\r\nTipe Grafis : Intel® HD Graphics 620\r\nDisplay : 15,6″ diagonal HD SVA eDP anti-glare WLED-backlit, 220 cd/m², 67% sRGB (1366 x 768)\r\nAudio : 2 Int', '1733466132.jpg', 6000000.00, 7000000.00, 9),
(12, 2, 2, 'LENOVO THINKPAD E14 GEN6-5BID ULTRA 7', 'Processor Onboard : Intel® Core™ Ultra 7 155U, 12C (2P + 8E + 2LPE) / 14T, Max Turbo up to 4.8GHz, 12MB\r\nMemori Standar : 16GB SO-DIMM DDR5-5600\r\nTipe Grafis : Integrated Intel® Graphics\r\nUkuran Layar : 14″ WUXGA (1920×1200) IPS 300nits Anti-glare, 45% NT', '1733467373.jpg', 20999000.00, 22000000.00, 11),
(13, 10, 1, 'ACER GAMING LAPTOP NITRO AN515-57-921P i9-11900H', 'Processor : Intel® Core™ i9-11900H processor (24MB cache, up to 4.80Ghz)\r\nMemory : 16GB DDR4 3200Mhz\r\nStorage : 512GB SSD NVMe\r\nGraphics : NVIDIA® GeForce® RTX 3060 with 6GB of GDDR6\r\nDisplay : 15.6″ display with IPS (In-Plane Switching) technology, QHD 1', '1733467476.jpg', 20199000.00, 22000000.00, 11),
(14, 4, 3, 'Xiaomi Redmibook Intel i5-10210U', 'Merek: Xiaomi\r\nKlasifikasi: Ultrabook\r\nBahan penutup belakang: Semua Logam\r\nJenis baterai: Baterai Polimer Lithium-ion\r\n\r\nInti: Quad Core\r\nCPU: Intel Core i5-10210U 1.6GHz, up to 4.2GHz\r\nCPU Merek: Intel\r\nKecepatan Prosesor: 2.0GHz, Turbo 4.1GHz, Level 3 ', '1733539167.jpg', 10000000.00, 12000000.00, 11),
(15, 1, 1, 'ASUS LAPTOP ROG STRIX-G G513QC', 'Processor Onboard : AMD Ryzen™ 5 5600H Processor 3.3 GHz (16M Cache, up to 4.2 GHz)\r\nMemori Standar : 8 GB DDR4 3200MHz\r\nTipe Grafis : NVIDIA® GeForce RTX™ 3050 Laptop GPU 4GB GDDR6\r\nUkuran Layar : 15.6″ (16:9) LED-backlit FHD (1920×1080) Anti-Glare IPS-l', '1733539242.jpg', 15000000.00, 16000000.00, 12),
(16, 11, 2, 'APPLE MACBOOK AIR MGN63ID/A M1', 'Apple M1 chip with 8‑core CPU, 7‑core GPU, and 16‑core Neural Engine\r\n8GB unified memory\r\n256GB SSD storage\r\nRetina display with True Tone\r\nBacklit Magic Keyboard – US English\r\nTouch ID\r\nForce Touch trackpad\r\nTwo Thunderbolt / USB 4 ports', '1733629683.jpg', 12800000.00, 13000000.00, 11),
(17, 11, 2, 'APPLE MACBOOK PRO 14', 'Display: Liquid Retina XDR 14,2″ (3024 x 1964) 254 Pixel / Inc\r\nProcessor: 8-core CPU with 4 performance cores and 4 efficiency cores + 10-core GPU\r\n16-core Neural Engine 100GB/s memory bandwidth\r\nMemory: 16GB unified memory\r\nHard Disk: 1TB SSD\r\nSistem Op', '1733629827.jpg', 34999000.00, 36000000.00, 9),
(18, 11, 2, 'APPLE MACBOOK AIR MRYN3ID/A', 'Display: 15.3-inch (diagonal) LED-backlit display with IPS technology; 2560-by-1664 native resolution at 224 pixels per inch with support for millions of colors, 500 Nits\r\nProcessor: M3 Chip 8-core CPU with 4 perform­ance cores and 4 efficiency cores, 10-', '1733629892.jpg', 25499000.00, 26000000.00, 11),
(20, 1, 1, 'ASUS GAMING LAPTOP ROG ZEPHYRUS', 'Processor Onboard : AMD Ryzen™ 9 6900HS Mobile Processor (8-core/16-thread, 16MB cache, up to 4.9 GHz max boost)\r\nMemori Standar : 16GB DDR5 4800Mhz\r\nTipe Grafis : NVIDIA® GeForce RTX™ 3060 Notebook GPU 6GB GDDR6\r\nTGP : ROG Boost: 1475MHz* at 120W (1425MH', '1741590893.jpg', 20000000.00, 24999000.00, 9),
(21, 5, 2, 'NOTEBOOK AXIOO MYBOOK SAGA 10', 'Processor: Intel Core i3-1220P\r\nCode Name Intel Alder Lake\r\nLCD: 16&quot; inch 2,5K Resolution IPS Display (16:10)\r\nDisplay / Resolution: WQXGA 2560×1600\r\n\r\nMemory 8GB DDR4 (2x SODIMM DDR4 Up to 64GB dual channel, Frequency Up to 3200MHz)\r\nStorage 256 GB ', '1741591363.jpg', 6500000.00, 9000000.00, 15),
(22, 2, 3, 'Lenovo IdeaPad 3 14IAU7 82RJ00CNID Notebook', '• Processor : Intel® Core™ i3-1215U\r\n• Memory : 8GB Soldered DDR4-3200\r\n• Storage : 256GB SSD M.2 2242 PCIe® 4.0x4 NVMe®\r\n• Graphics : Integrated Intel® UHD Graphics\r\n• Operating System : Windows® 11 Home + Office Home &amp; Student 2021', '1741591709.jpg', 5000000.00, 8000000.00, 14),
(23, 10, 2, 'Nitro V 16 (ANV16-41-R7W0) | AMD R7 RTX4050', 'Highlights :\r\n• Performa visualisasi, movement object yang tinggi &amp; proses editing yang cepat dengan AMD Ryzen 8000 series.\r\n• Performa grafis memukau dan pengalaman bermain game lebih real tanpa lag dengan Maximum Graphic Performance (MGP) tertinggi ', '1741592059.jpg', 15000000.00, 18999999.00, 7),
(25, 4, 3, 'RedmiBook Pro 14 MX450 Laptop Intel I5-11320H 16GB 512GB SSD Komputer Notebook Versi Global Win10 PC', 'General\r\nBrand: Redmi\r\nModel: RedmiBook Pro 14\r\nShell Material: Metal\r\nColor: Grey\r\n\r\nDisplay\r\nScreen Size: 14 Inch (16 : 10)\r\nScreen Resolution: 2560 ? 1600\r\nDisplay Technology: FHD\r\nViewing Angle: 178?\r\nBrightness: 300nit\r\nContrast Ratio: 1000 : 1\r\nColo', '1741592498.png', 10000000.00, 15000000.00, 9);

-- --------------------------------------------------------

--
-- Table structure for table `tb_custom_orders`
--

CREATE TABLE `tb_custom_orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ram` varchar(50) NOT NULL,
  `storage` varchar(100) NOT NULL,
  `processor` varchar(100) NOT NULL,
  `vga` varchar(100) NOT NULL,
  `screen_size` varchar(50) NOT NULL,
  `screen_type` varchar(100) NOT NULL,
  `operating_system` varchar(100) NOT NULL,
  `additional_specs` text DEFAULT NULL,
  `budget` int(11) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_custom_orders`
--

INSERT INTO `tb_custom_orders` (`order_id`, `user_id`, `ram`, `storage`, `processor`, `vga`, `screen_size`, `screen_type`, `operating_system`, `additional_specs`, `budget`, `status`, `admin_notes`, `admin_id`, `created_at`, `updated_at`) VALUES
(1, 14, '16GB', 'HDD 256GB', 'Intel Intel Core i5', 'NVIDIA GeForce RTX 3070', '15.6', 'LCD', 'Windows 11 Home', 'Untuk Gaming dan Kerja Kantoran', 30000000, 'completed', 'oke', 1, '2025-03-11 13:53:16', '2025-03-12 11:04:33'),
(2, 5, '32GB', 'SSD + HDD 1TB', 'AMD AMD Ryzen 7', 'AMD Radeon RX 7700', '15.6', 'OLED', 'Windows 11 Pro', 'untuk bermain anak saya ', 50000000, 'completed', 'tes', 1, '2025-03-12 14:30:10', '2025-03-12 14:45:31');

-- --------------------------------------------------------

--
-- Table structure for table `tb_detail_pembelian`
--

CREATE TABLE `tb_detail_pembelian` (
  `barang_id` int(11) NOT NULL,
  `id_pembelian` int(11) NOT NULL,
  `jumlah` int(10) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_detail_pembelian`
--

INSERT INTO `tb_detail_pembelian` (`barang_id`, `id_pembelian`, `jumlah`, `subtotal`) VALUES
(1, 1, 1, 10000000.00),
(1, 2, 2, 20000000.00),
(1, 3, 4, 40000000.00),
(1, 5, 2, 20000000.00),
(1, 8, 1, 30000000.00),
(1, 9, 1, 30000000.00),
(1, 12, 2, 60000000.00),
(1, 18, 1, 30000000.00),
(1, 23, 1, 30000000.00),
(1, 24, 1, 30000000.00),
(1, 26, 1, 30000000.00),
(1, 29, 1, 30000000.00),
(1, 30, 1, 30000000.00),
(1, 36, 1, 30000000.00),
(2, 7, 2, 20000000.00),
(2, 11, 1, 10000000.00),
(2, 18, 2, 20000000.00),
(2, 28, 1, 10000000.00),
(2, 31, 1, 10000000.00),
(2, 32, 1, 10000000.00),
(2, 41, 1, 10000000.00),
(2, 45, 1, 10000000.00),
(3, 4, 3, 36000000.00),
(3, 12, 1, 16499999.00),
(3, 15, 1, 16499999.00),
(3, 18, 1, 16499999.00),
(3, 33, 1, 16499999.00),
(4, 19, 2, 25199800.00),
(4, 20, 1, 12599900.00),
(4, 21, 1, 12599900.00),
(4, 22, 1, 12599900.00),
(4, 26, 1, 12599900.00),
(4, 35, 1, 12599900.00),
(6, 10, 1, 6000000.00),
(7, 16, 1, 10000000.00),
(7, 17, 2, 20000000.00),
(7, 26, 1, 10000000.00),
(8, 6, 1, 12000000.00),
(8, 26, 1, 12000000.00),
(8, 40, 1, 12000000.00),
(11, 10, 1, 7000000.00),
(12, 18, 1, 22000000.00),
(13, 47, 1, 22000000.00),
(14, 37, 1, 12000000.00),
(16, 42, 1, 13000000.00),
(17, 13, 1, 36000000.00),
(17, 14, 1, 36000000.00),
(17, 45, 1, 36000000.00),
(18, 43, 1, 26000000.00),
(20, 45, 1, 24999000.00),
(22, 46, 1, 8000000.00),
(25, 46, 1, 15000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_detail_penjualan`
--

CREATE TABLE `tb_detail_penjualan` (
  `penjualan_id` int(11) DEFAULT NULL,
  `barang_id` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_detail_penjualan`
--

INSERT INTO `tb_detail_penjualan` (`penjualan_id`, `barang_id`, `id`, `jumlah`, `subtotal`) VALUES
(1, 1, 1, 2, 20000000.00),
(2, 1, 2, 4, 40000000.00),
(3, 3, 3, 3, 36000000.00),
(4, 1, 4, 2, 20000000.00),
(5, 8, 5, 1, 12000000.00),
(6, 2, 6, 2, 20000000.00),
(7, 1, 7, 1, 30000000.00),
(8, 1, 8, 1, 30000000.00),
(9, 6, 9, 1, 6000000.00),
(9, 11, 10, 1, 7000000.00),
(10, 2, 11, 1, 10000000.00),
(11, 1, 12, 2, 60000000.00),
(11, 3, 13, 1, 16499999.00),
(12, 17, 14, 1, 36000000.00),
(13, 17, 15, 1, 36000000.00),
(14, 3, 16, 1, 16499999.00),
(15, 7, 17, 1, 10000000.00),
(16, 7, 18, 2, 20000000.00),
(17, 2, 19, 2, 20000000.00),
(17, 1, 20, 1, 30000000.00),
(17, 3, 21, 1, 16499999.00),
(17, 12, 22, 1, 22000000.00),
(18, 4, 23, 2, 25199800.00),
(19, 4, 24, 1, 12599900.00),
(20, 4, 25, 1, 12599900.00),
(21, 4, 26, 1, 12599900.00),
(22, 1, 27, 1, 30000000.00),
(23, 1, 28, 1, 30000000.00),
(24, 1, 29, 1, 30000000.00),
(24, 4, 30, 1, 12599900.00),
(24, 7, 31, 1, 10000000.00),
(24, 8, 32, 1, 12000000.00),
(25, 2, 33, 1, 10000000.00),
(26, 1, 34, 1, 30000000.00),
(27, 1, 35, 1, 30000000.00),
(28, 2, 36, 1, 10000000.00),
(29, 2, 37, 1, 10000000.00),
(30, 3, 38, 1, 16499999.00),
(31, 4, 39, 1, 12599900.00),
(32, 1, 40, 1, 30000000.00),
(33, 14, 41, 1, 12000000.00),
(34, 8, 42, 1, 12000000.00),
(35, 2, 43, 1, 10000000.00),
(36, 16, 44, 1, 13000000.00),
(37, 18, 45, 1, 26000000.00),
(38, 2, 46, 1, 10000000.00),
(38, 17, 47, 1, 36000000.00),
(38, 20, 48, 1, 24999000.00),
(39, 22, 49, 1, 8000000.00),
(39, 25, 50, 1, 15000000.00),
(40, 13, 51, 1, 22000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_kategori`
--

CREATE TABLE `tb_kategori` (
  `kategori_id` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kategori`
--

INSERT INTO `tb_kategori` (`kategori_id`, `nama_kategori`) VALUES
(1, 'laptop gaming'),
(2, 'Laptop Kantor'),
(3, 'Laptop Sekolah');

-- --------------------------------------------------------

--
-- Table structure for table `tb_merk`
--

CREATE TABLE `tb_merk` (
  `merk_id` int(11) NOT NULL,
  `nama_merk` varchar(255) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_merk`
--

INSERT INTO `tb_merk` (`merk_id`, `nama_merk`, `deskripsi`) VALUES
(1, 'Asus', 'merk asus'),
(2, 'Lenovo', 'lenovo anjay'),
(3, 'HP', 'HP laptop'),
(4, 'Xiaomi', 'Laptop China'),
(5, 'Axio', 'Laptop baru'),
(10, 'Acer', 'Laptop Acer'),
(11, 'Macbook', 'Apple Laptop Macbook');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pembayaran`
--

CREATE TABLE `tb_pembayaran` (
  `pembayaran_id` int(11) NOT NULL,
  `jenis_pembayaran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pembayaran`
--

INSERT INTO `tb_pembayaran` (`pembayaran_id`, `jenis_pembayaran`) VALUES
(1, 'BCA'),
(2, 'BRI'),
(3, 'BNI'),
(4, 'BTN'),
(5, 'MANDIRI'),
(6, 'BSI'),
(7, 'DANAMON'),
(8, 'CIMB NIAGA');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pembelian`
--

CREATE TABLE `tb_pembelian` (
  `id_pembelian` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pembayaran_id` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `bayar` decimal(10,2) DEFAULT NULL,
  `jumlah_pembayaran` decimal(10,2) DEFAULT NULL,
  `kembalian` decimal(10,2) DEFAULT NULL,
  `penjualan_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pembelian`
--

INSERT INTO `tb_pembelian` (`id_pembelian`, `user_id`, `pembayaran_id`, `tanggal`, `bayar`, `jumlah_pembayaran`, `kembalian`, `penjualan_id`) VALUES
(1, 5, 1, '2024-12-03', 20000000.00, 10000000.00, 10000000.00, NULL),
(2, 5, 1, '2024-12-03', 30000000.00, 20000000.00, 10000000.00, 1),
(3, 5, 1, '2024-12-03', 50000000.00, 40000000.00, 10000000.00, 2),
(4, 5, 2, '2024-12-04', 36000000.00, 36000000.00, 0.00, 3),
(5, 6, 2, '2024-12-04', 25000000.00, 20000000.00, 5000000.00, 4),
(6, 5, 1, '2024-12-04', 12500000.00, 12000000.00, 500000.00, 5),
(7, 5, 1, '2024-12-06', 23000000.00, 20000000.00, 3000000.00, 6),
(8, 5, 1, '2024-12-06', 32000000.00, 30000000.00, 2000000.00, 7),
(9, 5, 1, '2024-12-06', 31000000.00, 30000000.00, 1000000.00, 8),
(10, 5, 1, '2024-12-06', 14000000.00, 13000000.00, 1000000.00, 9),
(11, 5, 1, '2024-12-06', 11020000.00, 10000000.00, 1020000.00, 10),
(12, 5, 2, '2024-12-07', 76500000.00, 76499999.00, 1.00, 11),
(13, 5, 2, '2024-12-14', 37000000.00, 36000000.00, 1000000.00, 12),
(14, 5, 6, '2024-12-18', 39000000.00, 36000000.00, 3000000.00, 13),
(15, 5, 3, '2024-12-18', 17000000.00, 16499999.00, 500001.00, 14),
(16, 5, 4, '2024-12-18', 12000000.00, 10000000.00, 2000000.00, 15),
(17, 5, 5, '2024-12-18', 21000000.00, 20000000.00, 1000000.00, 16),
(18, 12, 4, '2024-12-18', 90000000.00, 88499999.00, 1500001.00, 17),
(19, 5, 1, '2025-03-07', 50000000.00, 25199800.00, 24800200.00, 18),
(20, 5, 3, '2025-03-07', 30000000.00, 12599900.00, 17400100.00, 19),
(21, 5, 2, '2025-03-07', 50000000.00, 12599900.00, 37400100.00, 20),
(22, 5, 1, '2025-03-07', 30000000.00, 12599900.00, 17400100.00, NULL),
(23, 5, 1, '2025-03-07', 30000000.00, 30000000.00, 0.00, 22),
(24, 5, 1, '2025-03-07', 30000000.00, 30000000.00, 0.00, NULL),
(26, 14, 3, '2025-03-10', 65000000.00, 64599900.00, 400100.00, 24),
(28, 14, 5, '2025-03-10', 12500000.00, 10000000.00, 2500000.00, 25),
(29, 14, 3, '2025-03-10', 31000000.00, 30000000.00, 1000000.00, 26),
(30, 14, 3, '2025-03-10', 32000000.00, 30000000.00, 2000000.00, 27),
(31, 14, 2, '2025-03-10', 12000000.00, 10000000.00, 2000000.00, 28),
(32, 15, 4, '2025-03-10', 50000000.00, 10000000.00, 40000000.00, 29),
(33, 15, 6, '2025-03-10', 30000000.00, 16499999.00, 13500001.00, 30),
(35, 5, 3, '2025-03-10', 36000000.00, 12599900.00, 23400100.00, 31),
(36, 14, 2, '2025-03-10', 31000000.00, 30000000.00, 1000000.00, NULL),
(37, 5, 2, '2025-03-10', 13000000.00, 12000000.00, 1000000.00, 33),
(40, 5, 3, '2025-03-10', 50000000.00, 12000000.00, 38000000.00, 34),
(41, 14, 1, '2025-03-11', 30000000.00, 10000000.00, 20000000.00, 35),
(42, 5, 1, '2025-03-11', 13000000.00, 13000000.00, 0.00, 36),
(43, 14, 5, '2025-03-11', 27000000.00, 26000000.00, 1000000.00, 37),
(45, 5, 3, '2025-03-12', 80000000.00, 70999000.00, 9001000.00, 38),
(46, 5, 4, '2025-03-12', 24000000.00, 23000000.00, 1000000.00, 39),
(47, 5, 5, '2025-03-12', 23000000.00, 22000000.00, 1000000.00, 40);

-- --------------------------------------------------------

--
-- Table structure for table `tb_penjualan`
--

CREATE TABLE `tb_penjualan` (
  `penjualan_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `bayar` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `kembalian` decimal(10,2) DEFAULT NULL,
  `id_pembelian` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_penjualan`
--

INSERT INTO `tb_penjualan` (`penjualan_id`, `admin_id`, `tanggal`, `bayar`, `total`, `kembalian`, `id_pembelian`) VALUES
(1, 1, '2024-12-03 02:18:47', 30000000.00, 20000000.00, 10000000.00, 2),
(2, 1, '2024-12-03 02:22:14', 50000000.00, 40000000.00, 10000000.00, 3),
(3, 1, '2024-12-03 17:12:11', 36000000.00, 36000000.00, 0.00, 4),
(4, 1, '2024-12-03 18:53:23', 25000000.00, 20000000.00, 5000000.00, 5),
(5, 1, '2024-12-03 20:28:33', 12500000.00, 12000000.00, 500000.00, 6),
(6, 1, '2024-12-05 20:09:22', 23000000.00, 20000000.00, 3000000.00, 7),
(7, 1, '2024-12-06 00:46:09', 32000000.00, 30000000.00, 2000000.00, 8),
(8, 1, '2024-12-06 00:56:36', 31000000.00, 30000000.00, 1000000.00, 9),
(9, 1, '2024-12-06 01:35:01', 14000000.00, 13000000.00, 1000000.00, 10),
(10, 1, '2024-12-06 06:03:57', 11020000.00, 10000000.00, 1020000.00, 11),
(11, 1, '2024-12-06 20:22:37', 76500000.00, 76499999.00, 1.00, 12),
(12, 1, '2024-12-13 18:30:44', 37000000.00, 36000000.00, 1000000.00, 13),
(13, 1, '2024-12-17 17:24:07', 39000000.00, 36000000.00, 3000000.00, 14),
(14, 1, '2024-12-17 17:40:39', 17000000.00, 16499999.00, 500001.00, 15),
(15, 1, '2024-12-17 20:26:38', 12000000.00, 10000000.00, 2000000.00, 16),
(16, 1, '2024-12-17 20:26:57', 21000000.00, 20000000.00, 1000000.00, 17),
(17, 1, '2024-12-18 04:27:39', 90000000.00, 88499999.00, 1500001.00, 18),
(18, 1, '2025-03-06 21:39:18', 50000000.00, 25199800.00, 24800200.00, 19),
(19, 1, '2025-03-06 21:40:54', 30000000.00, 12599900.00, 17400100.00, 20),
(20, 1, '2025-03-06 21:42:12', 50000000.00, 12599900.00, 37400100.00, 21),
(21, 1, '2025-03-06 21:44:41', 30000000.00, 12599900.00, 17400100.00, 20),
(22, 1, '2025-03-06 21:50:01', 30000000.00, 30000000.00, 0.00, 23),
(23, 1, '2025-03-06 22:00:56', 30000000.00, 30000000.00, 0.00, 23),
(24, 1, '2025-03-10 11:51:01', 65000000.00, 64599900.00, 400100.00, 26),
(25, 1, '2025-03-10 11:53:50', 12500000.00, 10000000.00, 2500000.00, 28),
(26, 1, '2025-03-10 12:46:28', 31000000.00, 30000000.00, 1000000.00, 29),
(27, 1, '2025-03-10 12:49:55', 32000000.00, 30000000.00, 2000000.00, 30),
(28, 1, '2025-03-10 12:53:11', 12000000.00, 10000000.00, 2000000.00, 31),
(29, 1, '2025-03-10 12:54:53', 50000000.00, 10000000.00, 40000000.00, 32),
(30, 1, '2025-03-10 12:55:05', 30000000.00, 16499999.00, 13500001.00, 33),
(31, 1, '2025-03-10 12:56:22', 36000000.00, 12599900.00, 23400100.00, 35),
(32, 1, '2025-03-10 13:05:55', 31000000.00, 30000000.00, 1000000.00, 29),
(33, 1, '2025-03-10 13:06:20', 13000000.00, 12000000.00, 1000000.00, 37),
(34, 1, '2025-03-10 13:07:22', 50000000.00, 12000000.00, 38000000.00, 40),
(35, 1, '2025-03-10 23:33:28', 30000000.00, 10000000.00, 20000000.00, 41),
(36, 1, '2025-03-11 00:03:29', 13000000.00, 13000000.00, 0.00, 42),
(37, 1, '2025-03-11 00:03:55', 27000000.00, 26000000.00, 1000000.00, 43),
(38, 1, '2025-03-12 01:44:40', 80000000.00, 70999000.00, 9001000.00, 45),
(39, 1, '2025-03-12 01:50:09', 24000000.00, 23000000.00, 1000000.00, 46),
(40, 1, '2025-03-12 05:29:44', 23000000.00, 22000000.00, 1000000.00, 47);

-- --------------------------------------------------------

--
-- Table structure for table `tb_supplier`
--

CREATE TABLE `tb_supplier` (
  `supplier_id` int(11) NOT NULL,
  `barang_id` int(11) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `telepon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_supplier`
--

INSERT INTO `tb_supplier` (`supplier_id`, `barang_id`, `nama`, `alamat`, `telepon`) VALUES
(1, 1, 'dony', 'nganjoek pusat', '0867653752671'),
(2, 2, 'dapa', 'nganjoek pusat', '087657472747'),
(3, 3, 'cimok', 'krian', '081820820808'),
(4, 16, 'zelda', 'tuban poesat of city', '08578658578'),
(5, 14, 'titi', 'Sidoarjo', '0812376484302'),
(6, 1, 'alipa', 'Taman', '081289362410'),
(7, 7, 'bilaa', 'Simo', '081976247308'),
(8, 13, 'mojo', 'tarik', '08526387649'),
(9, 1, 'abdi', 'jember', '085283651037'),
(10, 2, 'rusdi', 'ngawi city', '0854326193527'),
(11, 18, 'azril', 'ngawi barat', '089734263825'),
(12, 6, 'dhani', 'sana', '081848484545'),
(13, 7, 'dony ganteng', 'sini', '085454547565');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `user_id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` longtext DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`user_id`, `nama`, `password`, `alamat`, `telepon`, `photo`) VALUES
(1, 'mojo', '', 'sini', '0909090', NULL),
(2, 'dhani', 'akudhani', 'sini', '09090909', NULL),
(3, 'dhani', '$2a$12$QudVxwGfY7.D99OAMDFq5.m6YjVktTELHyWIlAV90wt9jbc3KBM1S', 'sini', '12122121', NULL),
(4, 'mojo', '$2y$10$cRc8EhVH0M7RLOD//h1NWe2tBgUKaaSZ2IYF8DR1d/y0jupUnMjv6', 'sini', '89898989', NULL),
(5, 'firman', '$2y$10$lGmD.SjjrNrgnLLa2R7.wuViiNbHo/M4VzyNZKnpf/gJiKpv5fkYO', 'disini senang disana senang', '085784777172', 'uploads/profile_photos/user_5_1741593794.jpg'),
(6, 'cimok', '$2y$10$dtsqNEVyAfbLnUKIcYuNJuqqvKr1yq378AbdsqA6m2Mk6fNlAmM6q', 'krian', '08998983983', NULL),
(7, 'tes1', '$2y$10$64DCMcoZtOobtCn/qjgB5ubzkeCuUjhoaXJuM7iSbaaffflFRx79G', 'sanaa', '0897798788', NULL),
(8, 'tes2', '$2y$10$YppCU4A2raE14YderKB3UOzZkIfIP2BYUahnjrEq5DqrlCKpxR.cG', 'sinisiansinaisnsain', '0823232323232', NULL),
(9, 'tes5', '$2y$10$.qLvg6cRDow6r/srtHUxCufBlEVqfCo27wEWhUoDZ6w1Azu6Kv3ay', 'sini sana', '0876276327362', NULL),
(10, 'tes4', '$2y$10$OEkSO2vAjQdFyUId3468I.FXG1mo7gVZVzvAn6QMDxuaX9i4yDR/S', 'sini', '085755654486', NULL),
(11, 'daffa', '$2y$10$I1wf6AeqwkuMePehyvGwiuO2iA.cB48VECkekFIOhjzpOUJr0.Vji', 'nganjuk pusat', '0874525698', NULL),
(12, 'coba1', '$2y$10$brdC07kRNVz39L2xQyt/DOnYEVFsO5fxl1tYMVrkJhAzFGr9T5Mga', 'sini looo', '0875485745587', NULL),
(13, 'cheysa', '$2y$10$mXi3Egqr8BxZ7/Zy7cFh8OXxuUvc1es2k68W7BUtVQUI2Lu/FF4Ei', 'simo', '08545665465', NULL),
(14, 'cimcim', '$2y$10$8Oe.VNGe/Q23AZCRzIYJQOeEjXhzOFkeHD5ibmo8iW4xd2CG5NUcm', 'akudisini', '08578477874', 'uploads/profile_photos/user_14_1741593986.png'),
(15, 'tes12', '$2y$10$adtaOspugpKriHq0YdM7qOVUe8zgib8WCKtYNVZibKifxmDOmXWHK', 'akuuu disini', '08541756964', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_wishlist`
--

CREATE TABLE `tb_wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_wishlist`
--

INSERT INTO `tb_wishlist` (`wishlist_id`, `user_id`, `barang_id`, `tanggal`) VALUES
(17, 14, 1, '2025-03-10 19:36:12'),
(18, 5, 7, '2025-03-12 12:29:26'),
(19, 5, 14, '2025-03-12 12:29:27'),
(20, 5, 15, '2025-03-12 12:29:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tb_barang`
--
ALTER TABLE `tb_barang`
  ADD PRIMARY KEY (`barang_id`),
  ADD KEY `merk_id` (`merk_id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indexes for table `tb_custom_orders`
--
ALTER TABLE `tb_custom_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `tb_detail_pembelian`
--
ALTER TABLE `tb_detail_pembelian`
  ADD PRIMARY KEY (`barang_id`,`id_pembelian`),
  ADD KEY `id_pembelian` (`id_pembelian`);

--
-- Indexes for table `tb_detail_penjualan`
--
ALTER TABLE `tb_detail_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_id` (`penjualan_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indexes for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indexes for table `tb_merk`
--
ALTER TABLE `tb_merk`
  ADD PRIMARY KEY (`merk_id`);

--
-- Indexes for table `tb_pembayaran`
--
ALTER TABLE `tb_pembayaran`
  ADD PRIMARY KEY (`pembayaran_id`);

--
-- Indexes for table `tb_pembelian`
--
ALTER TABLE `tb_pembelian`
  ADD PRIMARY KEY (`id_pembelian`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pembayaran_id` (`pembayaran_id`),
  ADD KEY `idx_pembelian_penjualan_id` (`penjualan_id`);

--
-- Indexes for table `tb_penjualan`
--
ALTER TABLE `tb_penjualan`
  ADD PRIMARY KEY (`penjualan_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `idx_penjualan_id_pembelian` (`id_pembelian`);

--
-- Indexes for table `tb_supplier`
--
ALTER TABLE `tb_supplier`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `tb_wishlist`
--
ALTER TABLE `tb_wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`barang_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_barang`
--
ALTER TABLE `tb_barang`
  MODIFY `barang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tb_custom_orders`
--
ALTER TABLE `tb_custom_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_detail_penjualan`
--
ALTER TABLE `tb_detail_penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  MODIFY `kategori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_merk`
--
ALTER TABLE `tb_merk`
  MODIFY `merk_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_pembayaran`
--
ALTER TABLE `tb_pembayaran`
  MODIFY `pembayaran_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_pembelian`
--
ALTER TABLE `tb_pembelian`
  MODIFY `id_pembelian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `tb_penjualan`
--
ALTER TABLE `tb_penjualan`
  MODIFY `penjualan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tb_supplier`
--
ALTER TABLE `tb_supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tb_wishlist`
--
ALTER TABLE `tb_wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_barang`
--
ALTER TABLE `tb_barang`
  ADD CONSTRAINT `tb_barang_ibfk_1` FOREIGN KEY (`merk_id`) REFERENCES `tb_merk` (`merk_id`),
  ADD CONSTRAINT `tb_barang_ibfk_2` FOREIGN KEY (`kategori_id`) REFERENCES `tb_kategori` (`kategori_id`);

--
-- Constraints for table `tb_detail_pembelian`
--
ALTER TABLE `tb_detail_pembelian`
  ADD CONSTRAINT `tb_detail_pembelian_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `tb_barang` (`barang_id`),
  ADD CONSTRAINT `tb_detail_pembelian_ibfk_2` FOREIGN KEY (`id_pembelian`) REFERENCES `tb_pembelian` (`id_pembelian`);

--
-- Constraints for table `tb_detail_penjualan`
--
ALTER TABLE `tb_detail_penjualan`
  ADD CONSTRAINT `tb_detail_penjualan_ibfk_1` FOREIGN KEY (`penjualan_id`) REFERENCES `tb_penjualan` (`penjualan_id`),
  ADD CONSTRAINT `tb_detail_penjualan_ibfk_2` FOREIGN KEY (`barang_id`) REFERENCES `tb_barang` (`barang_id`);

--
-- Constraints for table `tb_pembelian`
--
ALTER TABLE `tb_pembelian`
  ADD CONSTRAINT `tb_pembelian_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`user_id`),
  ADD CONSTRAINT `tb_pembelian_ibfk_2` FOREIGN KEY (`pembayaran_id`) REFERENCES `tb_pembayaran` (`pembayaran_id`);

--
-- Constraints for table `tb_penjualan`
--
ALTER TABLE `tb_penjualan`
  ADD CONSTRAINT `tb_penjualan_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `tb_admin` (`admin_id`);

--
-- Constraints for table `tb_supplier`
--
ALTER TABLE `tb_supplier`
  ADD CONSTRAINT `tb_supplier_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `tb_barang` (`barang_id`);

--
-- Constraints for table `tb_wishlist`
--
ALTER TABLE `tb_wishlist`
  ADD CONSTRAINT `tb_wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_wishlist_ibfk_2` FOREIGN KEY (`barang_id`) REFERENCES `tb_barang` (`barang_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
