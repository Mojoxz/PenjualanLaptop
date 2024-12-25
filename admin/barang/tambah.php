<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil data kategori dan merk untuk dropdown
$kategori = query("SELECT * FROM tb_kategori ORDER BY nama_kategori ASC");
$merk = query("SELECT * FROM tb_merk ORDER BY nama_merk ASC");

// Proses tambah barang
if (isset($_POST['tambah'])) {
    $nama_barang = htmlspecialchars($_POST['nama_barang']);
    $merk_id = $_POST['merk_id'];
    $kategori_id = $_POST['kategori_id'];
    $jenis_barang = htmlspecialchars($_POST['jenis_barang']);
    $harga_beli = str_replace(['Rp', '.', ','], '', $_POST['harga_beli']);
    $harga_jual = str_replace(['Rp', '.', ','], '', $_POST['harga_jual']);
    $stok = $_POST['stok'];

    // Upload gambar
    $gambar = '';
    if ($_FILES['gambar']['error'] != UPLOAD_ERR_NO_FILE) {
        $target_dir = "../../assets/img/barang/";
        $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
        $file_name = time() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        // Validasi file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_types)) {
            $error = "Hanya file JPG, JPEG, PNG & GIF yang diizinkan!";
        } elseif ($_FILES["gambar"]["size"] > 2000000) {
            $error = "File terlalu besar! Maksimal 2MB.";
        } else {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $file_name;
            } else {
                $error = "Gagal mengupload gambar! Coba lagi.";
            }
        }
    }

    // Validasi input
    if (empty($nama_barang)) {
        $error = "Nama laptop harus diisi!";
    } elseif (empty($merk_id)) {
        $error = "Merk harus dipilih!";
    } elseif (empty($kategori_id)) {
        $error = "Kategori harus dipilih!";
    } elseif (empty($harga_beli) || $harga_beli <= 0) {
        $error = "Harga beli tidak valid!";
    } elseif (empty($harga_jual) || $harga_jual <= 0) {
        $error = "Harga jual tidak valid!";
    } elseif ($harga_jual <= $harga_beli) {
        $error = "Harga jual harus lebih besar dari harga beli!";
    } elseif (empty($stok) || $stok < 0) {
        $error = "Stok tidak valid!";
    }

    if (!isset($error)) {
        $data = [
            'nama_barang' => $nama_barang,
            'merk_id' => $merk_id,
            'kategori_id' => $kategori_id,
            'jenis_barang' => $jenis_barang,
            'harga_beli' => $harga_beli,
            'harga_jual' => $harga_jual,
            'stok' => $stok,
            'gambar' => $gambar
        ];

        if (tambah('tb_barang', $data)) {
            $_SESSION['success'] = "Data laptop berhasil ditambahkan!";
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal menambahkan data laptop!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Laptop - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .container-fluid {
            padding: 20px;
        }
        
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            border-radius: 15px;
            background: white;
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(45deg, #3b7ddd, #4e92e8);
            color: white;
            border-bottom: none;
            padding: 15px 20px;
            border-radius: 15px 15px 0 0 !important;
            font-weight: 500;
        }
        
        .card-header i {
            margin-right: 10px;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .form-label {
            font-weight: 500;
            color: #344767;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #3b7ddd;
            box-shadow: 0 0 0 0.2rem rgba(59,125,221,0.25);
        }
        
        .input-group-text {
            border-radius: 10px 0 0 10px;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-right: none;
        }
        
        .btn {
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #3b7ddd, #4e92e8);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #2d62b9, #3b7ddd);
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 20px 0;
        }
        
        .breadcrumb-item a {
            color: #3b7ddd;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .breadcrumb-item a:hover {
            color: #2d62b9;
        }
        
        .breadcrumb-item.active {
            color: #6c757d;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }
        
        .img-thumbnail {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 5px;
            transition: all 0.3s ease;
        }
        
        .form-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .invalid-feedback {
            font-size: 0.85rem;
            color: #dc3545;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .container-fluid {
                padding: 15px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .row > div {
                margin-bottom: 15px;
            }
            
            .btn {
                width: 100%;
                margin: 5px 0;
            }
            
            .d-flex.justify-content-between {
                flex-direction: column-reverse;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Tambah Laptop</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php">Laptop</a></li>
            <li class="breadcrumb-item active">Tambah Laptop</li>
        </ol>

        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-plus-circle me-1"></i>
                        Form Tambah Laptop
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_barang" class="form-label">Nama Laptop</label>
                                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" 
                                               value="<?= isset($_POST['nama_barang']) ? $_POST['nama_barang'] : ''; ?>" required>
                                        <div class="invalid-feedback">
                                            Nama laptop harus diisi!
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="merk_id" class="form-label">Merk</label>
                                        <select class="form-select" id="merk_id" name="merk_id" required>
                                            <option value="">Pilih Merk</option>
                                            <?php foreach ($merk as $m) : ?>
                                                <option value="<?= $m['merk_id']; ?>" 
                                                    <?= (isset($_POST['merk_id']) && $_POST['merk_id'] == $m['merk_id']) ? 'selected' : ''; ?>>
                                                    <?= $m['nama_merk']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Pilih merk laptop!
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="kategori_id" class="form-label">Kategori</label>
                                        <select class="form-select" id="kategori_id" name="kategori_id" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php foreach ($kategori as $k) : ?>
                                                <option value="<?= $k['kategori_id']; ?>" 
                                                    <?= (isset($_POST['kategori_id']) && $_POST['kategori_id'] == $k['kategori_id']) ? 'selected' : ''; ?>>
                                                    <?= $k['nama_kategori']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Pilih kategori laptop!
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="jenis_barang" class="form-label">Spesifikasi</label>
                                        <textarea class="form-control" id="jenis_barang" name="jenis_barang" rows="4"
                                                  required><?= isset($_POST['jenis_barang']) ? $_POST['jenis_barang'] : ''; ?></textarea>
                                        <div class="invalid-feedback">
                                            Spesifikasi laptop harus diisi!
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="harga_beli" class="form-label">Harga Beli</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control rupiah-input" id="harga_beli" 
                                                   name="harga_beli" required>
                                        </div>
                                        <div class="invalid-feedback">
                                            Harga beli harus diisi!
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="harga_jual" class="form-label">Harga Jual</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control rupiah-input" id="harga_jual" 
                                                   name="harga_jual" required>
                                        </div>
                                        <div class="invalid-feedback">
                                            Harga jual harus diisi!
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="stok" class="form-label">Stok</label>
                                        <input type="number" class="form-control" id="stok" name="stok" 
                                               value="<?= isset($_POST['stok']) ? $_POST['stok'] : ''; ?>" 
                                               min="0" required>
                                        <div class="invalid-feedback">
                                            Stok harus diisi!
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="gambar" class="form-label">Gambar Produk</label>
                                        <input type="file" class="form-control" id="gambar" name="gambar" 
                                               accept="image/*" onchange="previewImage(this)">
                                        <div class="form-text">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB</div>
                                        <div class="mt-2">
                                            <img id="preview" src="#" alt="Preview" 
                                                 class="img-thumbnail" style="max-height: 200px; display: none;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
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

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Preview Image
    function previewImage(input) {
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }

    // Format Rupiah
    document.querySelectorAll('.rupiah-input').forEach(function(input) {
        input.addEventListener('keyup', function(e) {
            let value = this.value.replace(/[^0-9]/g, '');
            this.value = formatRupiah(value);
        });

        // Set initial value if exists
        if (input.value) {
            input.value = formatRupiah(input.value.replace(/[^0-9]/g, ''));
        }
    });

    function formatRupiah(angka) {
        let number_string = angka.toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    // Form Validation
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
    </script>
</body>
</html>