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

$kategori_id = $_GET['id'];
$kategori = query("SELECT * FROM tb_kategori WHERE kategori_id = $kategori_id")[0];

// Proses edit kategori
if (isset($_POST['edit'])) {
    $nama_kategori = htmlspecialchars($_POST['nama_kategori']);
    
    // Validasi
    if (empty($nama_kategori)) {
        $error = "Nama kategori harus diisi!";
    } else {
        // Cek duplikat nama kategori, kecuali untuk kategori yang sedang diedit
        $cek = query("SELECT * FROM tb_kategori WHERE nama_kategori = '$nama_kategori' AND kategori_id != $kategori_id");
        
        if ($cek) {
            $error = "Nama kategori sudah ada!";
        } else {
            // Update data
            $data = ['nama_kategori' => $nama_kategori];
            
            if (ubah('tb_kategori', $data, "kategori_id = $kategori_id")) {
                $_SESSION['success'] = "Kategori berhasil diupdate!";
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal mengupdate kategori!";
            }
        }
    }
}

// Include header
include_once '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Kategori</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Kategori</a></li>
        <li class="breadcrumb-item active">Edit Kategori</li>
    </ol>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-pencil-square me-1"></i>
                    Form Edit Kategori
                </div>
                <div class="card-body">
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" 
                                   value="<?= $kategori['nama_kategori']; ?>" required>
                            <div class="invalid-feedback">
                                Nama kategori harus diisi!
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="edit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>