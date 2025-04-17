<?php
session_start();
require_once 'config/koneksi.php';

// Ambil data laptop terbaru
$query = "SELECT b.*, k.nama_kategori, m.nama_merk 
          FROM tb_barang b 
          LEFT JOIN tb_kategori k ON b.kategori_id = k.kategori_id 
          LEFT JOIN tb_merk m ON b.merk_id = m.merk_id 
          WHERE b.stok > 0";

// Filter berdasarkan kategori
if (isset($_GET['kategori']) && $_GET['kategori'] != '') {
    $kategori_id = $_GET['kategori'];
    $query .= " AND b.kategori_id = $kategori_id";
}

// Filter berdasarkan merk
if (isset($_GET['merk']) && $_GET['merk'] != '') {
    $merk_id = $_GET['merk'];
    $query .= " AND b.merk_id = $merk_id";
}

$query .= " ORDER BY b.barang_id DESC";
$laptops = query($query);

// Ambil data kategori dan merk untuk filter
$categories = query("SELECT * FROM tb_kategori ORDER BY nama_kategori ASC");
$brands = query("SELECT * FROM tb_merk ORDER BY nama_merk ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unesa Laptop - Pusat Penjualan Laptop Terpercaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Tambahkan Animate.css untuk animasi -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Hero Slider Styles */
        .hero-section {
            position: relative;
            padding: 0;
            height: 600px;
            overflow: hidden;
        }
        
        .carousel, .carousel-inner, .carousel-item {
            height: 100%;
        }
        
        .hero-slide {
            height: 100%;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .hero-slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
        }
        
        .hero-content {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 2;
        }
        
        .carousel-indicators {
            margin-bottom: 2rem;
        }
        
        .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin: 0 8px;
            background-color: rgba(255, 255, 255, 0.5);
            border: none;
        }
        
        .carousel-indicators button.active {
            background-color: #0d6efd;
            transform: scale(1.2);
        }
        
        .carousel-control-prev, .carousel-control-next {
            width: 5%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .hero-section:hover .carousel-control-prev,
        .hero-section:hover .carousel-control-next {
            opacity: 0.8;
        }
        
        /* Efek Fade untuk Carousel */
        .carousel-fade .carousel-item {
            opacity: 0;
            transition: opacity 0.6s ease-in-out;
        }
        
        .carousel-fade .carousel-item.active {
            opacity: 1;
        }
        
        /* Animasi untuk konten */
        .animate__animated {
            animation-duration: 1s;
        }
        
        .animate__delay-1s {
            animation-delay: 0.5s;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -30px, 0);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 30px, 0);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }
        
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translate3d(-30px, 0, 0);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translate3d(30px, 0, 0);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }
        
        .animate__fadeInDown {
            animation-name: fadeInDown;
        }
        
        .animate__fadeInUp {
            animation-name: fadeInUp;
        }
        
        .animate__fadeInLeft {
            animation-name: fadeInLeft;
        }
        
        .animate__fadeInRight {
            animation-name: fadeInRight;
        }
        
        /* Responsif */
        @media (max-width: 768px) {
            .hero-section {
                height: 500px;
            }
            
            .display-4 {
                font-size: 2.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .hero-section {
                height: 450px;
            }
            
            .display-4 {
                font-size: 2rem;
            }
            
            .lead {
                font-size: 1rem;
            }
        }

        /* Features Section */
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #0d6efd;
            transition: transform 0.3s ease;
        }

        .feature-icon:hover {
            transform: scale(1.1);
        }

        /* Product Card Styles */
        .product-card {
            border: none;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }

        .product-image-container {
            position: relative;
            height: 250px;
            background: #f8f9fa;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image {
            height: 100%;
            width: 100%;
            object-fit: contain;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .stock-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 2;
        }

        .product-details {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .product-meta {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .product-description {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #495057;
            flex-grow: 1;
        }

        .price-tag {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0d6efd;
            margin: 15px 0;
        }

        /* Filter Section */
        .filter-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .filter-select {
            border-radius: 10px;
            padding: 12px;
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
        }

        /* Category Card */
        .category-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            border-radius: 15px;
            background: white;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        /* Cart Button */
        .cart-button {
            border-radius: 10px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            background: #0d6efd;
            border: none;
        }

        .cart-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13,110,253,0.2);
            background: #0b5ed7;
        }

        /* Quantity Input */
        .quantity-input {
            width: 80px !important;
            text-align: center;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }

        /* Modal Styles */
        .modal-product-image {
            max-height: 80vh;
            object-fit: contain;
        }

        /* Footer */
        .footer-link {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: #0d6efd;
        }

        .hover-link:hover {
            color: #0d6efd !important;
            transform: translateX(5px);
            transition: all 0.3s ease;
        }

        .contact-icon {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(13, 110, 253, 0.1);
            border-radius: 50%;
            color: #0d6efd;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background-color: #0d6efd;
            color: white;
            transform: translateY(-3px);
        }

        .text-primary {
            color: #0d6efd !important;
        }

        /* Ripple effect */
        .cart-button {
            position: relative;
            overflow: hidden;
        }

        .ripple {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-laptop me-2"></i>Unesa Laptop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#categories">Kategori</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['login'])) : ?>
                        <?php if ($_SESSION['role'] === 'admin') : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/index.php">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                                </a>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="user/cart.php">
                                    <i class="bi bi-cart-fill me-1"></i>Keranjang
                                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) : ?>
                                        <span class="badge bg-danger rounded-pill"><?= count($_SESSION['cart']); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="user/orders.php">
                                    <i class="bi bi-bag-check-fill me-1"></i>Pesanan
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/logout.php">
                                <i class="bi bi-box-arrow-right me-1"></i>Logout
                            </a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/register.php">
                                <i class="bi bi-person-plus-fill me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Slider (5 Slides) -->
    <section class="hero-section">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
            </div>
            
            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active" data-bs-interval="5000">
                    <div class="hero-slide" style="background-image: url('assets/img/hero-slide1.jpg');">
                        <div class="hero-content">
                            <div class="container">
                                <div class="row justify-content-center text-center">
                                    <div class="col-lg-8">
                                        <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInDown">Selamat Datang di Unesa Laptop</h1>
                                        <p class="lead mb-5 animate__animated animate__fadeInUp">Temukan laptop impian Anda dengan harga terbaik dan kualitas terjamin</p>
                                        <a href="#products" class="btn btn-primary btn-lg px-5 py-3 rounded-pill animate__animated animate__fadeInUp animate__delay-1s">
                                            Lihat Produk <i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 2 -->
                <div class="carousel-item" data-bs-interval="5000">
                    <div class="hero-slide" style="background-image: url('assets/img/hero-slide2.jpg');">
                        <div class="hero-content">
                            <div class="container">
                                <div class="row justify-content-center text-center">
                                    <div class="col-lg-8">
                                        <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInDown">Brand Terlengkap</h1>
                                        <p class="lead mb-5 animate__animated animate__fadeInUp">Pilihan laptop dari berbagai brand terkemuka dengan garansi resmi</p>
                                        <a href="#categories" class="btn btn-primary btn-lg px-5 py-3 rounded-pill animate__animated animate__fadeInUp animate__delay-1s">
                                            Lihat Merk <i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 3 -->
                <div class="carousel-item" data-bs-interval="5000">
                    <div class="hero-slide" style="background-image: url('assets/img/hero-slide3.jpg');">
                        <div class="hero-content">
                            <div class="container">
                                <div class="row justify-content-center text-center">
                                    <div class="col-lg-8">
                                        <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInLeft">Promo Spesial</h1>
                                        <p class="lead mb-5 animate__animated animate__fadeInRight">Dapatkan penawaran terbaik untuk laptop gaming, profesional, dan pelajar</p>
                                        <a href="#products" class="btn btn-danger btn-lg px-5 py-3 rounded-pill animate__animated animate__fadeInUp animate__delay-1s">
                                            Lihat Promo <i class="bi bi-tag-fill ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 4 -->
                <div class="carousel-item" data-bs-interval="5000">
                    <div class="hero-slide" style="background-image: url('assets/img/hero-slide4.jpg');">
                        <div class="hero-content">
                            <div class="container">
                                <div class="row justify-content-center text-center">
                                    <div class="col-lg-8">
                                        <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInDown">Laptop Gaming</h1>
                                        <p class="lead mb-5 animate__animated animate__fadeInUp">Performa tinggi untuk pengalaman gaming tanpa kompromi</p>
                                        <a href="?kategori=1#products" class="btn btn-success btn-lg px-5 py-3 rounded-pill animate__animated animate__fadeInUp animate__delay-1s">
                                            Explore Gaming <i class="bi bi-controller ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 5 -->
                <div class="carousel-item" data-bs-interval="5000">
                    <div class="hero-slide" style="background-image: url('assets/img/hero-slide5.jpg');">
                        <div class="hero-content">
                            <div class="container">
                                <div class="row justify-content-center text-center">
                                    <div class="col-lg-8">
                                        <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInRight">Layanan Premium</h1>
                                        <p class="lead mb-5 animate__animated animate__fadeInLeft">Konsultasi gratis dan after-sales service terbaik</p>
                                        <a href="https://wa.me/6285784777172?text=Halo,%20saya%20ingin%20konsultasi%20layanan%20premium%20Unesa%20Laptop" class="btn btn-info btn-lg px-5 py-3 rounded-pill text-white animate__animated animate__fadeInUp animate__delay-1s">
                                            Hubungi Kami <i class="bi bi-headset ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 bg-transparent text-center">
                        <div class="card-body">
                            <div class="feature-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h3 class="h4 mb-3">Produk Original</h3>
                            <p class="text-muted mb-0">Garansi resmi dan produk berkualitas dari brand terpercaya</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 bg-transparent text-center">
                        <div class="card-body">
                            <div class="feature-icon">
                                <i class="bi bi-truck"></i>
                            </div>
                            <h3 class="h4 mb-3">Pengiriman Cepat</h3>
                            <p class="text-muted mb-0">Layanan pengiriman cepat ke seluruh wilayah Indonesia</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 bg-transparent text-center">
                        <div class="card-body">
                            <div class="feature-icon">
                                <i class="bi bi-headset"></i>
                            </div>
                            <h3 class="h4 mb-3">Layanan 24/7</h3>
                            <p class="text-muted mb-0">Dukungan pelanggan siap membantu Anda setiap saat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Produk Kami</h2>
            
            <!-- Filter Section -->
            <div class="filter-section mb-4">
                <form action="" method="get" class="row g-3">
                    <div class="col-md-5">
                        <select name="kategori" class="form-select filter-select">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category['kategori_id']; ?>" 
                                    <?= (isset($_GET['kategori']) && $_GET['kategori'] == $category['kategori_id']) ? 'selected' : ''; ?>>
                                    <?= $category['nama_kategori']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <select name="merk" class="form-select filter-select">
                            <option value="">Semua Merk</option>
                            <?php foreach ($brands as $brand) : ?>
                                <option value="<?= $brand['merk_id']; ?>"
                                    <?= (isset($_GET['merk']) && $_GET['merk'] == $brand['merk_id']) ? 'selected' : ''; ?>>
                                    <?= $brand['nama_merk']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 py-3">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Products Grid -->
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($laptops as $laptop) : ?>
                    <div class="col">
                        <div class="product-card">
                            <div class="product-image-container">
                                <?php if ($laptop['stok'] <= 5) : ?>
                                    <div class="stock-badge">
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                            <i class="bi bi-exclamation-circle me-1"></i>
                                            Stok Terbatas
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($laptop['gambar'] && file_exists("assets/img/barang/" . $laptop['gambar'])) : ?>
                                    <img src="assets/img/barang/<?= $laptop['gambar']; ?>" 
                                         class="product-image" 
                                         alt="<?= htmlspecialchars($laptop['nama_barang']); ?>"
                                         onclick="showImage('<?= htmlspecialchars($laptop['nama_barang']); ?>', 
                                                          'assets/img/barang/<?= $laptop['gambar']; ?>')">
                                <?php else : ?>
                                    <img src="assets/img/no-image.jpg" 
                                         class="product-image" 
                                         alt="No Image Available">
                                <?php endif; ?>
                            </div>

                            <div class="product-details">
                                <h5 class="product-title"><?= htmlspecialchars($laptop['nama_barang']); ?></h5>
                                <div class="product-meta">
                                    <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($laptop['nama_merk']); ?> | 
                                    <i class="bi bi-laptop me-1"></i><?= htmlspecialchars($laptop['nama_kategori']); ?>
                                </div>

                                <div class="product-description">
                                    <?php
                                    $deskripsi = $laptop['jenis_barang'];
                                    $max_length = 100;
                                    
                                    if (strlen($deskripsi) > $max_length) {
                                        $short_desc = substr($deskripsi, 0, $max_length) . '...';
                                        echo '<div id="short_'.$laptop['barang_id'].'">' . htmlspecialchars($short_desc) . ' 
                                              <a href="javascript:void(0)" onclick="toggleDescription('.$laptop['barang_id'].')" 
                                                 class="text-primary text-decoration-none">Selengkapnya</a></div>';
                                        echo '<div id="full_'.$laptop['barang_id'].'" style="display:none">' 
                                             . htmlspecialchars($deskripsi) . ' <a href="javascript:void(0)" 
                                             onclick="toggleDescription('.$laptop['barang_id'].')" 
                                             class="text-primary text-decoration-none">Sembunyikan</a></div>';
                                    } else {
                                        echo htmlspecialchars($deskripsi);
                                    }
                                    ?>
                                </div>

                                <div class="mt-auto">
                                    <div class="price-tag">
                                        Rp <?= number_format($laptop['harga_jual'], 0, ',', '.'); ?>
                                    </div>

                                    <?php if (isset($_SESSION['login']) && $_SESSION['role'] === 'user') : ?>
                                        <?php if ($laptop['stok'] > 0) : ?>
                                            <form action="user/cart.php" method="post">
                                                <input type="hidden" name="barang_id" value="<?= $laptop['barang_id']; ?>">
                                                <input type="hidden" name="action" value="add">
                                                <div class="d-flex align-items-center gap-3 mb-3">
                                                    <label class="form-label mb-0">Jumlah:</label>
                                                    <input type="number" name="qty" value="1" min="1" 
                                                           max="<?= $laptop['stok']; ?>" 
                                                           class="form-control quantity-input">
                                                </div>
                                                <button type="submit" class="btn btn-primary cart-button w-100">
                                                    <i class="bi bi-cart-plus me-2"></i>Tambah ke Keranjang
                                                </button>
                                            </form>
                                        <?php else : ?>
                                            <button class="btn btn-secondary w-100" disabled>
                                                <i class="bi bi-x-circle me-2"></i>Stok Habis
                                            </button>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <a href="auth/login.php" class="btn btn-primary cart-button w-100">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Login untuk Membeli
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Kategori Laptop</h2>
            <div class="row g-4">
                <?php foreach ($categories as $category) : 
                    $kategori_id = $category['kategori_id'];
                    $jumlah_produk = query("SELECT COUNT(*) as total FROM tb_barang WHERE kategori_id = $kategori_id")[0]['total'];
                ?>
                <div class="col-md-4">
                    <div class="category-card h-100">
                        <div class="card-body text-center py-4">
                            <h3 class="h5 mb-3"><?= htmlspecialchars($category['nama_kategori']); ?></h3>
                            <p class="text-muted mb-4"><?= $jumlah_produk; ?> Produk</p>
                            <a href="?kategori=<?= $category['kategori_id']; ?>#products" 
                               class="btn btn-outline-primary rounded-pill px-4">
                                Lihat Produk <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 mb-4">
                <h5 class="mb-4 text-primary fw-bold">Tentang Unesa Laptop</h5>
                <p class="text-white opacity-75">Unesa Laptop adalah destinasi terpercaya untuk membeli laptop berkualitas dengan harga terbaik dan pelayanan yang memuaskan.</p>
            </div>
            
            <div class="col-lg-3 mb-4">
                <h5 class="mb-4 text-primary fw-bold">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <a href="#" class="text-white text-decoration-none hover-link">
                            <i class="bi bi-chevron-right me-2"></i>Home
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="#products" class="text-white text-decoration-none hover-link">
                            <i class="bi bi-chevron-right me-2"></i>Produk
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="#categories" class="text-white text-decoration-none hover-link">
                            <i class="bi bi-chevron-right me-2"></i>Kategori
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-5">
                <h5 class="mb-4 text-primary fw-bold">Kontak Kami</h5>
                <div class="contact-info">
                    <div class="d-flex align-items-center mb-3">
                        <div class="contact-icon me-3">
                            <i class="bi bi-geo-alt-fill fs-5"></i>
                        </div>
                        <div class="text-white">
                            Jl. Ketintang, Ketintang, Kec. Gayungan, Surabaya, Jawa Timur 60231
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="contact-icon me-3">
                            <i class="bi bi-telephone-fill fs-5"></i>
                        </div>
                        <div class="text-white">
                            +62 812 3456 7890
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="contact-icon me-3">
                            <i class="bi bi-envelope-fill fs-5"></i>
                        </div>
                        <div class="text-white">
                            info@unesalaptop.com
                        </div>
                    </div>

                    <div class="social-links">
                        <h6 class="text-primary mb-3">Ikuti Kami</h6>
                        <div class="d-flex gap-3">
                            <a href="#" class="social-icon">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4 opacity-25">
        
        <div class="text-center">
            <p class="mb-0 text-white opacity-75">
                &copy; <?= date('Y'); ?> Unesa Laptop. All rights reserved.
            </p>
        </div>
    </div>
</footer>

    <!-- Modal Preview Gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="imageModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="" class="img-fluid modal-product-image">
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk preview gambar
        function showImage(title, src) {
            document.getElementById('imageModalLabel').textContent = title;
            document.getElementById('modalImage').src = src;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }

        // Fungsi untuk toggle deskripsi
        function toggleDescription(id) {
            const shortDesc = document.getElementById('short_' + id);
            const fullDesc = document.getElementById('full_' + id);
            
            if (shortDesc.style.display === 'none') {
                shortDesc.style.display = 'block';
                fullDesc.style.display = 'none';
            } else {
                shortDesc.style.display = 'none';
                fullDesc.style.display = 'block';
            }
        }

        // Auto submit form ketika select berubah
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animasi untuk navbar saat scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow');
            } else {
                navbar.classList.remove('shadow');
            }
        });

        // Script untuk hero carousel
        document.addEventListener('DOMContentLoaded', function() {
            // Reset animasi saat slide berubah
            const heroCarousel = document.getElementById('heroCarousel');
            
            if (heroCarousel) {
                heroCarousel.addEventListener('slide.bs.carousel', function(event) {
                    // Mendapatkan slide berikutnya
                    const nextSlide = event.relatedTarget;
                    
                    // Reset dan trigger animasi pada slide berikutnya
                    const animations = nextSlide.querySelectorAll('.animate__animated');
                    animations.forEach(animation => {
                        // Reset animasi
                        animation.style.opacity = '0';
                        
                        // Trigger reflow
                        void animation.offsetWidth;
                        
                        // Set opacity kembali untuk memulai animasi
                        setTimeout(() => {
                            animation.style.opacity = '';
                        }, 50);
                    });
                });
                
                // Event ketika slide sudah aktif sepenuhnya
                heroCarousel.addEventListener('slid.bs.carousel', function(event) {
                    const activeSlide = event.relatedTarget;
                    const animations = activeSlide.querySelectorAll('.animate__animated');
                    
                    // Tambahkan kelas animasi yang sesuai
                    animations.forEach(animation => {
                        if (animation.classList.contains('display-4') || animation.classList.contains('fw-bold')) {
                            animation.classList.add('animate__fadeInDown');
                        } else if (animation.classList.contains('lead')) {
                            animation.classList.add('animate__fadeInUp');
                        } else if (animation.classList.contains('btn')) {
                            animation.classList.add('animate__fadeInUp');
                            animation.classList.add('animate__delay-1s');
                        }
                    });
                });
            }
            
            // Preload gambar slide untuk performa lebih baik
            const slideImages = [
                'assets/img/hero-slide1.jpg',
                'assets/img/hero-slide2.jpg',
                'assets/img/hero-slide3.jpg',
                'assets/img/hero-slide4.jpg',
                'assets/img/hero-slide5.jpg'
            ];
            
            slideImages.forEach(src => {
                const img = new Image();
                img.src = src;
            });
        });

        // Fungsi untuk animasi smooth scroll dengan easing
        function smoothScroll(target, duration) {
            const targetElement = document.querySelector(target);
            const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
            const startPosition = window.pageYOffset;
            const distance = targetPosition - startPosition;
            let startTime = null;

            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const run = ease(timeElapsed, startPosition, distance, duration);
                window.scrollTo(0, run);
                if (timeElapsed < duration) requestAnimationFrame(animation);
            }

            // Fungsi easing untuk scroll yang lebih halus
            function ease(t, b, c, d) {
                t /= d / 2;
                if (t < 1) return c / 2 * t * t + b;
                t--;
                return -c / 2 * (t * (t - 2) - 1) + b;
            }

            requestAnimationFrame(animation);
        }

        // Inisialisasi smooth scroll untuk semua link internal
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('href');
                smoothScroll(target, 1000);
            });
        });

        // Animasi navbar saat scroll
        let lastScroll = 0;
        const navbar = document.querySelector('.navbar');

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            // Tambah/hapus class berdasarkan arah scroll
            if (currentScroll > lastScroll && currentScroll > 100) {
                navbar.style.transform = 'translateY(-100%)';
                navbar.style.transition = 'transform 0.3s ease-in-out';
            } else {
                navbar.style.transform = 'translateY(0)';
                navbar.style.transition = 'transform 0.3s ease-in-out';
            }
            
            // Tambah bayangan saat scroll
            if (currentScroll > 50) {
                navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            } else {
                navbar.style.boxShadow = 'none';
            }
            
            lastScroll = currentScroll;
        });

        // Animasi fade-in untuk produk
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.product-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(card);
        });

        // Class untuk animasi fade-in
        document.head.insertAdjacentHTML('beforeend', `
            <style>
                .fade-in {
                    opacity: 1 !important;
                    transform: translateY(0) !important;
                }
            </style>
        `);

        // Animasi loading untuk gambar produk
        document.querySelectorAll('.product-image').forEach(img => {
            img.addEventListener('load', function() {
                this.style.animation = 'fadeIn 0.5s ease-in';
            });
        });

        // Tampilkan jumlah item di keranjang dengan animasi
        function updateCartBadge(count) {
            const badge = document.querySelector('.badge');
            if (badge) {
                badge.style.transform = 'scale(1.2)';
                badge.textContent = count;
                setTimeout(() => {
                    badge.style.transform = 'scale(1)';
                }, 200);
            }
        }

        // Animasi untuk tombol "Tambah ke Keranjang"
        document.querySelectorAll('.cart-button').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!this.disabled) {
                    const ripple = document.createElement('div');
                    ripple.className = 'ripple';
                    this.appendChild(ripple);
                    
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    ripple.style.width = ripple.style.height = `${size}px`;
                    
                    const x = e.clientX - rect.left - size/2;
                    const y = e.clientY - rect.top - size/2;
                    ripple.style.left = `${x}px`;
                    ripple.style.top = `${y}px`;
                    
                    setTimeout(() => ripple.remove(), 600);
                }
            });
        });

        // Animasi hover untuk kategori
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Lazy loading untuk gambar
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }

        // Tambahkan efek parallax pada hero section
        window.addEventListener('scroll', () => {
            const hero = document.querySelector('.hero-section');
            if (hero) {
                const scrolled = window.pageYOffset;
                hero.style.backgroundPositionY = `${scrolled * 0.5}px`;
            }
        });

        // Animasi loading saat filter diubah
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                document.querySelector('.row-cols-1').style.opacity = '0.5';
                document.querySelector('.row-cols-1').style.transition = 'opacity 0.3s ease';
            });
        });
    </script>
</body>
</html>