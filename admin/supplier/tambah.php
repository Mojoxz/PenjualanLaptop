<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil data barang untuk dropdown
$barang = query("SELECT * FROM tb_barang ORDER BY nama_barang ASC");

// Proses tambah supplier
if (isset($_POST['tambah'])) {
    $nama = htmlspecialchars($_POST['nama']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $telepon = htmlspecialchars($_POST['telepon']);
    $barang_id = $_POST['barang_id'];
    
    // Validasi
    $error = false;
    
    // Cek nama kosong
    if (empty($nama)) {
        $error = true;
        $error_msg = "Nama supplier harus diisi!";
    }
    
    // Bersihkan format telepon sebelum validasi
    $clean_telepon = str_replace('-', '', $telepon);
    
    // Validasi nomor telepon (10-12 digit)
    if (!preg_match("/^[0-9]{10,12}$/", $clean_telepon)) {
        $error = true;
        $error_msg = "Format nomor telepon tidak valid! Harus 10-12 digit angka.";
    }

    if (!$error) {
        $data = [
            'nama' => $nama,
            'alamat' => $alamat,
            'telepon' => $clean_telepon,
            'barang_id' => $barang_id
        ];
        
        if (tambah('tb_supplier', $data)) {
            $_SESSION['success'] = "Supplier berhasil ditambahkan!";
            header("Location: index.php");
            exit;
        } else {
            $error_msg = "Gagal menambahkan supplier!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Supplier - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --text-color: #2b2d42;
            --light-bg: #f8f9fa;
            --border-radius: 10px;
            --box-shadow: 0 0 20px rgba(0,0,0,0.08);
        }

        body {
            background-color: var(--light-bg);
            padding-top: 56px;
        }

        .navbar {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
            color: white !important;
        }

        .navbar .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: color 0.2s;
        }

        .navbar .nav-link:hover {
            color: white !important;
        }

        #sidebar {
            height: calc(100vh - 56px);
            position: fixed;
            top: 56px;
            left: 0;
            width: 250px;
            padding-top: 20px;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }

        #sidebar .nav-link {
            padding: 12px 20px;
            color: var(--text-color);
            transition: all 0.3s;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }

        #sidebar .nav-link.active {
            font-weight: 600;
        }

        #sidebar .nav-link i {
            margin-right: 10px;
        }

        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .card-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            padding: 1rem 1.25rem;
            border: none;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-color);
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 0.625rem 1rem;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
        }

        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.2);
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
        }

        .alert-danger {
            background-color: #fff5f7;
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        @media (max-width: 768px) {
            #sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .content-wrapper {
                margin-left: 0;
                padding: 15px;
            }
            
            .btn {
                width: 100%;
                margin: 0.25rem 0;
            }
            
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">Unesa Laptop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../../auth/logout.php">Sign Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<nav id="sidebar" class="col-md-3 col-lg-2">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="../index.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../barang/index.php">
                    <i class="bi bi-box"></i> Barang
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../kategori/index.php">
                    <i class="bi bi-tags"></i> Kategori
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../merk/index.php">
                    <i class="bi bi-bookmark"></i> Merk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="index.php">
                    <i class="bi bi-truck"></i> Supplier
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../penjualan/index.php">
                    <i class="bi bi-cart"></i> Penjualan
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Main content -->
<div class="content-wrapper">
    <h1 class="mt-4">Tambah Supplier</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Supplier</a></li>
        <li class="breadcrumb-item active">Tambah Supplier</li>
    </ol>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-plus-circle me-1"></i>
                    Form Tambah Supplier
                </div>
                <div class="card-body">
                    <?php if (isset($error_msg)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error_msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Supplier</label>
                            <input type="text" class="form-control" id="nama" name="nama" 
                                   value="<?= isset($_POST['nama']) ? $_POST['nama'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                Nama supplier harus diisi!
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= isset($_POST['alamat']) ? $_POST['alamat'] : ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="telepon" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon" 
                                   value="<?= isset($_POST['telepon']) ? $_POST['telepon'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                Nomor telepon harus diisi dengan format yang benar!
                            </div>
                            <small class="text-muted">Format: 081234567890 (10-12 digit)</small>
                        </div>

                        <div class="mb-3">
                            <label for="barang_id" class="form-label">Produk</label>
                            <select class="form-select" id="barang_id" name="barang_id" required>
                                <option value="">Pilih Produk</option>
                                <?php foreach ($barang as $item) : ?>
                                    <option value="<?= $item['barang_id']; ?>" 
                                            <?= (isset($_POST['barang_id']) && $_POST['barang_id'] == $item['barang_id']) ? 'selected' : ''; ?>>
                                        <?= $item['nama_barang']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Pilih produk yang disupply!
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" name="tambah" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Form validation
(function() {
    'use strict'
    
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                if (input.checkValidity()) {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                } else {
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                }
            });
        });
    });
})();

// Phone number formatter
document.getElementById('telepon').addEventListener('input', function(e) {
    // Hapus semua karakter non-digit
    let value = this.value.replace(/\D/g, '');
    
    // Batasi panjang input maksimal 12 digit
    value = value.substring(0, 12);
    
    // Update nilai input
    this.value = value;
});
</script>

</body>
</html>